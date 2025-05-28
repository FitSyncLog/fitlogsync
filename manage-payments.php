<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "manage-coupons.php";
$role_id = $_SESSION['role_id'];
$query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $page_name, $role_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Manage Payments | FiT-LOGSYNC</title>
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
        <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
        <script src="assets/js/sweetalert2.all.min.js"></script>
        <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />
        <script src="assets/js/sessionExpired.js"></script>

        <!-- Add print styles -->
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                .modal-content * {
                    visibility: visible;
                }

                .modal {
                    position: absolute;
                    left: 0;
                    top: 0;
                    margin: 0;
                    padding: 0;
                    min-height: 100%;
                    visibility: visible;
                    overflow: visible !important;
                }

                .modal-dialog {
                    width: 100% !important;
                    max-width: 100% !important;
                    margin: 0;
                    padding: 0;
                }

                .modal-content {
                    border: 0 !important;
                }

                .modal-header {
                    display: none !important;
                }

                .modal-footer {
                    display: none !important;
                }

                .receipt-container {
                    padding: 15px !important;
                }

                @page {
                    margin: 0.5cm;
                }
            }
        </style>

    </head>

    <body id="page-top">
        <?php
        if (isset($_GET['Success'])) {
            $message = htmlspecialchars($_GET['Success']);
            echo "<script>
                Swal.fire({
                position: 'center',
                icon: 'success',
                title: '{$message}',
                showConfirmButton: false,
                timer: 1500
                });
            </script>";
        }

        if (isset($_GET['Failed'])) {
            $message = htmlspecialchars($_GET['Failed']);
            echo "<script>
                Swal.fire({
                position: 'center',
                icon: 'error',
                title: '{$message}',
                showConfirmButton: false,
                timer: 1500
                });
            </script>";
        }
        ?>

        <div id="wrapper">
            <?php include 'layout/sidebar.php'; ?>
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <?php include 'layout/navbar.php'; ?>
                    <div class="container-fluid">
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <?php
                            // Set timezone and get current date
                            date_default_timezone_set('Asia/Manila');
                            $current_date = date('F j, Y'); // Example: May 26, 2025
                            ?>

                            <h1 class="h3 mb-0 text-gray-800">Manage Payments ( <?= $current_date ?> | Today )</h1>

                            <div class="text-right">
                                <a class="btn btn-warning" href="create-transaction.php">New Transaction</a>
                            </div>
                        </div>

                        <div class="row">

                            <?php
                            $role_id = $_SESSION['role_id'];

                            if ($role_id == 1 || $role_id == 2) {
                                ?>
                                <!-- Total Transactions Today -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Total Transaction
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-transactions">
                                                        <?php
                                                        // Set timezone to Manila
                                                        date_default_timezone_set('Asia/Manila');
                                                        $today = date('Y-m-d');
                                                        
                                                        $sql_transactions = "SELECT COUNT(*) AS total_transactions 
                                                                           FROM payment_transactions 
                                                                           WHERE DATE(transaction_date_time) = ?";
                                                        $stmt = $conn->prepare($sql_transactions);
                                                        $stmt->bind_param("s", $today);
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $row = $result->fetch_assoc();
                                                        echo $row['total_transactions'];
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- New Subscribers Today -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        New Subscribers
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="new-subscribers">
                                                        <?php
                                                        $sql_new = "SELECT COUNT(DISTINCT pt.user_id) as new_subscribers
                                                                   FROM payment_transactions pt
                                                                   WHERE DATE(pt.transaction_date_time) = ?
                                                                   AND pt.user_id IN (
                                                                       SELECT user_id
                                                                       FROM subscriptions
                                                                       GROUP BY user_id
                                                                       HAVING COUNT(*) = 1
                                                                   )";
                                                        $stmt = $conn->prepare($sql_new);
                                                        $stmt->bind_param("s", $today);
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $row = $result->fetch_assoc();
                                                        echo $row['new_subscribers'];
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Renewed Subscribers Today -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Renewed Subscribers
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="renewed-subscribers">
                                                        <?php
                                                        $sql_renewed = "SELECT COUNT(DISTINCT pt.user_id) as renewed_subscribers
                                                                      FROM payment_transactions pt
                                                                      WHERE DATE(pt.transaction_date_time) = ?
                                                                      AND pt.user_id IN (
                                                                          SELECT user_id
                                                                          FROM subscriptions
                                                                          GROUP BY user_id
                                                                          HAVING COUNT(*) > 1
                                                                      )";
                                                        $stmt = $conn->prepare($sql_renewed);
                                                        $stmt->bind_param("s", $today);
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $row = $result->fetch_assoc();
                                                        echo $row['renewed_subscribers'];
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-sync fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Sales Today -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Total Sales
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-sales">
                                                        <?php
                                                        $sql_sales = "SELECT COALESCE(SUM(grand_total), 0) as total_sales
                                                                     FROM payment_transactions
                                                                     WHERE DATE(transaction_date_time) = ?";
                                                        $stmt = $conn->prepare($sql_sales);
                                                        $stmt->bind_param("s", $today);
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $row = $result->fetch_assoc();
                                                        echo '₱ ' . number_format($row['total_sales'], 2);
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>

                        <?php
                        $query = "SELECT pt.*, 
                            c.coupon_name, c.coupon_type, c.coupon_value,
                            d.discount_name, d.discount_type, d.discount_value
                        FROM payment_transactions pt
                        LEFT JOIN coupons c ON pt.coupon_id = c.coupon_id
                        LEFT JOIN discounts d ON pt.discount_id = d.discount_id
                        WHERE DATE(pt.transaction_date_time) = CURDATE()";
                        $result = $conn->query($query);
                        ?>
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">Transaction History</h1>

                        </div>


                        <div class="card shadow mb-4">

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Acknow. Receipt No.</th>
                                                <th>Full Name</th>
                                                <th>Number of Months</th>
                                                <th>Montly Plan</th>
                                                <th>Total Bill</th>
                                                <th>Transact By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    // Fetch created_by user details
                                                    $payment_transaction_id = $row['payment_transaction_id'];
                                                    $acknowledgement_receipt_number = $row['acknowledgement_receipt_number'];
                                                    $user_id = $row['user_id'];
                                                    $plan_id = $row['plan_id'];
                                                    $plan_name_at_transaction = $row['plan_name_at_transaction'] ?? '';
                                                    $plan_description_at_transaction = $row['plan_description_at_transaction'] ?? '';
                                                    $plan_price_at_transaction = $row['plan_price_at_transaction'] ?? '';
                                                    $plan_duration_at_transaction = $row['plan_duration_at_transaction'] ?? '';
                                                    $coupon_id = $row['coupon_id'] ?? null;
                                                    $discount_id = $row['discount_id'] ?? null;
                                                    $grand_total = $row['grand_total'];
                                                    $transaction_date_time = $row['transaction_date_time'];
                                                    $payment_method = $row['payment_method'];
                                                    $transact_by = $row['transact_by'];

                                                    // Calculate discount amount if discount exists
                                                    $discount_amount = 0;
                                                    $subtotal = $plan_price_at_transaction * $plan_duration_at_transaction;
                                                    $amount_after_category_discount = $subtotal;

                                                    if ($discount_id) {
                                                        if ($row['discount_type'] === 'percentage') {
                                                            $discount_amount = $subtotal * ($row['discount_value'] / 100);
                                                            $amount_after_category_discount = $subtotal - $discount_amount;
                                                        } else {
                                                            $discount_amount = $row['discount_value'];
                                                            $amount_after_category_discount = $subtotal - $discount_amount;
                                                        }
                                                    }

                                                    // Calculate coupon amount if coupon exists
                                                    $coupon_amount = 0;
                                                    if ($coupon_id) {
                                                        if ($row['coupon_type'] === 'percentage') {
                                                            $coupon_amount = $amount_after_category_discount * ($row['coupon_value'] / 100);
                                                        } else {
                                                            $coupon_amount = $row['coupon_value'];
                                                        }
                                                    }

                                                    $transaction_date_time_formatted = date('F d, Y | g:i A', strtotime($transaction_date_time));

                                                    $member_query = "SELECT lastname, firstname, account_number FROM users WHERE user_id = $user_id";
                                                    $member_result = $conn->query($member_query);
                                                    $member = $member_result->fetch_assoc();
                                                    $member_full_name = $member ? $member['lastname'] . ', ' . $member['firstname'] : 'Unknown';

                                                    $transact_by_query = "SELECT lastname, firstname FROM users WHERE user_id = $transact_by";
                                                    $transact_by_result = $conn->query($transact_by_query);
                                                    $transact_by = $transact_by_result->fetch_assoc();
                                                    $transact_by_full_name = $transact_by ? $transact_by['lastname'] . ', ' . $transact_by['firstname'] : 'Unknown';
                                                    ?>

                                                    <tr>
                                                        <td class="text-center"><?= $transaction_date_time_formatted ?></td>
                                                        <td class="text-center"><?= $acknowledgement_receipt_number ?></td>
                                                        <td class="text-center"><?= $member_full_name ?></td>
                                                        <td class="text-center"><?= $plan_duration_at_transaction ?></td>
                                                        <td class="text-center">₱ <?= $plan_price_at_transaction ?> / mo</td>
                                                        <td class="text-center">₱ <?= $grand_total ?></td>
                                                        <td class="text-center"><?= $transact_by_full_name ?></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                                data-target="#paymentModal<?= $payment_transaction_id ?>">
                                                                View
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='9' class='text-center'>No transaction yet.</td></tr>";
                                            }
                                            ?>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php
                        if ($result->num_rows > 0) {
                            mysqli_data_seek($result, 0);  // Reset the result pointer
                            while ($row = $result->fetch_assoc()) {
                                $payment_transaction_id = $row['payment_transaction_id'];
                                
                                // Fetch transaction details for this specific payment_transaction_id
                                $modal_query = "SELECT pt.*, 
                                    c.coupon_name, c.coupon_type, c.coupon_value,
                                    d.discount_name, d.discount_type, d.discount_value,
                                    u.lastname as member_lastname, u.firstname as member_firstname, u.account_number,
                                    tb.lastname as transact_by_lastname, tb.firstname as transact_by_firstname,
                                    s.starting_date, s.expiration_date
                                FROM payment_transactions pt
                                LEFT JOIN coupons c ON pt.coupon_id = c.coupon_id
                                LEFT JOIN discounts d ON pt.discount_id = d.discount_id
                                LEFT JOIN users u ON pt.user_id = u.user_id
                                LEFT JOIN users tb ON pt.transact_by = tb.user_id
                                LEFT JOIN subscriptions s ON pt.payment_transaction_id = s.payment_transaction_id
                                WHERE pt.payment_transaction_id = ?";
                                
                                $modal_stmt = $conn->prepare($modal_query);
                                $modal_stmt->bind_param("i", $payment_transaction_id);
                                $modal_stmt->execute();
                                $modal_result = $modal_stmt->get_result();
                                $modal_row = $modal_result->fetch_assoc();

                                // Calculate discount amount if discount exists
                                $discount_amount = 0;
                                $subtotal = $modal_row['plan_price_at_transaction'] * $modal_row['plan_duration_at_transaction'];
                                $amount_after_category_discount = $subtotal;

                                if ($modal_row['discount_id']) {
                                    if ($modal_row['discount_type'] === 'percentage') {
                                        $discount_amount = $subtotal * ($modal_row['discount_value'] / 100);
                                        $amount_after_category_discount = $subtotal - $discount_amount;
                                    } else {
                                        $discount_amount = $modal_row['discount_value'];
                                        $amount_after_category_discount = $subtotal - $discount_amount;
                                    }
                                }

                                // Calculate coupon amount if coupon exists
                                $coupon_amount = 0;
                                if ($modal_row['coupon_id']) {
                                    if ($modal_row['coupon_type'] === 'percentage') {
                                        $coupon_amount = $amount_after_category_discount * ($modal_row['coupon_value'] / 100);
                                    } else {
                                        $coupon_amount = $modal_row['coupon_value'];
                                    }
                                }

                                $member_full_name = $modal_row['member_lastname'] . ', ' . $modal_row['member_firstname'];
                                $transact_by_full_name = $modal_row['transact_by_lastname'] . ', ' . $modal_row['transact_by_firstname'];
                                ?>

                                <!-- Modal for each transaction -->
                                <div class="modal fade" id="paymentModal<?= $payment_transaction_id ?>" tabindex="-1" role="dialog"
                                    aria-labelledby="paymentModalLabel<?= $payment_transaction_id ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title" id="paymentModalLabel<?= $payment_transaction_id ?>">
                                                    <i class="fas fa-receipt"></i> Acknowledgement Receipt
                                                </h5>
                                                <button type="button" class="close text-white" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="receipt-container border p-4">
                                                    <div class="text-center mb-4">
                                                        <img src="assets/fitlogsync1.png" alt="FiT-LOGSYNC Logo"
                                                            style="height: 100px;" class="mb-2">
                                                        <div class="text-center mb-4">

                                                            <?php
                                                            $information_query = "SELECT * FROM information";
                                                            $information_result = $conn->query($information_query);

                                                            // Initialize variables
                                                            $address = '';
                                                            $phone_number = '';
                                                            $email = '';

                                                            while ($information_row = $information_result->fetch_assoc()) {
                                                                switch ($information_row['information_for']) {
                                                                    case 'address':
                                                                        $address = $information_row['description'];
                                                                        break;
                                                                    case 'phone_number':
                                                                        $phone_number = $information_row['description'];
                                                                        break;
                                                                    case 'email':
                                                                        $email = $information_row['description'];
                                                                        break;
                                                                }
                                                            }
                                                            ?>
                                                            <h3 class="font-weight-bold">Fit-LOGSYNC</h3>
                                                            <p class="mb-0"><?= $address ?> | <?= $phone_number ?> | <?= $email ?>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <hr class="my-3 border-dark">

                                                    <!-- Receipt Details -->
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Acknowledgement Receipt No.:</strong>
                                                                <?= $modal_row['acknowledgement_receipt_number'] ?></p>
                                                            <p class="mb-1"><strong>Date:</strong>
                                                                <?= date('F d, Y', strtotime($modal_row['transaction_date_time'])) ?></p>
                                                            <p class="mb-1"><strong>Time:</strong>
                                                                <?= date('g:i A', strtotime($modal_row['transaction_date_time'])) ?></p>
                                                        </div>
                                                        <div class="col-md-6 text-right">
                                                            <p class="mb-1"><strong>Processed By:</strong>
                                                                <?= $transact_by_full_name ?></p>
                                                            <p class="mb-1"><strong>Payment Method:</strong>
                                                                <?= ucfirst($modal_row['payment_method']) ?></p>
                                                        </div>
                                                    </div>

                                                    <!-- Member Information -->
                                                    <div class="member-info p-3 mb-3 bg-light rounded">
                                                        <h5 class="font-weight-bold">Member Information</h5>
                                                        <p class="mb-1"><strong>Name:</strong> <?= $member_full_name ?></p>
                                                        <p class="mb-1"><strong>Account Number:</strong>
                                                            <?= rtrim(chunk_split($modal_row['account_number'], 4, '-'), ' - ') ?></p>
                                                    </div>

                                                    <!-- Transaction Details -->
                                                    <div class="transaction-details mb-4">
                                                        <h5 class="font-weight-bold">Subscription Details</h5>
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
                                                                        <strong><?= $modal_row['plan_name_at_transaction'] ?></strong><br>
                                                                        <small><?= $modal_row['plan_description_at_transaction'] ?></small><br>
                                                                        <small>₱<?= number_format($modal_row['plan_price_at_transaction'], 2) ?>
                                                                            / month X <?= $modal_row['plan_duration_at_transaction'] ?> month(s)
                                                                        </small><br>
                                                                        <small class="text-warning">
                                                                            <strong>Subscription Period:</strong><br>
                                                                            <?= date('F d, Y', strtotime($modal_row['starting_date'])) ?> - 
                                                                            <?= date('F d, Y', strtotime($modal_row['expiration_date'])) ?>
                                                                        </small>
                                                                    </td>
                                                                    <td class="text-right">₱
                                                                        <?= number_format($subtotal, 2) ?>
                                                                    </td>
                                                                </tr>

                                                                <?php if ($modal_row['discount_id']): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <strong><?= $modal_row['discount_name'] ?> Discount</strong><br>
                                                                            <small><?= $modal_row['discount_type'] === 'percentage' ? $modal_row['discount_value'] . '%' : '₱' . number_format($modal_row['discount_value'], 2) ?></small>
                                                                        </td>
                                                                        <td class="text-right text-danger">-₱
                                                                            <?= number_format($discount_amount, 2) ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>

                                                                <?php if ($modal_row['coupon_id']): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <strong>Coupon Discount:</strong> 
                                                                            <?= $modal_row['coupon_type'] === 'percentage' ? $modal_row['coupon_value'] . '%' : '₱' . number_format($modal_row['coupon_value'], 2) ?><br>
                                                                            <small><strong>Event Name:</strong> <?= $modal_row['coupon_name'] ?></small><br>
                                                                        </td>
                                                                        <td class="text-right text-danger">-₱
                                                                            <?= number_format($coupon_amount, 2) ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>

                                                                <tr class="font-weight-bold">
                                                                    <td class="text-right">TOTAL</td>
                                                                    <td class="text-right">₱
                                                                        <?= number_format($modal_row['grand_total'], 2) ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Payment Notes -->
                                                    <div class="payment-notes p-3 mb-3 bg-light rounded">
                                                        <h5 class="font-weight-bold">Payment Notes</h5>
                                                        <p class="mb-1 small">Thank you for your payment. This receipt serves as
                                                            your official record of payment.</p>
                                                        <p class="mb-1 small">Please present this receipt for any inquiries
                                                            regarding your membership.</p>
                                                    </div>

                                                    <!-- Footer -->
                                                    <div class="text-center mt-4">
                                                        <p class="mb-1">-----------------------------------</p>
                                                        <p class="mb-1">Authorized Signature</p>
                                                        <p class="small text-muted">This is a computer-generated receipt. No
                                                            signature required.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-warning"
                                                    onclick="printReceipt('paymentModal<?= $payment_transaction_id ?>')">
                                                    <i class="fas fa-print"></i> Print Receipt
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>




        <!-- jQuery and Bootstrap -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Rest -->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="js/demo/datatables-demo.js"></script>

        <!-- Print Receipt Function -->
        <script>
            function printReceipt(modalId) {
                const modal = document.getElementById(modalId);
                const originalContents = document.body.innerHTML;

                // Get only the receipt content
                const printContents = modal.querySelector('.modal-content').innerHTML;

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
                                .modal-header, .modal-footer {
                                    display: none !important;
                                }
                                .receipt-container {
                                    padding: 15px;
                                }
                                @media print {
                                    @page {
                                        margin: 0.5cm;
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="modal-content">
                                ${printContents}
                            </div>
                        </body>
                    </html>
                `);
                printWindow.document.close();

                // Wait for resources to load
                printWindow.onload = function () {
                    printWindow.print();
                    printWindow.onafterprint = function () {
                        printWindow.close();
                    };
                };
            }
        </script>
    </body>

</html>
<?php
} else {
    header("Location: dashboard.php/?AccessDenied=You do not have permission to access this page.");
    exit();
}
