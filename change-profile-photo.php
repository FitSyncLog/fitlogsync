<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "profile.php";
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
        <title>My Profile | FiT-LOGSYNC</title>
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
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
                            <h1 class="h3 mb-0 text-gray-800">Profile Photo</h1>
                        </div>

                        <!-- Main content -->
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <div class="col-md-5">
                                        <div class="card card-primary card-outline">
                                            <div class="card-body box-profile">
                                                <div class="row justify-content-center">
                                                    <div class="text-center mb-3">
                                                        <img id="previewImage"
                                                            class="profile-picture img-fluid rounded-circle"
                                                            style="width: 400px; height: 400px; object-fit: cover; border-radius: 50%;"
                                                            src="assets/profile-pictures/<?php echo $_SESSION['profile_image']; ?>?<?php echo time(); ?>"
                                                            alt="User profile picture">


                                                    </div>
                                                </div>

                                                <form action="indexes/upload-prfile-photo.php" method="POST"
                                                    enctype="multipart/form-data" class="text-center">
                                                    <div class="input-group mb-3">
                                                        <input type="file" class="form-control" name="profile_image"
                                                            id="inputGroupFile02" accept="image/*" required>
                                                    </div>
                                                    <button class="btn btn-warning" type="submit" name="upload">Change
                                                        Photo</button>
                                                </form>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

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
            document.getElementById('inputGroupFile02').addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const preview = document.getElementById('previewImage');
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>

    </body>

    </html>

    <?php
} else {
    header("Location: dashboard.php?AccessDenied=You have no permission to access this page");
    exit();
}