<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
    echo "<script>
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: '{$error}',
          showConfirmButton: true
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Fit-LOGSYNC</title>

    <link rel="stylesheet" type="text/css" href="bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">

    <script src="assets/css/sweetalert2.min.css"></script>
    <script src="assets/js/sweetalert2.all.min.js"></script>
    <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">

    <style>
        .form-check-input:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .error {
            border: 1px solid #DC3545 !important;
        }

        .error-message {
            color: #DC3545;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>

    <?php include 'layout/index_header.php'; ?>

    <section class="hero section light-background py-3 py-md-5 py-xl-8">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-12 col-md-6 col-xl-7">
                </div>
                <div class="col-12 col-md-6 col-xl-5">
                    <div class="card border-0 rounded-4">
                        <div class="card-body p-3 p-md-4 p-xl-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-4">
                                        <h3><strong>LOGIN VERIFICATION</strong></h3>
                                        <p>The OTP code was sent to your email.</p>
                                    </div>
                                </div>
                            </div>

                            <form id="loginForm" action="indexes/verify-login.php" method="POST">
                                <div class="row gy-3 overflow-hidden">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="otp_code" name="otp_code"
                                                placeholder="OTP Code">
                                            <label for="otp_code">OTP Code</label>
                                            <div class="error-message" id="otpError"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button class="btn btn-warning btn-lg" type="submit"
                                                name="login">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'layout/footer.php'; ?>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (event) {
            let isValid = true;

            // Get the OTP code field
            const otpCode = document.getElementById('otp_code');

            // Get the error message element
            const otpError = document.getElementById('otpError');

            // Reset error messages and styles
            otpError.innerHTML = '';
            otpCode.classList.remove('error');

            // Validate OTP code
            if (!otpCode.value) {
                otpError.innerHTML = '<i class="bi bi-exclamation-circle"></i> OTP Code is required';
                otpCode.classList.add('error');
                isValid = false;
            }

            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'OTP Code is required',
                    showConfirmButton: true
                });
            }
        });
    </script>

</body>

</html>