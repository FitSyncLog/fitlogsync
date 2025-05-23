<?php
include 'indexes/db_con.php';

$email = trim($_GET['email'] ?? '');
$token = trim($_GET['token'] ?? '');  

// Debug output
error_log("Verifying token for email: $email");
error_log("Token length: " . strlen($token));

$stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ?");
$stmt->bind_param("ss", $email, $token);

if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
$emailToDisplay = '';

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    error_log("Token match found. DB token length: " . strlen($row['token']));
    $emailToDisplay = htmlspecialchars($email);
} else {
    error_log("No match found. Possible reasons:");
    error_log("- Email doesn't exist");
    error_log("- Token mismatch");
    error_log("- Token truncated in DB (stored length: " . strlen($row['token'] ?? '') . ")");
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password | Fit-LOGSYNC</title>

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
    <?php
    if (isset($_GET['error'])) {
        $message = htmlspecialchars($_GET['error']);
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
                                        <h3><strong>Enter security code</strong></h3>
                                        <p>Please check your email for a message with your code. Your code is 6 numbers
                                            long.</p>
                                        <h5>We sent your code to:</h5>
                                        <p><?php echo htmlspecialchars($emailToDisplay); ?></p>

                                        <hr>
                                    </div>
                                </div>
                            </div>

                            <form id="loginForm" action="indexes/reset-password-verification.php" method="POST">
                                <div class="row gy-3 overflow-hidden">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="resetCode" name="resetCode"
                                                placeholder="Reset Code">
                                            <label for="Reset Code">Reset Code</label>
                                            <div class="error-message" id="emailError"></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-12">
                                        <div class="form-floating position-relative">
                                            <input type="password" class="form-control" id="newPassword"
                                                name="newPassword" placeholder="New Password">
                                            <label for="New Password">New Password</label>
                                            <div class="error-message" id="newPasswordError"></div>
                                            <i class="bi bi-eye-slash position-absolute top-50 translate-middle-y"
                                                id="toggleNewPassword" style="right: 10px; cursor: pointer;"></i>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating position-relative">
                                            <input type="password" class="form-control" id="retypenewPassword"
                                                name="retypenewPassword" placeholder="Retype New Password">
                                            <label for="Retype New Password">Retype New Password</label>
                                            <div class="error-message" id="retypenewPasswordError"></div>
                                            <i class="bi bi-eye-slash position-absolute top-50 translate-middle-y"
                                                id="toggleRetypeNewPassword" style="right: 10px; cursor: pointer;"></i>
                                        </div>
                                    </div>

                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($emailToDisplay); ?>">

                                    <input type="hidden" name="token" value="<?php echo $token ?>">

                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button class="btn btn-warning btn-lg" type="submit"
                                                name="reset">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
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


    <script>
        document.getElementById('loginForm').addEventListener('submit', function (event) {
            let isValid = true;

            // Reset Code Validation
            const resetCode = document.getElementById('resetCode');
            const resetCodeError = document.getElementById('emailError');

            // Reset error messages and styles
            resetCodeError.innerHTML = '';
            resetCode.classList.remove('error');

            // Validate reset code
            if (!resetCode.value) {
                resetCodeError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Reset code is required`;
                resetCode.classList.add('error');
                isValid = false;
            } else if (!/^\d{6}$/.test(resetCode.value)) {
                resetCodeError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Please enter a valid 6-digit reset code`;
                resetCode.classList.add('error');
                isValid = false;
            }

            // New Password Validation
            const newPassword = document.getElementById('newPassword');
            const newPasswordError = document.getElementById('newPasswordError');

            // Reset error messages and styles
            newPasswordError.innerHTML = '';
            newPassword.classList.remove('error');

            // Validate new password
            if (!newPassword.value) {
                newPasswordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> New password is required`;
                newPassword.classList.add('error');
                isValid = false;
            } else if (newPassword.value.length < 8) {
                newPasswordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Password must be at least 8 characters long`;
              newPasswordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Password must contain at least one uppercase letter`;
                newPassword.classList.add('error');
                i  newPassword.classList.add('error');
                isValid = false;
            } else if (!/[A-Z]/.test(newPassword.value)) {
                sValid = false;
            } else if (!/[0-9]/.test(newPassword.value)) {
                newPasswordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Password must contain at least one numeric character`;
                newPassword.classList.add('error');
                isValid = false;
            } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(newPassword.value)) {
                newPasswordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Password must contain at least one special character`;
                newPassword.classList.add('error');
                isValid = false;
            } else {
                newPasswordError.innerHTML = '';
                newPassword.classList.remove('error');
            }

            // Retype New Password Validation
            const retypenewPassword = document.getElementById('retypenewPassword');
            const retypenewPasswordError = document.getElementById('retypenewPasswordError');

            // Reset error messages and styles
            retypenewPasswordError.innerHTML = '';
            retypenewPassword.classList.remove('error');

            // Validate retype new password
            if (!retypenewPassword.value) {
                retypenewPasswordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Retype new password is required`;
                retypenewPassword.classList.add('error');
                isValid = false;
            } else if (newPassword.value !== retypenewPassword.value) {
                retypenewPasswordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Passwords do not match`;
                retypenewPassword.classList.add('error');
                isValid = false;
            } else {
                retypenewPasswordError.innerHTML = '';
                retypenewPassword.classList.remove('error');
            }

            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault(); // Prevent the form from submitting
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const toggleNewPassword = document.getElementById('toggleNewPassword');
            const newPassword = document.getElementById('newPassword');
            const toggleRetypeNewPassword = document.getElementById('toggleRetypeNewPassword');
            const retypenewPassword = document.getElementById('retypenewPassword');

            toggleNewPassword.addEventListener('click', function () {
                const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                newPassword.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });

            toggleRetypeNewPassword.addEventListener('click', function () {
                const type = retypenewPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                retypenewPassword.setAttribute('type', type);
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        });

    </script>



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

</body>

</html>