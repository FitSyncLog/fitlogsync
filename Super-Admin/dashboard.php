<?php include 'session-management.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard | FiT-LOGSYNC</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="../assets/fitlogsync.ico">

    <script src="../assets/js/sessionExpired.js"></script>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include 'layout/super-admin-sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include 'layout/super-admin-navbar.php'; ?>

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
                                                $sql_members = "SELECT COUNT(*) AS total_members FROM user_roles WHERE role = 'Member'";
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

                        <!-- Total Instructors -->
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
                                                $sql_instructor = "SELECT COUNT(*) AS total_members FROM user_roles WHERE role = 'Instructor'";
                                                $result_instructor = mysqli_query($conn, $sql_instructor);
                                                $row_instructor = mysqli_fetch_assoc($result_instructor);
                                                echo $row_instructor['total_members'];
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

                        <!-- Active Members Today -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Active Subscription Today
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-member">
                                                <?php
                                                $current_date = date("Y-m-d");
                                                $sub_query = "SELECT user_id FROM subscription WHERE starting_date <= '$current_date' AND expiration_date >= '$current_date'";
                                                $sub_result = mysqli_query($conn, $sub_query);
                                                echo mysqli_num_rows($sub_result);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Visit -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Visit
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-visit">
                                                <?php
                                                $today = date("Y-m-d");
                                                $query = "SELECT COUNT(*) AS total_in FROM attendance_log WHERE transaction_type = 'IN' AND DATE(transaction_time) = '$today'";
                                                $result = mysqli_query($conn, $query);
                                                $row = mysqli_fetch_assoc($result);
                                                echo $row['total_in'];
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shoe-prints fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

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
                weekday: 'long', year: 'numeric', month: 'long',
                day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'
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
                        document.getElementById('active-member').innerText = data.activeMember;
                        document.getElementById('total-visit').innerText = data.totalVisit;
                    } else {
                        console.error(data.error);
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        setInterval(fetchDashboardData, 1000);

        // Initial fetch
        fetchDashboardData();
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../js/demo/chart-area-demo.js"></script>
    <script src="../js/demo/chart-pie-demo.js"></script>

</body>

</html>