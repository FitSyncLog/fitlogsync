<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    header("Location: manage-members.php?Failed=No member selected");
    exit();
}

$user_id = $_GET['user_id'];

// Fetch member details
$member_query = "SELECT u.*, ur.role_id 
                FROM users u 
                INNER JOIN user_roles ur ON u.user_id = ur.user_id 
                WHERE u.user_id = ? AND ur.role_id = 5";
$member_stmt = $conn->prepare($member_query);
$member_stmt->bind_param("i", $user_id);
$member_stmt->execute();
$member_result = $member_stmt->get_result();
$member = $member_result->fetch_assoc();

if (!$member) {
    header("Location: manage-members.php?Failed=Member not found");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Member Profile | FiT-LOGSYNC</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <script src="assets/js/sweetalert2.all.min.js"></script>
    <script src="assets/js/sessionExpired.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


    <style>
        .profile-header {
            background: #f8f9fc;
            padding: 2rem;
            border-radius: 0.35rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .tab-content {
            padding: 2rem;
            background: #fff;
            border: 1px solid #e3e6f0;
            border-top: none;
            border-radius: 0 0 0.35rem 0.35rem;
        }

        .nav-tabs .nav-item .nav-link {
            color: #858796;
        }

        .nav-tabs .nav-item .nav-link.active {
            color: #F6C23E;
            border-color: #F6C23E;
        }

        /* Add these styles for dropdown */
        .status-dropdown {
            position: relative;
        }

        .status-dropdown .dropdown-menu {
            position: absolute;
            z-index: 1000;
            min-width: 10rem;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 0.35rem;
        }

        .status-dropdown .dropdown-menu.show {
            display: block;
        }

        .status-dropdown .dropdown-item {
            padding: 0.5rem 1rem;
            clear: both;
            font-weight: 400;
            color: #3a3b45;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }

        .status-dropdown .dropdown-item:hover {
            color: #2e2f37;
            background-color: #eaecf4;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'layout/navbar.php'; ?>
                <div class="container-fluid">
                    <!-- Back Button -->
                    <div class="mb-4">
                        <a href="manage-members.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Members
                        </a>
                    </div>

                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="<?php echo $member['profile_image'] ? 'assets/profile-pictures/' . $member['profile_image'] : 'assets/default-profile.png'; ?>"
                                    class="profile-image rounded-circle" alt="Profile Picture">
                            </div>
                            <div class="col">
                                <h1 class="h3 mb-2 text-gray-800">
                                    <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                </h1>
                                <p class="mb-0"><strong>Account Number:</strong>
                                    <?php echo $member['account_number']; ?></p>
                                <p class="mb-0"><strong>Status:</strong>
                                    <span
                                        class="badge badge-<?php echo $member['status'] === 'Active' ? 'success' : 'danger'; ?>">
                                        <?php echo $member['status']; ?>
                                    </span>
                                </p>
                                <?php
                                // Get latest subscription info
                                $subscription_query = "SELECT s.*, pt.plan_name_at_transaction 
                                                     FROM subscriptions s 
                                                     JOIN payment_transactions pt ON s.payment_transaction_id = pt.payment_transaction_id 
                                                     WHERE s.user_id = ? 
                                                     ORDER BY s.expiration_date DESC 
                                                     LIMIT 1";
                                $subscription_stmt = $conn->prepare($subscription_query);
                                $subscription_stmt->bind_param("i", $user_id);
                                $subscription_stmt->execute();
                                $subscription = $subscription_stmt->get_result()->fetch_assoc();

                                $today = new DateTime();
                                $subscription_status = "No Active Subscription";
                                $badge_class = "badge-secondary";
                                $show_renew_button = true;
                                $subscription_end_date = null;

                                if ($subscription) {
                                    $expiration = new DateTime($subscription['expiration_date']);
                                    $subscription_end_date = $subscription['expiration_date'];
                                    if ($today <= $expiration) {
                                        $subscription_status = "Active - " . $subscription['plan_name_at_transaction'] . " (Until " . $expiration->format('F d, Y') . ")";
                                        $badge_class = "badge-success";
                                        // Only show renew button if subscription expires within 30 days
                                        $days_until_expiration = $today->diff($expiration)->days;
                                        $show_renew_button = $days_until_expiration <= 30;
                                    } else {
                                        $subscription_status = "Expired - Last Plan: " . $subscription['plan_name_at_transaction'] . " (Ended " . $expiration->format('F d, Y') . ")";
                                        $badge_class = "badge-danger";
                                        $show_renew_button = true;
                                    }
                                }
                                ?>
                                <p class="mb-0"><strong>Subscription Status:</strong>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo $subscription_status; ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-auto">
                                <?php if ($show_renew_button): ?>
                                <a href="create-transaction.php?user_id=<?php echo $user_id; ?>&member_name=<?php echo urlencode($member['firstname'] . ' ' . $member['middlename'] . ' ' . $member['lastname']); ?><?php echo $subscription_end_date ? '&subscription_end=' . $subscription_end_date : ''; ?>" 
                                   class="btn btn-success mb-2">
                                    <i class="fas fa-sync-alt mr-1"></i>Renew Subscription
                                </a>
                                <br>
                                <?php endif; ?>
                                <div class="btn-group status-dropdown">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        Change Status
                                    </button>
                                    <div class="dropdown-menu">
                                        <?php
                                        $statuses = ['Active', 'Inactive', 'Suspended', 'Banned'];
                                        foreach ($statuses as $statusOption) {
                                            $activeClass = ($member['status'] === $statusOption) ? ' active' : '';
                                            echo "<a class='dropdown-item{$activeClass}' href='javascript:void(0)' onclick='changeStatus({$user_id}, \"{$statusOption}\")'>{$statusOption}</a>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <a href="edit-member.php?user_id=<?php echo $user_id; ?>" class="btn btn-primary ml-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-danger ml-2" onclick="deleteMember(<?php echo $user_id; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="card shadow mb-4">
                        <div class="card-header p-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#subscription">
                                        <i class="fas fa-clipboard-list mr-2"></i>Subscription
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#attendance">
                                        <i class="fas fa-calendar-check mr-2"></i>Attendance Log
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#details">
                                        <i class="fas fa-user mr-2"></i>Member Details
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <!-- Subscription Tab -->
                            <div class="tab-pane fade show active" id="subscription">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="subscriptionTable" width="100%"
                             cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Acknow. Receipt No.</th>
                                                <th>Number of Months</th>
                                                <th>Monthly Plan</th>
                                                <th>Total Bill</th>
                                                <th>Transact By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $transactions_query = "SELECT pt.*, 
                                                u.lastname as transact_by_lastname, u.firstname as transact_by_firstname
                                                FROM payment_transactions pt
                                                LEFT JOIN users u ON pt.transact_by = u.user_id
                                                WHERE pt.user_id = ?
                                                ORDER BY pt.transaction_date_time DESC";
                                            $transactions_stmt = $conn->prepare($transactions_query);
                                            $transactions_stmt->bind_param("i", $user_id);
                                            $transactions_stmt->execute();
                                            $transactions_result = $transactions_stmt->get_result();

                                            while ($transaction = $transactions_result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . date('F d, Y | g:i A', strtotime($transaction['transaction_date_time'])) . "</td>";
                                                echo "<td>" . $transaction['acknowledgement_receipt_number'] . "</td>";
                                                echo "<td>" . $transaction['plan_duration_at_transaction'] . "</td>";
                                                echo "<td>" . $transaction['plan_name_at_transaction'] . "</td>";
                                                echo "<td>₱" . number_format($transaction['grand_total'], 2) . "</td>";
                                                echo "<td>" . $transaction['transact_by_lastname'] . ", " . $transaction['transact_by_firstname'] . "</td>";
                                                echo "<td>
                                                        <button type='button' class='btn btn-warning btn-sm view-receipt' onclick='viewReceipt(" . $transaction['payment_transaction_id'] . ")'>
                                                            <i class='fas fa-eye'></i> View Receipt
                                                        </button>
                                                    </td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Attendance Log Tab -->
                            <div class="tab-pane fade" id="attendance">
                                <div class="text-center text-muted my-5">
                                    <i class="fas fa-clock fa-4x mb-3"></i>
                                    <h4>Coming Soon</h4>
                                    <p>Attendance log feature is under development.</p>
                                </div>
                            </div>

                            <!-- Member Details Tab -->
                            <div class="tab-pane fade" id="details">
                                <?php include 'includes/member-details.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="receiptModalLabel">Acknowledgement Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Receipt content will be loaded here via AJAX -->
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p class="mt-2">Loading receipt...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" onclick="printReceipt()">
                        <i class="fas fa-print mr-1"></i>Print
                    </button>
                    <button type="button" class="btn btn-warning" onclick="downloadPDF()">
                        <i class="fas fa-download mr-1"></i>Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            console.log('Document ready');
            
            // Initialize DataTable
            $('#subscriptionTable').DataTable({
                order: [[0, 'desc']]
            });

            // Initialize tabs
            $('a[data-toggle="tab"]').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Show first tab on load
            $('#subscription').addClass('show active');

            // Debug click handler
            $(document).on('click', '.btn-warning.btn-sm', function() {
                console.log('View button clicked');
            });
        });

        function viewReceipt(paymentTransactionId) {
            console.log('viewReceipt called with ID:', paymentTransactionId);
            
            // Show modal with loading spinner
            $('#receiptModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            
            console.log('Making AJAX request...');
            // Load receipt details via AJAX
            $.ajax({
                url: 'indexes/get-receipt-details.php',
                method: 'POST',
                data: { payment_transaction_id: paymentTransactionId },
                dataType: 'html',
                beforeSend: function() {
                    console.log('AJAX request starting...');
                    $('#receiptModal .modal-body').html(`
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin fa-3x"></i>
                            <p class="mt-2">Loading receipt...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    console.log('AJAX request successful');
                    $('#receiptModal').data('payment-transaction-id', paymentTransactionId);
                    $('#receiptModal .modal-body').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    $('#receiptModal .modal-body').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Failed to load receipt details. Please try again.<br>
                            Error: ${error}
                        </div>
                    `);
                }
            });
        }

        function printReceipt() {
            var printContents = document.querySelector('#receiptModal .modal-body').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            
            // Reinitialize the page
            location.reload();
        }

        function downloadPDF() {
            var paymentTransactionId = $('#receiptModal').data('payment-transaction-id');
            window.location.href = 'indexes/download-receipt.php?payment_transaction_id=' + paymentTransactionId;
        }

        function changeStatus(userId, status) {
            Swal.fire({
                title: 'Change Status',
                text: `Are you sure you want to change the status to ${status}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F6C23E',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `indexes/change-member-status.php?user_id=${userId}&status=${status}&redirect=profile`;
                }
            });
        }

        function deleteMember(userId) {
            Swal.fire({
                title: 'Delete Member',
                text: "Are you sure you want to delete this member? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `indexes/delete-member.php?user_id=${userId}&redirect=members`;
                }
            });
        }
    </script>
</body>

</html>