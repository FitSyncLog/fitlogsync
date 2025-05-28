<?php
session_start();
require "db_con.php";

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['newTransaction'])) {
    // Add error logging
    error_log("Form submitted with data: " . print_r($_POST, true));
    
    // Get form data
    $user_id = $_POST['member_id'];
    $plan_id = $_POST['selected_plan_id'];
    $starting_date = $_POST['start_date'];
    $coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : 'regular';

    // Validate required fields
    if (empty($user_id) || empty($plan_id) || empty($starting_date)) {
        error_log("Missing required fields: user_id=$user_id, plan_id=$plan_id, starting_date=$starting_date");
        header("Location: ../create-transaction.php?Failed=Please fill in all required fields");
        exit();
    }

    // Double-check subscription overlap
    $check_query = "SELECT * FROM subscriptions 
                   WHERE user_id = ? 
                   AND (
                       (starting_date <= ? AND expiration_date > ?) OR
                       (? < expiration_date AND ? >= starting_date)
                   )";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("issss", $user_id, $starting_date, $starting_date, $starting_date, $starting_date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $existing_sub = $check_result->fetch_assoc();
        header("Location: ../create-transaction.php?Failed=Active subscription found for the selected period. Please select a date after " . date('F d, Y', strtotime($existing_sub['expiration_date'])));
        exit();
    }

    // Get plan details
    $plan_query = "SELECT * FROM plans WHERE plan_id = ?";
    $plan_stmt = $conn->prepare($plan_query);
    $plan_stmt->bind_param("i", $plan_id);
    $plan_stmt->execute();
    $plan_result = $plan_stmt->get_result();
    
    if ($plan_result->num_rows === 0) {
        error_log("Plan not found for plan_id: $plan_id");
        header("Location: ../create-transaction.php?Failed=Selected plan not found");
        exit();
    }
    
    $plan = $plan_result->fetch_assoc();

    // Set plan details
    $plan_name_at_transaction = $plan['plan_name'];
    $plan_description_at_transaction = $plan['description'];
    $plan_price_at_transaction = $plan['price'];
    $plan_duration_at_transaction = $plan['duration'];

    // Calculate expiration date
    $expiration_date = date('Y-m-d', strtotime($starting_date . ' + ' . $plan_duration_at_transaction . ' months'));
    $duration = $plan_duration_at_transaction;

    // Calculate subtotal first as it's needed for discount and coupon calculations
    $subtotal = $plan_price_at_transaction * $duration;
    $amount_after_discount = $subtotal;

    // Get discount details based on category
    if ($category === 'regular') {
        // Set default values for regular category
        $discount_id = null;
        $discount_name_at_transaction = 'Regular';
        $discount_type_at_transaction = 'none';
        $discount_value_at_transaction = 0;
        $discount_total_at_transaction = 0;
    } else {
        // Get discount details from the discounts table
        $discount_query = "SELECT * FROM discounts WHERE discount_id = ? AND status = 1";
        $discount_stmt = $conn->prepare($discount_query);
        $discount_stmt->bind_param("i", $category);
        $discount_stmt->execute();
        $discount_result = $discount_stmt->get_result();
        $discount = $discount_result->fetch_assoc();

        $discount_id = $discount['discount_id'];
        $discount_name_at_transaction = $discount['discount_name'];
        $discount_type_at_transaction = $discount['discount_type'];
        $discount_value_at_transaction = $discount['discount_value'];

        // Calculate discount total based on the Plan Quotation
        if ($discount_type_at_transaction === 'percentage') {
            $discount_total_at_transaction = $subtotal * ($discount_value_at_transaction / 100);
        } else {
            $discount_total_at_transaction = $discount_value_at_transaction;
        }
        
        // Calculate amount after discount for sequential discount calculation
        $amount_after_discount = $subtotal - $discount_total_at_transaction;
    }

    // Get coupon details if provided
    if (!empty($coupon_code)) {
        // First get the coupon_id from coupon_codes table
        $coupon_id_query = "SELECT coupon_id FROM coupon_codes WHERE coupon_code = ?";
        $coupon_id_stmt = $conn->prepare($coupon_id_query);
        $coupon_id_stmt->bind_param("s", $coupon_code);
        $coupon_id_stmt->execute();
        $coupon_id_result = $coupon_id_stmt->get_result();
        
        if ($coupon_id_result->num_rows > 0) {
            $coupon_id_row = $coupon_id_result->fetch_assoc();
            $coupon_id = $coupon_id_row['coupon_id'];

            // Now get the coupon information from coupons table
            $coupon_info_query = "SELECT * FROM coupons WHERE coupon_id = ?";
            $coupon_info_stmt = $conn->prepare($coupon_info_query);
            $coupon_info_stmt->bind_param("i", $coupon_id);
            $coupon_info_stmt->execute();
            $coupon_info_result = $coupon_info_stmt->get_result();

            if ($coupon_info_result->num_rows > 0) {
                $coupon_information = $coupon_info_result->fetch_assoc();
                
                // Set coupon details
                $coupon_name_at_transaction = $coupon_information['coupon_name'];
                $coupon_code_at_transaction = $coupon_code; // Use the actual entered code
                $coupon_type_at_transaction = $coupon_information['coupon_type'];
                $coupon_value_at_transaction = $coupon_information['coupon_value'];

                // Calculate coupon total based on the amount after discount
                if ($coupon_type_at_transaction === 'percentage') {
                    $coupon_total_at_transaction = $amount_after_discount * ($coupon_value_at_transaction / 100);
                } else {
                    $coupon_total_at_transaction = $coupon_value_at_transaction;
                }

            } else {
                // Set default values if coupon information not found
                $coupon_id = null;
                $coupon_name_at_transaction = 'No Coupon';
                $coupon_code_at_transaction = '';
                $coupon_type_at_transaction = 'none';
                $coupon_value_at_transaction = 0;
                $coupon_total_at_transaction = 0;
            }
        } else {
            // Set default values if coupon code not found
            $coupon_id = null;
            $coupon_name_at_transaction = 'No Coupon';
            $coupon_code_at_transaction = '';
            $coupon_type_at_transaction = 'none';
            $coupon_value_at_transaction = 0;
            $coupon_total_at_transaction = 0;
        }
    } else {
        // Set default values if no coupon provided
        $coupon_id = null;
        $coupon_name_at_transaction = 'No Coupon';
        $coupon_code_at_transaction = '';
        $coupon_type_at_transaction = 'none';
        $coupon_value_at_transaction = 0;
        $coupon_total_at_transaction = 0;
    }

    // Calculate grand total using sequential discounts
    $total_discount = $discount_total_at_transaction + $coupon_total_at_transaction;
    $grand_total = $subtotal - $total_discount;

    // Set other transaction details with Manila timezone
    $transaction_date_time = date('Y-m-d H:i:s');
    $payment_method = 'cash';
    $transact_by = $_SESSION['user_id'];

    // Generate acknowledgement receipt number (YYYYMMDD####) using Manila timezone
    $date_part = date('Ymd');
    
    // Get the latest receipt number for today
    $check_receipt_query = "SELECT acknowledgement_receipt_number FROM payment_transactions 
                          WHERE acknowledgement_receipt_number LIKE ? 
                          ORDER BY acknowledgement_receipt_number DESC LIMIT 1";
    $check_receipt_stmt = $conn->prepare($check_receipt_query);
    $pattern = $date_part . '%';
    $check_receipt_stmt->bind_param("s", $pattern);
    $check_receipt_stmt->execute();
    $result = $check_receipt_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $last_receipt = $result->fetch_assoc()['acknowledgement_receipt_number'];
        $number_part = intval(substr($last_receipt, -4));
        $new_number = str_pad($number_part + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $new_number = '0001';
    }
    
    $acknowledgement_receipt_number = $date_part . $new_number;

    // Get full name of person who made the transaction
    $full_name_transact_query = "SELECT lastname, firstname FROM users WHERE user_id = ?";
    $full_name_transact_stmt = $conn->prepare($full_name_transact_query);
    $full_name_transact_stmt->bind_param("i", $transact_by);
    $full_name_transact_stmt->execute();
    $full_name_transact = $full_name_transact_stmt->get_result()->fetch_assoc();
    $transact_by_full_name = $full_name_transact['lastname'] . ", " . $full_name_transact['firstname'];

    // Get full name of member
    $full_name_member_query = "SELECT lastname, firstname FROM users WHERE user_id = ?";
    $full_name_member_stmt = $conn->prepare($full_name_member_query);
    $full_name_member_stmt->bind_param("i", $user_id);
    $full_name_member_stmt->execute();
    $full_name_member = $full_name_member_stmt->get_result()->fetch_assoc();
    $member_full_name = $full_name_member['lastname'] . ", " . $full_name_member['firstname'];

    // Update coupon status if a coupon was used
    if (!empty($coupon_code) && $coupon_id !== null) {
        $update_coupon_query = "UPDATE coupon_codes SET status = 'Used', used_by = ?, date_time_used = NOW() WHERE coupon_code = ?";
        $update_coupon_stmt = $conn->prepare($update_coupon_query);
        $update_coupon_stmt->bind_param("is", $user_id, $coupon_code);
        $update_coupon_stmt->execute();
    }

    // Insert into payment_transactions table
    $insert_payment_query = "INSERT INTO payment_transactions (
        acknowledgement_receipt_number, user_id, plan_id, plan_name_at_transaction, 
        plan_description_at_transaction, plan_price_at_transaction, plan_duration_at_transaction,
        coupon_id, discount_id, grand_total, transaction_date_time, payment_method, 
        transact_by
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";

    // Set coupon_id to null if not provided
    if ($coupon_id === null) {
        $coupon_id = null;
    }

    // Set discount_id to null if it's 0
    if ($discount_id === 0) {
        $discount_id = null;
    }

    $stmt = $conn->prepare($insert_payment_query);
    $stmt->bind_param(
        "siissidiidssi",
        $acknowledgement_receipt_number, $user_id, $plan_id, $plan_name_at_transaction,
        $plan_description_at_transaction, $plan_price_at_transaction, $plan_duration_at_transaction,
        $coupon_id, $discount_id, $grand_total, $transaction_date_time, $payment_method,
        $transact_by
    );


    if ($stmt->execute()) {
        // Get the payment_transaction_id of the newly inserted payment transaction
        $payment_transaction_id = $conn->insert_id;

        // Insert into subscriptions table
        $insert_subscription_query = "INSERT INTO subscriptions (user_id, starting_date, expiration_date, payment_transaction_id) VALUES (?, ?, ?, ?)";
        $subscription_stmt = $conn->prepare($insert_subscription_query);
        $subscription_stmt->bind_param("issi", $user_id, $starting_date, $expiration_date, $payment_transaction_id);

        if ($subscription_stmt->execute()) {
            // Generate PDF receipt
            require_once(__DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php');

            // Create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator('FiT-LOGSYNC');
            $pdf->SetAuthor('FiT-LOGSYNC');
            $pdf->SetTitle('Acknowledgement Receipt');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set margins
            $pdf->SetMargins(15, 15, 15);

            // Add a page
            $pdf->AddPage();

            // Get business information
            $info_query = "SELECT information_for, description FROM information WHERE information_for IN ('address', 'phone_number', 'email')";
            $info_result = $conn->query($info_query);
            $business_info = [];
            while ($info_row = $info_result->fetch_assoc()) {
                $business_info[$info_row['information_for']] = $info_row['description'];
            }

            // Add logo
            $logo_path = dirname(__FILE__) . '/../assets/fitlogsync1.png';
            if (file_exists($logo_path)) {
                $pdf->Image($logo_path, 75, 10, 60, 0, 'PNG');
            }

            // Business Information
            $pdf->SetY(25);
            $pdf->SetFont('nunito', 'B', 16);
            $pdf->Cell(0, 10, 'Fit-LOGSYNC', 0, 1, 'C');
            $pdf->SetFont('nunito', '', 10);
            $pdf->Cell(0, 5, $business_info['address'] . ' | ' . $business_info['phone_number'] . ' | ' . $business_info['email'], 0, 1, 'C');

            // Add horizontal line
            $pdf->Line(15, $pdf->GetY() + 5, 195, $pdf->GetY() + 5);
            $pdf->Ln(10);

            // Receipt Details
            $pdf->SetFont('nunito', 'B', 11);
            $pdf->Cell(95, 5, 'Acknowledgement Receipt No.: ' . $acknowledgement_receipt_number, 0, 0);
            $pdf->Cell(95, 5, 'Processed By: ' . $transact_by_full_name, 0, 1, 'R');
            
            $pdf->SetFont('nunito', '', 11);
            $pdf->Cell(95, 5, 'Date: ' . date('F d, Y', strtotime($transaction_date_time)), 0, 0);
            $pdf->Cell(95, 5, 'Payment Method: ' . ucfirst($payment_method), 0, 1, 'R');
            
            $pdf->Cell(95, 5, 'Time: ' . date('g:i A', strtotime($transaction_date_time)), 0, 1);
            $pdf->Ln(5);

            // Member Information
            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetFont('nunito', 'B', 11);
            $pdf->Cell(0, 7, 'Member Information', 0, 1, '', true);
            $pdf->SetFont('nunito', '', 11);
            $pdf->Cell(0, 7, 'Name: ' . $member_full_name, 0, 1);

            // Get account number from users table
            $account_query = "SELECT account_number FROM users WHERE user_id = ?";
            $account_stmt = $conn->prepare($account_query);
            $account_stmt->bind_param("i", $user_id);
            $account_stmt->execute();
            $account_result = $account_stmt->get_result();
            $account_data = $account_result->fetch_assoc();
            $account_number = $account_data['account_number'];
            
            // Format account number with spaces every 4 digits
            $formatted_account = trim(chunk_split($account_number, 4, ' '));
            $pdf->Cell(0, 7, 'Account Number: ' . $formatted_account, 0, 1);
            $pdf->Ln(5);

            // Subscription Details
            $pdf->SetFont('nunito', 'B', 11);
            $pdf->Cell(0, 7, 'Subscription Details', 0, 1);
            
            // Table Header
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell(140, 7, 'Description', 1, 0, '', true);
            $pdf->Cell(40, 7, 'Amount', 1, 1, 'R', true);

            // Plan Details
            $pdf->SetFont('nunito', '', 11);
            $pdf->SetTextColor(0, 0, 0); // Reset to black color
            
            // Calculate the full description with subscription period
            $description = $plan_name_at_transaction . "\n" . 
                         $plan_description_at_transaction . "\n" . 
                         "P" . number_format($plan_price_at_transaction, 2) . " / month X " . 
                         $plan_duration_at_transaction . " month(s)\n";

            // Start the cell
            $pdf->MultiCell(140, 8, $description, 'LTR', 'L');
            
            // Add subscription period in yellow
            $startY = $pdf->GetY();
            $pdf->SetTextColor(246, 194, 62); // Set color to #F6C23E
            $pdf->MultiCell(140, 8, "Subscription Period:\n" . 
                          date('F d, Y', strtotime($starting_date)) . " - " . 
                          date('F d, Y', strtotime($expiration_date)), 'LRB', 'L');
            
            // Calculate total height
            $endY = $pdf->GetY();
            $height = $endY - $startY + 24; // Adjust this value if needed
            
            // Add the amount cell
            $pdf->SetTextColor(0, 0, 0); // Reset to black
            $pdf->SetXY($pdf->GetX() + 140, $endY - $height);
            $pdf->Cell(40, $height, 'P' . number_format($subtotal, 2), 1, 1, 'R');

            // Discount if applicable
            if ($discount_id) {
                $discount_desc = $discount_name_at_transaction . " Discount\n" .
                               ($discount_type_at_transaction === 'percentage' ? 
                               $discount_value_at_transaction . '%' : 
                               'P' . number_format($discount_value_at_transaction, 2));
                
                $pdf->MultiCell(140, 10, $discount_desc, 1, 'L');
                $pdf->SetXY($pdf->GetX() + 140, $pdf->GetY() - 10);
                $pdf->SetTextColor(255, 0, 0);
                $pdf->Cell(40, 10, '-P' . number_format($discount_total_at_transaction, 2), 1, 1, 'R');
                $pdf->SetTextColor(0, 0, 0);
            }

            // Coupon if applicable
            if ($coupon_id) {
                $coupon_desc = "Coupon Discount: " . 
                              ($coupon_type_at_transaction === 'percentage' ? 
                              $coupon_value_at_transaction . '%' : 
                              'P' . number_format($coupon_value_at_transaction, 2)) . "\n" .
                              "Event Name: " . $coupon_name_at_transaction;
                
                $pdf->MultiCell(140, 10, $coupon_desc, 1, 'L');
                $pdf->SetXY($pdf->GetX() + 140, $pdf->GetY() - 10);
                $pdf->SetTextColor(255, 0, 0);
                $pdf->Cell(40, 10, '-P' . number_format($coupon_total_at_transaction, 2), 1, 1, 'R');
                $pdf->SetTextColor(0, 0, 0);
            }

            // Total
            $pdf->SetFont('nunito', 'B', 11);
            $pdf->Cell(140, 7, 'TOTAL', 1, 0, 'R');
            $pdf->Cell(40, 7, 'P' . number_format($grand_total, 2), 1, 1, 'R');

            // Payment Notes
            $pdf->Ln(5);
            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetFont('nunito', 'B', 11);
            $pdf->Cell(0, 7, 'Payment Notes', 0, 1, '', true);
            $pdf->SetFont('nunito', '', 10);
            $pdf->Cell(0, 5, 'Thank you for your payment. This receipt serves as your official record of payment.', 0, 1);
            $pdf->Cell(0, 5, 'Please present this receipt for any inquiries regarding your membership.', 0, 1);

            // Signature Line
            $pdf->Ln(15);
            $pdf->SetFont('nunito', '', 11);
            $pdf->Cell(0, 0, '-----------------------------------', 0, 1, 'C');
            $pdf->Cell(0, 7, 'Authorized Signature', 0, 1, 'C');
            $pdf->SetFont('nunito', '', 8);
            $pdf->Cell(0, 5, 'This is a computer-generated receipt. No signature required.', 0, 1, 'C');

            // Create directory if it doesn't exist
            $receipt_dir = dirname(__FILE__) . '/../acknowledgement-receipt';
            if (!file_exists($receipt_dir)) {
                mkdir($receipt_dir, 0777, true);
            }

            // Save PDF
            $pdf_filename = 'FiT-LOGSYNC Acknowledgement Receipt (' . $acknowledgement_receipt_number . ').pdf';
            $pdf->Output($receipt_dir . '/' . $pdf_filename, 'F');

            // Send email with receipt
            // Get user's email
            $email_query = "SELECT email FROM users WHERE user_id = ?";
            $email_stmt = $conn->prepare($email_query);
            $email_stmt->bind_param("i", $user_id);
            $email_stmt->execute();
            $email_result = $email_stmt->get_result();
            $email_data = $email_result->fetch_assoc();
            $user_email = $email_data['email'];

            function sendMail($email, $acknowledgement_receipt_number, $pdf_path, $member_full_name, $plan_name_at_transaction, 
                            $plan_description_at_transaction, $plan_price_at_transaction, $plan_duration_at_transaction, 
                            $discount_name_at_transaction, $discount_type_at_transaction, $discount_value_at_transaction, 
                            $coupon_name_at_transaction, $coupon_type_at_transaction, $coupon_value_at_transaction,
                            $subtotal, $discount_total_at_transaction, $coupon_total_at_transaction, $grand_total,
                            $transaction_date_time, $payment_method, $transact_by_full_name, $account_number, $business_info)
            {
                require("PHPMailer/PHPMailer.php");
                require("PHPMailer/SMTP.php");
                require("PHPMailer/Exception.php");

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'fitlogsync.official@gmail.com';
                    $mail->Password = 'tjen yako tlcc knwi';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('fitlogsync.official@gmail.com', 'FiT-LOGSYNC');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = "FiT-LOGSYNC Acknowledgement Receipt ($acknowledgement_receipt_number) | FiT-LOGSYNC";

                    // Attach PDF with proper filename
                    $mail->addAttachment($pdf_path, basename($pdf_path));

                    $logo = "https://rmmccomsoc.org/fitlogsync1.png";

                    // Format the date and time using Manila timezone
                    $formatted_date = date('F d, Y', strtotime($transaction_date_time));
                    $formatted_time = date('g:i A', strtotime($transaction_date_time));

                    // Format account number
                    $formatted_account = trim(chunk_split($account_number, 4, ' '));

                    // Format business contact info
                    $business_contact = $business_info['address'];
                    if (!empty($business_info['phone_number'])) {
                        $business_contact .= ' | ' . $business_info['phone_number'];
                    }
                    if (!empty($business_info['email'])) {
                        $business_contact .= ' | ' . $business_info['email'];
                    }

                    $mail->Body = "
                    <html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; }
                                .receipt-container { max-width: 800px; margin: 0 auto; padding: 20px; }
                                .header { text-align: center; margin-bottom: 20px; }
                                .logo { height: 100px; }
                                .details-section { background-color: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; }
                                .table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                                .table th, .table td { padding: 8px; border: 1px solid #dee2e6; }
                                .table th { background-color: #f8f9fa; }
                                .text-right { text-align: right; }
                                .text-danger { color: #dc3545; }
                                .subscription-period { color: #F6C23E; }
                            </style>
                        </head>
                        <body>
                            <div class='receipt-container'>
                                <div class='header'>
                                    <img src='$logo' alt='FiT-LOGSYNC Logo' class='logo'>
                                    <h2>FiT-LOGSYNC</h2>
                                    <p>$business_contact</p>
                                </div>

                                <div class='details-section'>
                                    <p><strong>Acknowledgement Receipt No.:</strong> $acknowledgement_receipt_number</p>
                                    <p><strong>Date:</strong> $formatted_date</p>
                                    <p><strong>Time:</strong> $formatted_time</p>
                                    <p><strong>Processed By:</strong> $transact_by_full_name</p>
                                    <p><strong>Payment Method:</strong> " . ucfirst($payment_method) . "</p>
                                </div>

                                <div class='details-section'>
                                    <h4>Member Information</h4>
                                    <p><strong>Name:</strong> $member_full_name</p>
                                    <p><strong>Account Number:</strong> $formatted_account</p>
                                </div>

                                <h4>Subscription Details</h4>
                                <table class='table'>
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th class='text-right'>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>$plan_name_at_transaction</strong><br>
                                                $plan_description_at_transaction<br>
                                                P" . number_format($plan_price_at_transaction, 2) . " / month X $plan_duration_at_transaction month(s)<br>
                                                <div class='subscription-period'>
                                                    <strong>Subscription Period:</strong><br>
                                                    " . date('F d, Y', strtotime($starting_date)) . " - " . date('F d, Y', strtotime($expiration_date)) . "
                                                </div>
                                            </td>
                                            <td class='text-right'>P" . number_format($subtotal, 2) . "</td>
                                        </tr>";

                    // Add discount if applicable
                    if ($discount_total_at_transaction > 0) {
                        $mail->Body .= "
                        <tr>
                            <td>
                                <strong>$discount_name_at_transaction Discount</strong><br>
                                <small>" . ($discount_type_at_transaction === 'percentage' ? 
                                    $discount_value_at_transaction . '%' : 
                                    'P' . number_format($discount_value_at_transaction, 2)) . "</small>
                            </td>
                            <td class='text-right text-danger'>-P" . number_format($discount_total_at_transaction, 2) . "</td>
                        </tr>";
                    }

                    // Add coupon if applicable
                    if ($coupon_total_at_transaction > 0) {
                        $mail->Body .= "
                        <tr>
                            <td>
                                <strong>Coupon Discount:</strong> " . 
                                ($coupon_type_at_transaction === 'percentage' ? 
                                    $coupon_value_at_transaction . '%' : 
                                    'P' . number_format($coupon_value_at_transaction, 2)) . "<br>
                                <small><strong>Event Name:</strong> $coupon_name_at_transaction</small>
                            </td>
                            <td class='text-right text-danger'>-P" . number_format($coupon_total_at_transaction, 2) . "</td>
                        </tr>";
                    }

                    $mail->Body .= "
                                        <tr>
                                            <td class='text-right'><strong>TOTAL</strong></td>
                                            <td class='text-right'><strong>P" . number_format($grand_total, 2) . "</strong></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class='details-section'>
                                    <h4>Payment Notes</h4>
                                    <p>Thank you for your payment. This receipt serves as your official record of payment.</p>
                                    <p>Please present this receipt for any inquiries regarding your membership.</p>
                                </div>

                                <div style='text-align: center; margin-top: 30px;'>
                                    <p>-----------------------------------</p>
                                    <p>Authorized Signature</p>
                                    <p><small>This is a computer-generated receipt. No signature required.</small></p>
                                </div>
                            </div>
                        </body>
                    </html>";

                    $mail->send();
                    return true;
                } catch (Exception $e) {
                    return false;
                }
            }

            // Send the email
            $mail_sent = sendMail($user_email, $acknowledgement_receipt_number, $receipt_dir . '/' . $pdf_filename,
                $member_full_name, $plan_name_at_transaction, $plan_description_at_transaction,
                $plan_price_at_transaction, $plan_duration_at_transaction,
                $discount_name_at_transaction, $discount_type_at_transaction, $discount_value_at_transaction,
                $coupon_name_at_transaction, $coupon_type_at_transaction, $coupon_value_at_transaction,
                $subtotal, $discount_total_at_transaction, $coupon_total_at_transaction, $grand_total,
                $transaction_date_time, $payment_method, $transact_by_full_name, $account_number, $business_info);

            if ($mail_sent) {
                header("Location: ../manage-payments.php?Success=Transaction created and receipt sent successfully");
            } else {
                header("Location: ../manage-payments.php?Success=Transaction created but failed to send receipt");
            }
            exit();
        } else {
            // If subscription insert fails, we should rollback the payment transaction
            $delete_transaction = "DELETE FROM payment_transactions WHERE payment_transaction_id = ?";
            $delete_stmt = $conn->prepare($delete_transaction);
            $delete_stmt->bind_param("i", $payment_transaction_id);
            $delete_stmt->execute();

            header("Location: ../create-transaction.php?Failed=Error creating subscription");
            exit();
        }
    } else {
        header("Location: ../create-transaction.php?Failed=Error creating transaction");
        exit();
    }
} else {
    header("Location: ../create-transaction.php");
    exit();
}
?>