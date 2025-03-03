<?php include 'session-management.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>My Profile | FiT-LOGSYNC</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="../assets/fitlogsync.ico">
    <link rel="stylesheet" href="../assets/css/sweetalert2.min.css">
    <script src="../assets/js/sweetalert2.all.min.js"></script>
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />

    <script src="../assets/js/sessionExpired.js"></script>

    <style>
        /* Custom switch color */
        .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #F6C23E; /* Change switch color to #F6C23E */
            border-color: #F6C23E; /* Change border color to match */
        }

        .custom-switch .custom-control-input:focus ~ .custom-control-label::before {
            box-shadow: 0 0 0 0.2rem rgba(246, 194, 62, 0.25); /* Add focus shadow */
        }

        .custom-switch .custom-control-label::before {
            background-color: #e9ecef; /* Default off color */
            border-color: #adb5bd; /* Default off border color */
        }

        .custom-switch .custom-control-label::after {
            background-color: #ffffff; /* Switch thumb color */
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
                        <h1 class="h3 mb-0 text-gray-800">Settings</h1>
                    </div>

                    <!-- Main content -->
                    <section class="content">
                        <div class="row justify-content-center">
                            <div class="col-xl-6 col-md-8 mb-4">
                                <div class="card shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="container mt-4">
                                            <h2>Two-Factor Authentication</h2>
                                            <form id="twoFactorForm" method="POST" action="indexes/2fa.php">
                                                <div class="form-group">
                                                    <label for="twoFactorSwitch">Enable Two-Factor Authentication</label>
                                                    <div class="custom-control custom-switch">
                                                        <?php
                                                        $id = $_SESSION['user_id'];
                                                        $query = "SELECT * FROM users WHERE user_id = $id";
                                                        $result = mysqli_query($conn, $query);
                                                        $row = mysqli_fetch_assoc($result)
                                                        ?>
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="twoFactorSwitch" name="twoFactorSwitch" <?php echo $row['two_factor_authentication'] ? 'checked' : ''; ?>>
                                                        <label class="custom-control-label" for="twoFactorSwitch"></label>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-warning" name="2faButton">Save Changes</button>
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

    <!-- Password Verification Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Verify Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="indexes/2fa.php">
                        <div class="form-group">
                            <label for="password">Enter your password to continue:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <input type="hidden" name="twoFactorSwitch" id="modalTwoFactorSwitch" value="">
                        <button type="submit" class="btn btn-warning" name="verifyPassword">Verify</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="../js/demo/datatables-demo.js"></script>

    <script>
    $(document).ready(function() {
        // Show the password modal when the form is submitted
        $('#twoFactorForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting immediately

            // Get the current state of the switch
            var twoFactorSwitch = $('#twoFactorSwitch').is(':checked') ? 1 : 0;

            // Set the value of the hidden input in the modal
            $('#modalTwoFactorSwitch').val(twoFactorSwitch);

            // Show the password modal
            $('#passwordModal').modal('show');
        });
    });
    </script>
</body>

</html>