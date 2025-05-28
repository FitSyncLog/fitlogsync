<?php include 'session-management.php'; ?>
<?php
// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Check if the user is logged in and is a member
if (!isset($_SESSION['login']) || $_SESSION['role_id'] != 5) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>My Subscription | FiT-LOGSYNC</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'layout/navbar.php'; ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">My Subscription</h1>

                    <!-- Current Subscription Status -->
                    <?php
                    $sql_current = "SELECT s.*, pt.*, p.plan_name 
                                   FROM subscriptions s
                                   JOIN payment_transactions pt ON s.payment_transaction_id = pt.payment_transaction_id
                                   JOIN plans p ON pt.plan_id = p.plan_id
                                   WHERE s.user_id = ? 
                                   AND CURDATE() BETWEEN s.starting_date AND s.expiration_date";
                    $stmt = $conn->prepare($sql_current);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $current_result = $stmt->get_result();
                    $current = $current_result->fetch_assoc();
                    ?>
                    <?php if ($current) { ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Current Subscription Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span class="badge badge-success">Active</span></p>
                                    <p><strong>Plan:</strong> <?= $current['plan_name'] ?></p>
                                    <p><strong>Start Date:</strong> <?= date('F d, Y', strtotime($current['starting_date'])) ?></p>
                                    <p><strong>End Date:</strong> <?= date('F d, Y', strtotime($current['expiration_date'])) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Amount Paid:</strong> ₱<?= number_format($current['grand_total'], 2) ?></p>
                                    <p><strong>Payment Date:</strong> <?= date('F d, Y', strtotime($current['transaction_date_time'])) ?></p>
                                    <p><strong>Receipt No:</strong> <?= $current['acknowledgement_receipt_number'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                        <h5 class="text-danger">No Active Subscription</h5>
                        <p>Please visit the front desk to renew your subscription.</p>
                    </div>
                    <?php } ?>

                    <!-- Subscription History -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Subscription History</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Plan</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Amount Paid</th>
                                            <th>Receipt No.</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql_history = "SELECT 
                                            s.subscription_id, s.starting_date, s.expiration_date,
                                            pt.*,
                                            u.firstname as staff_firstname, u.lastname as staff_lastname,
                                            d.discount_name, d.discount_type, d.discount_value,
                                            c.coupon_name, c.coupon_type, c.coupon_value
                                        FROM subscriptions s
                                        JOIN payment_transactions pt ON s.payment_transaction_id = pt.payment_transaction_id
                                        LEFT JOIN users u ON pt.transact_by = u.user_id
                                        LEFT JOIN discounts d ON pt.discount_id = d.discount_id
                                        LEFT JOIN coupons c ON pt.coupon_id = c.coupon_id
                                        WHERE s.user_id = ?
                                        ORDER BY s.starting_date DESC";
                                        
                                        $stmt = $conn->prepare($sql_history);
                                        $stmt->bind_param("i", $user_id);
                                        $stmt->execute();
                                        $history_result = $stmt->get_result();

                                        while ($row = $history_result->fetch_assoc()) {
                                            $status = '';
                                            $today = new DateTime('now', new DateTimeZone('Asia/Manila'));
                                            $start = new DateTime($row['starting_date'], new DateTimeZone('Asia/Manila'));
                                            $end = new DateTime($row['expiration_date'], new DateTimeZone('Asia/Manila'));

                                            if ($today >= $start && $today <= $end) {
                                                $status = '<span class="badge badge-success">Active</span>';
                                            } elseif ($today < $start) {
                                                $status = '<span class="badge badge-info">Upcoming</span>';
                                            } else {
                                                $status = '<span class="badge badge-secondary">Expired</span>';
                                            }

                                            // Calculate discount amount
                                            $subtotal = $row['plan_price_at_transaction'] * $row['plan_duration_at_transaction'];
                                            $discount_amount = 0;
                                            if ($row['discount_id'] && isset($row['discount_type']) && isset($row['discount_value'])) {
                                                if ($row['discount_type'] === 'percentage') {
                                                    $discount_amount = $subtotal * ($row['discount_value'] / 100);
                                                } else {
                                                    $discount_amount = $row['discount_value'];
                                                }
                                            }

                                            // Calculate coupon amount
                                            $coupon_amount = 0;
                                            if ($row['coupon_id'] && isset($row['coupon_type']) && isset($row['coupon_value'])) {
                                                $remaining_amount = $subtotal - $discount_amount;
                                                if ($row['coupon_type'] === 'percentage') {
                                                    $coupon_amount = $remaining_amount * ($row['coupon_value'] / 100);
                                                } else {
                                                    $coupon_amount = $row['coupon_value'];
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['plan_name_at_transaction']) ?></td>
                                                <td><?= date('M d, Y', strtotime($row['starting_date'])) ?></td>
                                                <td><?= date('M d, Y', strtotime($row['expiration_date'])) ?></td>
                                                <td>₱<?= number_format($row['grand_total'], 2) ?></td>
                                                <td><?= htmlspecialchars($row['acknowledgement_receipt_number']) ?></td>
                                                <td><?= $status ?></td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal" 
                                                            data-target="#receiptModal<?= $row['payment_transaction_id'] ?>">
                                                        <i class="fas fa-receipt"></i> View Receipt
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Receipt Modal -->
                                            <div class="modal fade" id="receiptModal<?= $row['payment_transaction_id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning text-white">
                                                            <h5 class="modal-title">
                                                                <i class="fas fa-receipt"></i> Acknowledgement Receipt
                                                            </h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="receipt-container border p-4">
                                                                <!-- Receipt Header -->
                                                                <div class="text-center mb-4">
                                                                    <img src="assets/fitlogsync1.png" alt="FiT-LOGSYNC Logo" style="height: 100px;">
                                                                    <h4 class="mt-2">FiT-LOGSYNC</h4>
                                                                    <?php
                                                                    // Get business information
                                                                    $info_query = "SELECT information_for, description FROM information WHERE information_for IN ('address', 'phone_number', 'email')";
                                                                    $info_result = $conn->query($info_query);
                                                                    $business_info = [];
                                                                    while ($info_row = $info_result->fetch_assoc()) {
                                                                        $business_info[$info_row['information_for']] = $info_row['description'];
                                                                    }
                                                                    ?>
                                                                    <p class="text-muted">
                                                                        <?php 
                                                                        echo $business_info['address'] ?? '';
                                                                        if (!empty($business_info['phone_number'])) echo ' | ' . $business_info['phone_number'];
                                                                        if (!empty($business_info['email'])) echo ' | ' . $business_info['email'];
                                                                        ?>
                                                                    </p>
                                                                </div>

                                                                <!-- Receipt Details -->
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Acknowledgement Receipt No.:</strong><br><?= htmlspecialchars($row['acknowledgement_receipt_number']) ?></p>
                                                                        <p class="mb-1"><strong>Date:</strong><br><?= date('F d, Y', strtotime($row['transaction_date_time'])) ?></p>
                                                                        <p class="mb-1"><strong>Time:</strong><br><?= date('g:i A', strtotime($row['transaction_date_time'])) ?></p>
                                                                    </div>
                                                                    <div class="col-md-6 text-right">
                                                                        <p class="mb-1"><strong>Processed By:</strong><br><?= isset($row['staff_firstname']) && isset($row['staff_lastname']) ? htmlspecialchars($row['staff_lastname'] . ', ' . $row['staff_firstname']) : 'N/A' ?></p>
                                                                        <p class="mb-1"><strong>Payment Method:</strong><br><?= ucfirst(htmlspecialchars($row['payment_method'])) ?></p>
                                                                    </div>
                                                                </div>

                                                                <!-- Member Information -->
                                                                <div class="card mb-3">
                                                                    <div class="card-header bg-light">
                                                                        <strong>Member Information</strong>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($_SESSION['lastname'] . ', ' . $_SESSION['firstname'] . ' ' . $_SESSION['middlename']) ?></p>
                                                                        <p class="mb-0"><strong>Account Number:</strong> <?= chunk_split(htmlspecialchars($_SESSION['account_number']), 4, ' ') ?></p>
                                                                    </div>
                                                                </div>

                                                                <!-- Subscription Details -->
                                                                <div class="table-responsive mb-3">
                                                                    <table class="table table-bordered">
                                                                        <thead class="bg-light">
                                                                            <tr>
                                                                                <th>Description</th>
                                                                                <th class="text-right" style="width: 30%;">Amount</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <strong><?= htmlspecialchars($row['plan_name_at_transaction']) ?></strong><br>
                                                                                    <small><?= htmlspecialchars($row['plan_description_at_transaction']) ?></small><br>
                                                                                    <small>₱<?= number_format($row['plan_price_at_transaction'], 2) ?> / month X <?= (int)$row['plan_duration_at_transaction'] ?> month(s)</small><br>
                                                                                    <small class="text-warning">
                                                                                        <strong>Subscription Period:</strong><br>
                                                                                        <?= date('F d, Y', strtotime($row['starting_date'])) ?> - 
                                                                                        <?= date('F d, Y', strtotime($row['expiration_date'])) ?>
                                                                                    </small>
                                                                                </td>
                                                                                <td class="text-right">₱<?= number_format($row['plan_price_at_transaction'] * $row['plan_duration_at_transaction'], 2) ?></td>
                                                                            </tr>

                                                                            <?php if ($row['discount_id'] && isset($row['discount_type']) && isset($row['discount_value'])): ?>
                                                                                <tr>
                                                                                    <td>
                                                                                        <strong>Discount</strong><br>
                                                                                        <?= htmlspecialchars($row['discount_name']) . ' (' . 
                                                                                            ($row['discount_type'] === 'percentage' ? 
                                                                                            $row['discount_value'] . '%' : 
                                                                                            '₱' . number_format($row['discount_value'], 2)) . ')' ?>
                                                                                    </td>
                                                                                    <td class="text-right text-danger">-₱<?= number_format($discount_amount, 2) ?></td>
                                                                                </tr>
                                                                            <?php endif; ?>

                                                                            <?php if ($row['coupon_id'] && isset($row['coupon_type']) && isset($row['coupon_value'])): ?>
                                                                                <tr>
                                                                                    <td>
                                                                                        <strong>Coupon Discount</strong><br>
                                                                                        <?= htmlspecialchars($row['coupon_name']) . ' (' . 
                                                                                            ($row['coupon_type'] === 'percentage' ? 
                                                                                            $row['coupon_value'] . '%' : 
                                                                                            '₱' . number_format($row['coupon_value'], 2)) . ')' ?>
                                                                                    </td>
                                                                                    <td class="text-right text-danger">-₱<?= number_format($coupon_amount, 2) ?></td>
                                                                                </tr>
                                                                            <?php endif; ?>

                                                                            <tr class="font-weight-bold">
                                                                                <td class="text-right">TOTAL</td>
                                                                                <td class="text-right">₱<?= number_format($row['grand_total'], 2) ?></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                <!-- Payment Notes -->
                                                                <div class="card mb-3">
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
                                                                    <p class="mb-1">-----------------------------------</p>
                                                                    <p class="mb-1">Authorized Signature</p>
                                                                    <p class="small text-muted">This is a computer-generated receipt. No signature required.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-warning" onclick="printReceipt('receiptModal<?= $row['payment_transaction_id'] ?>')">
                                                                <i class="fas fa-print"></i> Print Receipt
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        function printReceipt(modalId) {
            const modal = document.getElementById(modalId);
            const printContents = modal.querySelector('.receipt-container').innerHTML;
            
            // Create a new window with only the receipt content
            const printWindow = window.open('', '_blank');
            printWindow.document.open();
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Receipt</title>
                        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
                        <link href="css/sb-admin-2.min.css" rel="stylesheet">
                        <style>
                            body {
                                padding: 20px;
                                font-family: 'Nunito', sans-serif;
                            }
                            .receipt-container {
                                max-width: 800px;
                                margin: 0 auto;
                                padding: 20px;
                            }
                            @media print {
                                @page {
                                    margin: 0.5cm;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="receipt-container">
                            ${printContents}
                        </div>
                    </body>
                </html>
            `);
            printWindow.document.close();

            // Wait for resources to load
            printWindow.onload = function() {
                printWindow.print();
                printWindow.onafterprint = function() {
                    printWindow.close();
                };
            };
        }
    </script>
</body>
</html> 