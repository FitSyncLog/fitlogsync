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
                                                WHERE role_id = 5";
                                                    $result_members = mysqli_query($conn, $sql_members);
                                                    $row_members = mysqli_fetch_assoc($result_members);
                                                    echo $row_members['total_members'];
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Instructor -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Instructors
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-instructor">
                                                    <?php
                                                    $sql_members = "SELECT COUNT(*) AS total_members
                                                FROM user_roles
                                                WHERE role_id = 4";
                                                    $result_members = mysqli_query($conn, $sql_members);
                                                    $row_members = mysqli_fetch_assoc($result_members);
                                                    echo $row_members['total_members'];
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        <script src="js/demo/chart-area-demo.js"></script>
        <script src="js/demo/chart-pie-demo.js"></script>

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