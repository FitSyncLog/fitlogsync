<?php
require_once 'db_con.php';

if (!isset($_POST['payment_transaction_id'])) {
    echo "Error: No payment transaction ID provided";
    exit;
}

$payment_transaction_id = $_POST['payment_transaction_id'];

// Get transaction details
$query = "SELECT pt.*, 
          u_member.firstname as member_firstname, u_member.lastname as member_lastname,
          u_staff.firstname as staff_firstname, u_staff.lastname as staff_lastname,
          u_member.account_number,
          s.starting_date, s.expiration_date
          FROM payment_transactions pt
          LEFT JOIN users u_member ON pt.user_id = u_member.user_id
          LEFT JOIN users u_staff ON pt.transact_by = u_staff.user_id
          LEFT JOIN subscriptions s ON pt.payment_transaction_id = s.payment_transaction_id
          WHERE pt.payment_transaction_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $payment_transaction_id);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

if (!$transaction) {
    echo "Error: Transaction not found";
    exit;
}

// Get business information
$info_query = "SELECT information_for, description FROM information WHERE information_for IN ('address', 'phone_number', 'email')";
$info_result = $conn->query($info_query);
$business_info = [];
while ($info_row = $info_result->fetch_assoc()) {
    $business_info[$info_row['information_for']] = $info_row['description'];
}

// Format account number
$formatted_account = trim(chunk_split($transaction['account_number'], 4, ' '));

// Format the receipt HTML
?>
<div class="receipt-container">
    <!-- Header -->
    <div class="text-center mb-4">
        <img src="assets/fitlogsync1.png" alt="FiT-LOGSYNC Logo" style="height: 100px;">
        <h4 class="mt-2">FiT-LOGSYNC</h4>
        <p class="text-muted">
            <?php 
            echo $business_info['address'];
            if (!empty($business_info['phone_number'])) echo ' | ' . $business_info['phone_number'];
            if (!empty($business_info['email'])) echo ' | ' . $business_info['email'];
            ?>
        </p>
    </div>

    <!-- Receipt Details -->
    <div class="row mb-3">
        <div class="col-md-6">
            <p><strong>Acknowledgement Receipt No.:</strong><br><?php echo $transaction['acknowledgement_receipt_number']; ?></p>
            <p><strong>Date:</strong><br><?php echo date('F d, Y', strtotime($transaction['transaction_date_time'])); ?></p>
            <p><strong>Time:</strong><br><?php echo date('g:i A', strtotime($transaction['transaction_date_time'])); ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Processed By:</strong><br><?php echo $transaction['staff_lastname'] . ', ' . $transaction['staff_firstname']; ?></p>
            <p><strong>Payment Method:</strong><br><?php echo ucfirst($transaction['payment_method']); ?></p>
        </div>
    </div>

    <!-- Member Information -->
    <div class="card mb-3">
        <div class="card-header bg-light">
            <strong>Member Information</strong>
        </div>
        <div class="card-body">
            <p class="mb-1"><strong>Name:</strong> <?php echo $transaction['member_lastname'] . ', ' . $transaction['member_firstname']; ?></p>
            <p class="mb-0"><strong>Account Number:</strong> <?php echo $formatted_account; ?></p>
        </div>
    </div>

    <!-- Subscription Details -->
    <div class="table-responsive mb-3">
        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?php echo $transaction['plan_name_at_transaction']; ?></strong><br>
                        <?php echo $transaction['plan_description_at_transaction']; ?><br>
                        P<?php echo number_format($transaction['plan_price_at_transaction'], 2); ?> / month X <?php echo $transaction['plan_duration_at_transaction']; ?> month(s)<br>
                        <div style="color: #F6C23E;">
                            <strong>Subscription Period:</strong><br>
                            <?php 
                            echo date('F d, Y', strtotime($transaction['starting_date'])) . ' - ' . 
                                 date('F d, Y', strtotime($transaction['expiration_date'])); 
                            ?>
                        </div>
                    </td>
                    <td class="text-right align-middle">
                        P<?php echo number_format($transaction['plan_price_at_transaction'] * $transaction['plan_duration_at_transaction'], 2); ?>
                    </td>
                </tr>

                <?php if ($transaction['discount_id']): ?>
                    <tr>
                        <td>
                            <strong>Discount</strong><br>
                            <?php 
                            // Get discount details
                            $discount_query = "SELECT * FROM discounts WHERE discount_id = ?";
                            $discount_stmt = $conn->prepare($discount_query);
                            $discount_stmt->bind_param("i", $transaction['discount_id']);
                            $discount_stmt->execute();
                            $discount = $discount_stmt->get_result()->fetch_assoc();
                            
                            echo $discount['discount_name'] . ' (' . 
                                 ($discount['discount_type'] === 'percentage' ? 
                                 $discount['discount_value'] . '%' : 
                                 'P' . number_format($discount['discount_value'], 2)) . ')';
                            ?>
                        </td>
                        <td class="text-right align-middle text-danger">
                            -P<?php 
                            $discount_amount = $discount['discount_type'] === 'percentage' ? 
                                ($transaction['plan_price_at_transaction'] * $transaction['plan_duration_at_transaction'] * ($discount['discount_value'] / 100)) : 
                                $discount['discount_value'];
                            echo number_format($discount_amount, 2); 
                            ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if ($transaction['coupon_id']): ?>
                    <tr>
                        <td>
                            <strong>Coupon Discount</strong><br>
                            <?php 
                            // Get coupon details
                            $coupon_query = "SELECT * FROM coupons WHERE coupon_id = ?";
                            $coupon_stmt = $conn->prepare($coupon_query);
                            $coupon_stmt->bind_param("i", $transaction['coupon_id']);
                            $coupon_stmt->execute();
                            $coupon = $coupon_stmt->get_result()->fetch_assoc();
                            
                            echo $coupon['coupon_name'] . ' (' . 
                                 ($coupon['coupon_type'] === 'percentage' ? 
                                 $coupon['coupon_value'] . '%' : 
                                 'P' . number_format($coupon['coupon_value'], 2)) . ')';
                            ?>
                        </td>
                        <td class="text-right align-middle text-danger">
                            -P<?php 
                            $remaining_amount = $transaction['plan_price_at_transaction'] * $transaction['plan_duration_at_transaction'] - $discount_amount;
                            $coupon_amount = $coupon['coupon_type'] === 'percentage' ? 
                                ($remaining_amount * ($coupon['coupon_value'] / 100)) : 
                                $coupon['coupon_value'];
                            echo number_format($coupon_amount, 2); 
                            ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>P<?php echo number_format($transaction['grand_total'], 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment Notes -->
    <div class="card">
        <div class="card-header bg-light">
            <strong>Payment Notes</strong>
        </div>
        <div class="card-body">
            <p class="mb-1">Thank you for your payment. This receipt serves as your official record of payment.</p>
            <p class="mb-0">Please present this receipt for any inquiries regarding your membership.</p>
        </div>
    </div>

    <!-- Signature -->
    <div class="text-center mt-4">
        <p>-----------------------------------</p>
        <p>Authorized Signature</p>
        <p><small class="text-muted">This is a computer-generated receipt. No signature required.</small></p>
    </div>
</div>

<script>
    // Store the payment transaction ID in the modal for the download function
    $('#receiptModal').data('payment-transaction-id', <?php echo $payment_transaction_id; ?>);
</script> 