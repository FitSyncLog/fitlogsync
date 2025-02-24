<?php
session_start();
include "../indexes/db_con.php";

if (isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin') {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Manage Home | FiT-LOGSYNC</title>
        <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="../css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="../assets/fitlogsync.ico">

        <!-- Include SweetAlert2 CSS -->
        <link rel="stylesheet" href="../assets/css/sweetalert2.min.css">

        <!-- Include SweetAlert2 JS -->
        <script src="../assets/js/sweetalert2.all.min.js"></script>
        <style>
            .video-container {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-top: 20px;
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
            <?php include 'layout/super-admin-sidebar.php'; ?>
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <?php include 'layout/super-admin-navbar.php'; ?>
                    <div class="container-fluid">
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">Manage Home</h1>
                        </div>

                        <?php
                        $query = "SELECT description FROM information WHERE information_for = 'home_video'";
                        $result = mysqli_query($conn, $query);
                        if ($result && $row = mysqli_fetch_assoc($result)) {
                            $videoUrl = $row['description'];

                            // Check if the URL is a valid YouTube embed link
                            if (!preg_match('/^https:\/\/www\.youtube\.com\/embed\/[a-zA-Z0-9_-]{11}/', $videoUrl)) {
                                // Fallback to a default YouTube video in embed format
                                $videoUrl = "https://www.youtube.com/embed/xvFZjo5PgG0";
                            }
                        } else {
                            $videoUrl = "https://www.youtube.com/embed/xvFZjo5PgG0";
                        }
                        ?>

                        <div class="video-container">
                            <iframe width="560" height="315" src="<?php echo htmlspecialchars($videoUrl); ?>"
                                frameborder="0" allowfullscreen></iframe>
                        </div>

                        <!-- Centered Button Container -->
                        <div class="d-flex justify-content-center mt-3">
                            <button type="button" class="btn btn-warning" data-toggle="modal"
                                data-target="#changeVideoModal">
                                Change Video
                            </button>
                        </div>
                    </div>

                </div>
            </div>


            <!-- Change Video Modal -->
            <div class="modal fade" id="changeVideoModal" tabindex="-1" role="dialog"
                aria-labelledby="changeVideoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changeVideoModalLabel"><strong>Change Video</strong></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="indexes/manage-home.php" method="POST">
                                <div class="form-group">
                                    <label for="youtubeUrl">New YouTube URL</label>
                                    <input type="text" class="form-control" id="youtubeUrl" name="youtubeUrl"
                                        placeholder="Enter YouTube URL">
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    <button type="submit" class="btn btn-warning">Save changes</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Your Website 2021</span>
                </div>
            </div>
        </footer>
        </div>

        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <script src="../vendor/jquery/jquery.min.js"></script>
        <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="../js/sb-admin-2.min.js"></script>
        <script src="../vendor/chart.js/Chart.min.js"></script>
        <script src="../js/demo/chart-area-demo.js"></script>
        <script src="../js/demo/chart-pie-demo.js"></script>
    </body>

    </html>
    <?php
} else {
    header("Location: ../login.php?LoginFirst=Please login first.");
    exit();
}
?>