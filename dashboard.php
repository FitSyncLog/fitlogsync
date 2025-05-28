<?php include 'session-management.php'; ?>
<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "dashboard.php";
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
        <title>Dashboard | FiT-LOGSYNC</title>
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
        <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
        <script src="assets/js/sweetalert2.all.min.js"></script>
        <script src="assets/js/sessionExpired.js"></script>




    </head>

    <body id="page-top">
        <?php
        if (isset($_GET['AccessDenied'])) {
            $message = htmlspecialchars($_GET['AccessDenied']);
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

        <!-- Page Wrapper -->
        <div id="wrapper">

            <?php include 'layout/sidebar.php'; ?>

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Main Content -->
                <div id="content">

                    <?php include 'layout/navbar.php'; ?>

                    <!-- Begin Page Content -->
                    <div class="container-fluid">

                        <!-- Page Heading -->
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                            <div class="text-right">
                                <p class="text-gray-600" id="currentDateTime"></p>
                            </div>
                        </div>

                        <!-- Content Row -->
                        <div class="row">
                            <?php
                            $role_id = $_SESSION['role_id'];

                            if ($role_id == 1 || $role_id == 2 || $role_id == 3) {
                                // Set timezone to Manila
                                date_default_timezone_set('Asia/Manila');
                                $today = date('Y-m-d');
                                $first_day_of_month = date('Y-m-01');
                                $last_day_of_month = date('Y-m-t');
                                ?>
                                <!-- Total Members -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Total Members
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-members">
                                                        <?php
                                                        $sql_members = "SELECT COUNT(*) AS total_members
                                                                       FROM user_roles
                                                                       JOIN users ON users.user_id = user_roles.user_id
                                                                       WHERE user_roles.role_id = 5
                                                                       AND users.status = 'Active'";
                                                        $result_members = mysqli_query($conn, $sql_members);
                                                        $row_members = mysqli_fetch_assoc($result_members);
                                                        echo $row_members['total_members'];
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Subscriptions -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        Active Subscriptions
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-subscriptions">
                                                        <?php
                                                        $sql_active = "SELECT COUNT(DISTINCT user_id) AS active_subs 
                                                                      FROM subscriptions 
                                                                      WHERE ? BETWEEN starting_date AND expiration_date";
                                                        $stmt = $conn->prepare($sql_active);
                                                        $stmt->bind_param("s", $today);
                                                        $stmt->execute();
                                                        $result_active = $stmt->get_result();
                                                        $row_active = $result_active->fetch_assoc();
                                                        echo $row_active['active_subs'];
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Expired Subscriptions -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-danger shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                        Expired Subscriptions
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="expired-subscriptions">
                                                        <?php
                                                        $sql_expired = "SELECT COUNT(DISTINCT user_id) AS expired_subs 
                                                                       FROM subscriptions 
                                                                       WHERE expiration_date < ?";
                                                        $stmt = $conn->prepare($sql_expired);
                                                        $stmt->bind_param("s", $today);
                                                        $stmt->execute();
                                                        $result_expired = $stmt->get_result();
                                                        $row_expired = $result_expired->fetch_assoc();
                                                        echo $row_expired['expired_subs'];
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Revenue This Month -->
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Revenue This Month
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthly-revenue">
                                                        <?php
                                                        $sql_revenue = "SELECT COALESCE(SUM(grand_total), 0) as monthly_revenue 
                                                                      FROM payment_transactions 
                                                                      WHERE transaction_date_time BETWEEN ? AND ?";
                                                        $stmt = $conn->prepare($sql_revenue);
                                                        $stmt->bind_param("ss", $first_day_of_month, $last_day_of_month);
                                                        $stmt->execute();
                                                        $result_revenue = $stmt->get_result();
                                                        $row_revenue = $result_revenue->fetch_assoc();
                                                        echo '₱ ' . number_format($row_revenue['monthly_revenue'], 2);
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

                        <?php if ($role_id == 1 || $role_id == 2 || $role_id == 3) { ?>
                        <!-- Content Row -->
                        <div class="row">
                            <!-- Monthly Revenue Chart -->
                            <div class="col-xl-8 col-lg-7">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-area">
                                            <canvas id="revenueChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- New Members Chart -->
                            <div class="col-xl-4 col-lg-5">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">New Members per Month</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-pie">
                                            <canvas id="newMembersChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Heatmap -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">Gym Usage Heatmap</h6>
                                        <span class="badge badge-warning">Coming Soon</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center text-gray-500 py-5">
                                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                                            <p>Attendance heatmap feature is currently under development.</p>
                                            <p>This will show peak gym usage hours to help members plan their visits.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <!-- virtual card -->
                        <?php
                        if ($role_id == 5) {
                            $accountNumber = $_SESSION['account_number'];
                            $user_id = $_SESSION['user_id'];

                            // Get current subscription info
                            $sql_subscription = "SELECT s.*, pt.*, p.plan_name 
                                              FROM subscriptions s
                                              JOIN payment_transactions pt ON s.payment_transaction_id = pt.payment_transaction_id
                                              JOIN plans p ON pt.plan_id = p.plan_id
                                              WHERE s.user_id = ? 
                                              AND CURDATE() BETWEEN s.starting_date AND s.expiration_date";
                            $stmt = $conn->prepare($sql_subscription);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $subscription_result = $stmt->get_result();
                            $subscription = $subscription_result->fetch_assoc();

                            // For debugging
                            if (!$subscription) {
                                // Check if there's any subscription at all
                                $debug_query = "SELECT s.*, pt.*, p.plan_name 
                                             FROM subscriptions s
                                             JOIN payment_transactions pt ON s.payment_transaction_id = pt.payment_transaction_id
                                             JOIN plans p ON pt.plan_id = p.plan_id
                                             WHERE s.user_id = ?";
                                $debug_stmt = $conn->prepare($debug_query);
                                $debug_stmt->bind_param("i", $user_id);
                                $debug_stmt->execute();
                                $debug_result = $debug_stmt->get_result();
                                if ($debug_row = $debug_result->fetch_assoc()) {
                                    // Log the dates for debugging
                                    error_log("User ID: " . $user_id);
                                    error_log("Current Date: " . date('Y-m-d'));
                                    error_log("Start Date: " . $debug_row['starting_date']);
                                    error_log("End Date: " . $debug_row['expiration_date']);
                                }
                            }
                            ?>
                            <!-- Subscription Status -->
                            <div class="row mb-4">
                                <!-- Subscription Timer Card -->
                                <div class="col-xl-6 col-lg-6">
                                    <div class="card border-left-warning shadow h-100">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Subscription Timer</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <?php if ($subscription) { 
                                                $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
                                                $expiry = new DateTime($subscription['expiration_date'], new DateTimeZone('Asia/Manila'));
                                                $diff = $now->diff($expiry);
                                                $days_remaining = $diff->days;
                                                $is_expired = $now > $expiry;
                                            ?>
                                                <div class="display-4 font-weight-bold <?= $is_expired ? 'text-danger' : 'text-success' ?>">
                                                    <?= $days_remaining ?> Days
                                                </div>
                                                <p class="mt-2"><?= $is_expired ? 'EXPIRED' : 'Remaining on your subscription' ?></p>
                                                <div class="progress mt-3">
                                                    <?php
                                                    $start = new DateTime($subscription['starting_date'], new DateTimeZone('Asia/Manila'));
                                                    $total_days = $start->diff($expiry)->days;
                                                    $progress = $is_expired ? 0 : (($total_days - $days_remaining) / $total_days * 100);
                                                    ?>
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $progress ?>%"></div>
                                                </div>
                                            <?php } else { 
                                                // Additional debug information
                                                error_log("No active subscription found for user: " . $user_id);
                                            ?>
                                                <div class="text-center py-4">
                                                    <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                                    <h5 class="text-danger">No Active Subscription</h5>
                                                    <p>Please visit the front desk to start your subscription.</p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Subscription Details -->
                                <div class="col-xl-6 col-lg-6">
                                    <div class="card border-left-warning shadow h-100">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Current Subscription</h6>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($subscription) { ?>
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                            Subscription Status
                                                        </div>
                                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Active</div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <i class="fas fa-check-circle fa-2x text-success"></i>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Start Date:</strong><br>
                                                            <?= date('F d, Y', strtotime($subscription['starting_date'])) ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>End Date:</strong><br>
                                                            <?= date('F d, Y', strtotime($subscription['expiration_date'])) ?></p>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-12">
                                                        <p class="mb-1"><strong>Days Remaining:</strong><br>
                                                            <?= $days_remaining ?> days
                                                        </p>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                            Subscription Status
                                                        </div>
                                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Inactive</div>
                                                        <p class="mt-2">Please visit the front desk to renew your subscription.</p>
                                                    </div>
                                                    <div class="col-auto">
                                                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Virtual Card Section -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Virtual Membership Card</h6>
                                        </div>
                                        <div class="card-body">
                                            <style>
                                                .virtual-card-container {
                                                    perspective: 1000px;
                                                    width: 100%;
                                                    max-width: 500px;
                                                    height: 280px;
                                                    margin: 20px auto;
                                                }

                                                .virtual-card {
                                                    position: relative;
                                                    width: 100%;
                                                    height: 100%;
                                                    transform-style: preserve-3d;
                                                    transition: transform 0.6s;
                                                    cursor: pointer;
                                                }

                                                .virtual-card.flipped {
                                                    transform: rotateY(180deg);
                                                }

                                                .virtual-card-front,
                                                .virtual-card-back {
                                                    position: absolute;
                                                    width: 100%;
                                                    height: 100%;
                                                    backface-visibility: hidden;
                                                    display: flex;
                                                    justify-content: center;
                                                    align-items: center;
                                                    border-radius: 10px;
                                                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                                    background-color: white;
                                                }

                                                .virtual-card-back {
                                                    transform: rotateY(180deg);
                                                }

                                                .virtual-card img {
                                                    max-width: 100%;
                                                    max-height: 100%;
                                                    object-fit: contain;
                                                }
                                            </style>
                                            <div class="virtual-card-container">
                                                <div class="virtual-card" id="virtualCard">
                                                    <div class="virtual-card-front">
                                                        <img src="assets/access-card/<?php echo $accountNumber; ?>-accesscard.jpg"
                                                            alt="Front Card">
                                                    </div>
                                                    <div class="virtual-card-back">
                                                        <img src="assets/access-card-back/<?php echo $accountNumber; ?>-qr.png"
                                                            alt="Back Card">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center mt-3">
                                                <p class="text-muted">Click the card to flip</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                document.getElementById('virtualCard').addEventListener('click', function() {
                                    this.classList.toggle('flipped');
                                });
                            </script>
                        <?php } ?>
                    </div>
                    <!-- /.container-fluid -->
                </div>
                <!-- End of Main Content -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- JavaScript for Dynamic Date and Time -->
        <script>
            function updateDateTime() {
                const now = new Date();
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                const formattedDate = now.toLocaleDateString('en-US', options);
                document.getElementById('currentDateTime').innerText = formattedDate;
            }

            // Initial call
            updateDateTime();

            // Update every second
            setInterval(updateDateTime, 1000);
        </script>

        <!-- JavaScript for Live Updates -->
        <script>
            function fetchDashboardData() {
                fetch('fetch_dashboard_data.php')
                    .then(response => response.json())
                    .then(data => {
                        if (!data.error) {
                            // Update the DOM with new data
                            document.getElementById('total-members').innerText = data.total_members;
                            document.getElementById('total-instructor').innerText = data.total_instructor;
                        } else {
                            console.error(data.error);
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            setInterval(fetchDashboardData, 5000); // Update every 5 seconds

            // Initial fetch
            fetchDashboardData();
        </script>

        <!-- Core JavaScript Files -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>

        <!-- Page Level Plugins -->
        <script src="vendor/chart.js/Chart.min.js"></script>

        <!-- Page Level Custom Scripts -->
        <script>
            function number_format(number, decimals, dec_point, thousands_sep) {
                // *     example: number_format(1234.56, 2, ',', ' ');
                // *     return: '1 234,56'
                number = (number + '').replace(',', '').replace(' ', '');
                var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                    s = '',
                    toFixedFix = function(n, prec) {
                        var k = Math.pow(10, prec);
                        return '' + Math.round(n * k) / k;
                    };
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1).join('0');
                }
                return s.join(dec);
            }
        </script>
        <script src="js/demo/dashboard-charts.js"></script>

        <!-- Initialize Bootstrap dropdowns -->
        <script>
            $(document).ready(function () {
                // Enable all dropdown toggles
                $('.dropdown-toggle').dropdown();

                // Initialize tooltips if needed
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>

    </body>

    </html>
    <?php
} else {
    header("Location: indexes/logout.php");
    exit();
}