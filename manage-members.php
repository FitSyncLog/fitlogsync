<?php include 'session-management.php'; ?>

<?php
// Check if the user was logged in
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}
// Prepare the query to check permissions
$page_name = "manage-members.php";
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
        <title>Manage All Members | FiT-LOGSYNC</title>
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

        <style>
            .form-check-input[type="radio"] {
                accent-color: #F6C23E;
            }

            /* Custom styles for the tabs */
            .nav-tabs .nav-link {
                color: #F6C23E !important;
                font-weight: bold;
            }

            .nav-tabs .nav-link:hover {
                color: #e0ad2c !important;
            }

            .nav-tabs .nav-link.active {
                color: #F6C23E !important;
                font-weight: bold;
                border-color: #F6C23E #F6C23E #fff;
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
                            <h1 class="h3 mb-0 text-gray-800">Manage All Members</h1>
                            <a class="btn btn-warning" href="create-new-member.php">Add New Member</a>
                        </div>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <ul class="nav nav-tabs" id="memberTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">All Members</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="false">Active</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pending-tab" data-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="false">Pending</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="banned-tab" data-toggle="tab" href="#banned" role="tab" aria-controls="banned" aria-selected="false">Banned</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="suspended-tab" data-toggle="tab" href="#suspended" role="tab" aria-controls="suspended" aria-selected="false">Suspended</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="deleted-tab" data-toggle="tab" href="#deleted" role="tab" aria-controls="deleted" aria-selected="false">Deleted</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="memberTabContent">
                                    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Account Number</th>
                                                        <th class="text-center">Name</th>
                                                        <th class="text-center">Gender</th>
                                                        <th class="text-center">Date of Birth</th>
                                                        <th class="text-center">Account Status</th>
                                                        <th class="text-center">Subscription Status</th>
                                                        <th class="text-center">Registration Date</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>

                                                <?php
                                                $status = isset($_GET['status']) ? $_GET['status'] : 'all';
                                                $query = "SELECT users.*, 
                                                         s.expiration_date,
                                                         pt.plan_name_at_transaction
                                                         FROM users 
                                                         JOIN user_roles ON users.user_id = user_roles.user_id 
                                                         LEFT JOIN (
                                                             SELECT user_id, expiration_date, payment_transaction_id
                                                             FROM subscriptions 
                                                             WHERE (user_id, expiration_date) IN (
                                                                 SELECT user_id, MAX(expiration_date)
                                                                 FROM subscriptions
                                                                 GROUP BY user_id
                                                             )
                                                         ) s ON users.user_id = s.user_id
                                                         LEFT JOIN payment_transactions pt ON s.payment_transaction_id = pt.payment_transaction_id
                                                         WHERE user_roles.role_id = 5";

                                                // Add status condition if not showing all
                                                if ($status !== 'all') {
                                                    if ($status === 'deleted') {
                                                        $query .= " AND users.status = 'Deleted'";
                                                    } else {
                                                        $query .= " AND users.status = '" . mysqli_real_escape_string($conn, ucfirst($status)) . "'";
                                                    }
                                                } else {
                                                    // For 'all' tab, exclude deleted members
                                                    $query .= " AND users.status != 'Deleted'";
                                                }

                                                $result = mysqli_query($conn, $query);

                                                if (!$result) {
                                                    die("Query Failed: " . mysqli_error($conn));
                                                }
                                                ?>

                                                <tbody>
                                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <?php
                                                                $account_number = $row['account_number'];
                                                                $formatted_account = substr($account_number, 0, 4) . '-' .
                                                                    substr($account_number, 4, 4) . '-' .
                                                                    substr($account_number, 8, 4) . '-' .
                                                                    substr($account_number, 12, 4);
                                                                echo htmlspecialchars($formatted_account);
                                                                ?>
                                                            </td>

                                                            <td class="text-center">
                                                                <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?>
                                                            </td>
                                                            <td class="text-center"><?php echo htmlspecialchars($row['gender']); ?></td>
                                                            <td class="text-center">
                                                                <?php
                                                                $dob = $row['date_of_birth'];
                                                                echo htmlspecialchars(date("F j, Y", strtotime($dob)));
                                                                ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php
                                                                $status = htmlspecialchars($row['status']);
                                                                $badgeClass = '';

                                                                switch ($status) {
                                                                    case 'Active':
                                                                        $badgeClass = 'badge-success';
                                                                        break;
                                                                    case 'Pending':
                                                                        $badgeClass = 'badge-warning';
                                                                        break;
                                                                    case 'Suspended':
                                                                    case 'Banned':
                                                                        $badgeClass = 'badge-danger';
                                                                        break;
                                                                    default:
                                                                        $badgeClass = 'badge-light';
                                                                }
                                                                ?>
                                                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php
                                                                $today = new DateTime();
                                                                $subscription_status = "No Active Subscription";
                                                                $badge_class = "badge-secondary";

                                                                if ($row['expiration_date']) {
                                                                    $expiration = new DateTime($row['expiration_date']);
                                                                    if ($today <= $expiration) {
                                                                        $subscription_status = "Active - " . $row['plan_name_at_transaction'] . "<br>(Until " . $expiration->format('M j, Y') . ")";
                                                                        $badge_class = "badge-success";
                                                                    } else {
                                                                        $subscription_status = "Expired - Last Plan: " . $row['plan_name_at_transaction'] . "<br>(Ended " . $expiration->format('M j, Y') . ")";
                                                                        $badge_class = "badge-danger";
                                                                    }
                                                                }
                                                                ?>
                                                                <span class="badge <?php echo $badge_class; ?>">
                                                                    <?php echo $subscription_status; ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php
                                                                $reg_date = $row['registration_date'];
                                                                echo htmlspecialchars(date("F j, Y", strtotime($reg_date)));
                                                                ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="member-profile.php?user_id=<?php echo $row['user_id']; ?>"
                                                                    class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-user mr-1"></i>Select
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="js/demo/datatables-demo.js"></script>

        <script>
            function confirmDelete(userId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the delete-member.php page with the user_id as a query parameter
                        window.location.href = 'indexes/delete-member.php?user_id=' + userId;
                    }
                });
            }

            $(document).ready(function() {
                // Initialize DataTable
                var table = $('#dataTable').DataTable();
                
                // Handle tab clicks
                $('#memberTabs a').on('click', function(e) {
                    e.preventDefault();
                    var status = $(this).attr('href').replace('#', '');
                    
                    // Update URL with status parameter
                    var newUrl = window.location.pathname + '?status=' + status;
                    window.history.pushState({ path: newUrl }, '', newUrl);
                    
                    // Reload the page with new status
                    location.reload();
                });
                
                // Set active tab based on URL parameter
                var urlParams = new URLSearchParams(window.location.search);
                var status = urlParams.get('status') || 'all';
                $('#memberTabs a[href="#' + status + '"]').tab('show');
            });
        </script>

    </body>

    </html>

    <?php
} else {
    header("Location: dashboard.php?AccessDenied=You have no permission to access this page");
    exit();
}
?>