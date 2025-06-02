<?php
// Turn off output buffering completely
ob_end_clean();

require_once 'db_con.php';
session_start();

if (!isset($_SESSION['login'])) {
    die('Unauthorized access');
}

// Set timezone to Manila/Asia
date_default_timezone_set('Asia/Manila');

// Required for PDF generation
require_once '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get and validate input
$report_type = $_POST['report_type'] ?? '';
$date_range = $_POST['date_range'] ?? '';

if (empty($report_type) || empty($date_range)) {
    die('Missing required parameters');
}

try {
    // Parse date range
    $dates = explode(' - ', $date_range);
    $start_date = date('Y-m-d', strtotime($dates[0]));
    $end_date = date('Y-m-d', strtotime($dates[1]));

    // Get report data based on type
    function getReportData($conn, $report_type, $start_date, $end_date) {
        $data = [];
        
        switch ($report_type) {
            case 'sales':
                $query = "SELECT 
                            pt.transaction_date_time as 'Date & Time',
                            pt.acknowledgement_receipt_number as 'AR Number',
                            CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) as 'Member Name',
                            pt.plan_duration_at_transaction as 'Duration (Months)',
                            pt.plan_name_at_transaction as 'Plan',
                            pt.plan_price_at_transaction as 'Monthly Rate',
                            pt.grand_total as 'Total Amount',
                            CONCAT(staff.firstname, ' ', staff.lastname) as 'Transact By'
                         FROM payment_transactions pt
                         LEFT JOIN users u ON pt.user_id = u.user_id
                         LEFT JOIN users staff ON pt.transact_by = staff.user_id
                         WHERE DATE(pt.transaction_date_time) BETWEEN ? AND ?
                         ORDER BY pt.transaction_date_time DESC";
                break;

            case 'coupons':
                $query = "SELECT 
                            pt.transaction_date_time as 'Date & Time',
                            pt.acknowledgement_receipt_number as 'AR Number',
                            CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) as 'Member Name',
                            cc.coupon_code as 'Coupon Code',
                            c.coupon_name as 'Coupon Name',
                            pt.plan_duration_at_transaction as 'Duration (Months)',
                            pt.plan_name_at_transaction as 'Plan',
                            pt.plan_price_at_transaction as 'Monthly Rate',
                            CASE 
                                WHEN c.coupon_type = 'percentage' 
                                THEN CONCAT(c.coupon_value, '%')
                                ELSE CONCAT('Php ', c.coupon_value)
                            END as 'Discount Value',
                            CASE 
                                WHEN c.coupon_type = 'percentage' 
                                THEN (pt.plan_price_at_transaction * pt.plan_duration_at_transaction * (c.coupon_value/100))
                                ELSE c.coupon_value 
                            END as 'Total Discount',
                            pt.grand_total as 'Final Amount',
                            CONCAT(staff.firstname, ' ', staff.lastname) as 'Transact By'
                         FROM payment_transactions pt
                         JOIN users u ON pt.user_id = u.user_id
                         JOIN coupons c ON pt.coupon_id = c.coupon_id
                         JOIN coupon_codes cc ON cc.coupon_id = c.coupon_id AND cc.used_by = u.user_id AND cc.status = 'Used'
                         LEFT JOIN users staff ON pt.transact_by = staff.user_id
                         WHERE DATE(pt.transaction_date_time) BETWEEN ? AND ?
                         ORDER BY pt.transaction_date_time DESC";
                break;

            case 'discounts':
                $query = "SELECT 
                            pt.transaction_date_time as 'Date & Time',
                            pt.acknowledgement_receipt_number as 'AR Number',
                            CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) as 'Member Name',
                            pt.plan_duration_at_transaction as 'Duration (Months)',
                            pt.plan_name_at_transaction as 'Plan',
                            pt.plan_price_at_transaction as 'Monthly Rate',
                            CONCAT(d.discount_value, '%') as 'Discount Value',
                            (pt.plan_price_at_transaction * pt.plan_duration_at_transaction * (d.discount_value/100)) as 'Total Discount',
                            pt.grand_total as 'Final Amount',
                            CONCAT(staff.firstname, ' ', staff.lastname) as 'Transact By'
                         FROM payment_transactions pt
                         JOIN users u ON pt.user_id = u.user_id
                         JOIN discounts d ON pt.discount_id = d.discount_id
                         LEFT JOIN users staff ON pt.transact_by = staff.user_id
                         WHERE DATE(pt.transaction_date_time) BETWEEN ? AND ?
                         ORDER BY pt.transaction_date_time DESC";
                break;

            case 'members':
                $query = "SELECT 
                            CONCAT(u.firstname, ' ', u.middlename, ' ', u.lastname) as 'Member Name',
                            u.account_number as 'Account Number',
                            u.gender as 'Gender',
                            u.phone_number as 'Contact Number',
                            u.email as 'Email',
                            u.address as 'Address',
                            s.starting_date as 'Membership Start',
                            s.expiration_date as 'Membership End',
                            CASE 
                                WHEN s.expiration_date >= CURDATE() THEN 'Active'
                                ELSE 'Expired'
                            END as 'Status'
                         FROM users u
                         LEFT JOIN subscriptions s ON u.user_id = s.user_id
                         JOIN user_roles ur ON u.user_id = ur.user_id
                         WHERE ur.role_id = 5
                         AND DATE(u.registration_date) BETWEEN ? AND ?
                         ORDER BY u.lastname, u.firstname";
                break;
        }

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
    }

    // Get report data
    $report_data = getReportData($conn, $report_type, $start_date, $end_date);

    if (empty($report_data)) {
        throw new Exception("No data found for the selected date range");
    }

    // Calculate totals for numeric columns
    $totals = array();
    foreach ($report_data[0] as $key => $value) {
        if (strpos(strtolower($key), 'amount') !== false || 
            strpos(strtolower($key), 'total') !== false || 
            strpos(strtolower($key), 'revenue') !== false || 
            strpos(strtolower($key), 'price') !== false || 
            strpos(strtolower($key), 'sale') !== false) {
            $totals[$key] = array_sum(array_column($report_data, $key));
        }
    }

    // Configure PDF options
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('defaultFont', 'Helvetica');
    $options->set('chroot', realpath('../'));
    $options->set('enable_remote', true);
    
    // Create new PDF document
    $dompdf = new Dompdf($options);
    
    // Get the base64 encoded image
    $imagePath = realpath('../assets/reports-header.png');
    $imageData = base64_encode(file_get_contents($imagePath));
    
    // Simple HTML template
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>' . ucfirst($report_type) . ' Report</title>
        <style>
            @page {
                margin: 1cm 1.5cm 2cm 1.5cm;
                size: landscape;
            }
            body {
                font-family: Helvetica, Arial, sans-serif;
                color: #333333;
                line-height: 1.4;
                margin: 0;
                padding: 0;
            }
            .header-image {
                width: 100%;
                max-height: 100px;
                object-fit: contain;
                margin-bottom: 20px;
            }
            .report-title {
                font-size: 24px;
                color: #2C3E50;
                text-align: center;
                margin: 20px 0;
                font-weight: bold;
            }
            .date-range {
                text-align: center;
                color: #5D6975;
                margin-bottom: 30px;
                font-size: 14px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 10px;
            }
            th {
                background-color: #F6C23E;
                color: white;
                padding: 8px 4px;
                text-align: left;
                font-weight: bold;
                border: 1px solid #ddd;
                white-space: nowrap;
            }
            td {
                padding: 6px 4px;
                border: 1px solid #ddd;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #f5f5f5;
            }
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 10px;
                color: #666;
                padding: 10px;
                border-top: 1px solid #ddd;
            }
            .total-row {
                background-color: #F6C23E !important;
                color: white;
                font-weight: bold;
            }
            .total-row:hover {
                background-color: #F6C23E !important;
            }
        </style>
    </head>
    <body>
        <img src="data:image/png;base64,' . $imageData . '" class="header-image">
        <h1 class="report-title">' . ucfirst($report_type) . ' Report</h1>
        <div class="date-range">
            Period: ' . date('F d, Y', strtotime($start_date)) . ' - ' . date('F d, Y', strtotime($end_date)) . '
        </div>
        <table>
            <thead>
                <tr>';
    
    // Add table headers
    foreach (array_keys($report_data[0]) as $header) {
        $html .= '<th>' . htmlspecialchars($header) . '</th>';
    }
    
    $html .= '</tr>
            </thead>
            <tbody>';
    
    // Add table data with number formatting where appropriate
    foreach ($report_data as $row) {
        $html .= '<tr>';
        foreach ($row as $key => $value) {
            if (strpos($key, 'Amount') !== false || 
                strpos($key, 'Rate') !== false || 
                strpos($key, 'Total') !== false || 
                strpos($key, 'Discount') !== false && strpos($value, '%') === false) {
                // Format currency values
                $html .= '<td>Php ' . number_format((float)$value, 2) . '</td>';
            } elseif (strpos($key, 'Date & Time') !== false) {
                // Format datetime
                $html .= '<td>' . date('M d, Y h:i A', strtotime($value)) . '</td>';
            } elseif (strpos($key, 'Start') !== false || strpos($key, 'End') !== false) {
                // Format date
                $html .= '<td>' . date('M d, Y', strtotime($value)) . '</td>';
            } else {
                $html .= '<td>' . htmlspecialchars($value) . '</td>';
            }
        }
        $html .= '</tr>';
    }

    // Add totals row for reports with monetary values
    if (in_array($report_type, ['sales', 'coupons', 'discounts'])) {
        $html .= '<tr class="total-row">';
        
        // Get column count
        $columnCount = count($report_data[0]);
        
        // Counter for current column
        $currentColumn = 0;
        
        foreach ($row as $key => $value) {
            $currentColumn++;
            
            if ($currentColumn === 1) {
                // First column shows "TOTAL"
                $html .= '<td>TOTAL</td>';
            } elseif (strpos($key, 'Total Discount') !== false || 
                      strpos($key, 'Final Amount') !== false || 
                      strpos($key, 'Total Amount') !== false) {
                // Calculate total for monetary columns
                $total = array_sum(array_column($report_data, $key));
                $html .= '<td>Php ' . number_format($total, 2) . '</td>';
            } else {
                // Empty cell for non-total columns
                $html .= '<td></td>';
            }
        }
        
        $html .= '</tr>';
    }
    
    $html .= '</tbody>
        </table>
        <div class="footer">
            Generated on: ' . date('F d, Y h:i:s A') . '<br>
            Generated by: ' . htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']) . '<br>
            FiT-LOGSYNC Report
        </div>
    </body>
    </html>';

    // Load HTML
    $dompdf->loadHtml($html);

    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF
    $dompdf->render();

    // Generate filename
    $filename = $report_type . '_report_' . date('Ymd_His') . '.pdf';

    // Clear any previous output
    if (ob_get_length()) ob_clean();

    // Set headers
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: public');
    
    // Output PDF
    echo $dompdf->output();

    // Log report generation
    $query = "INSERT INTO report_logs (user_id, report_type, date_range_start, date_range_end) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $_SESSION['user_id'], $report_type, $start_date, $end_date);
    $stmt->execute();

} catch (Exception $e) {
    error_log("Report generation error: " . $e->getMessage());
    header('Content-Type: text/plain');
    die("Error generating report: " . $e->getMessage());
}
?> 