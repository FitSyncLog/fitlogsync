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

  <?php
  if (isset($_GET['registrationSuccess'])) {
    $message = htmlspecialchars($_GET['registrationSuccess']);
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
  ?>

  <?php
  if (isset($_GET['registrationFailed'])) {
    $message = htmlspecialchars($_GET['registrationFailed']);
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

  <?php
  if (isset($_GET['UnexpectedError'])) {
    $message = htmlspecialchars($_GET['UnexpectedError']);
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

  <?php
  if (isset($_GET['LoginFirst'])) {
    $message = htmlspecialchars($_GET['LoginFirst']);
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

  <?php
  if (isset($_GET['EmailisnotRegistered'])) {
    $message = htmlspecialchars($_GET['EmailisnotRegistered']);
    echo "<script>
      Swal.fire({
        position: 'center',
        icon: 'error',
        title: '{$message}',
        showConfirmButton: true,
        confirmButtonText: 'Register Now',
        confirmButtonColor: '#ffc107',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'register.php';
        }
      });
  </script>";
  }
  ?>

  <?php
  if (isset($_GET['incorrectPassword'])) {
    $message = htmlspecialchars($_GET['incorrectPassword']);
    echo "<script>
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: '{$message}',
          showConfirmButton: true
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
                    <h3><strong>Login</strong></h3>
                  </div>
                </div>
              </div>

              <form id="loginForm" action="indexes/login.php" method="POST">
                <div class="row gy-3 overflow-hidden">
                  <div class="col-12">
                    <div class="form-floating">
                      <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                        value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                      <label for="email">Email</label>
                      <div class="error-message" id="emailError"></div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-floating">
                      <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                      <label for="password">Password</label>
                      <div class="error-message" id="passwordError"></div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-grid">
                      <button class="btn btn-warning btn-lg" type="submit" name="login">Login</button>
                    </div>
                  </div>
                </div>
              </form>
              <div class="row">
                <div class="col-12">
                  <div class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-end mt-4">
                    <a href="forgot-password.php">Forgot password</a>
                  </div>
                </div>
              </div>
              <!-- <div class="row">
                <div class="col-12">
                  <p class="mt-4 mb-4">Or continue with</p>
                  <div class="col-12">
                    <div class="d-grid">
                      <a href="#" class="btn btn-lg btn-dark mt-2">
                        <img src="assets/google_icon.png" alt="Google Icon" class="google-icon me-2 fs-6"
                          style="width: 1em; height: 1em;">
                        <span class="fs-6 mt-0">Log in with Google</span>
                      </a>
                    </div>
                  </div>
                </div>
              </div> -->
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

      // Get the email and password fields
      const email = document.getElementById('email');
      const password = document.getElementById('password');

      // Get the error message elements
      const emailError = document.getElementById('emailError');
      const passwordError = document.getElementById('passwordError');

      // Reset error messages and styles
      emailError.innerHTML = '';
      email.classList.remove('error');
      passwordError.innerHTML = '';
      password.classList.remove('error');

      // Validate email
      if (!email.value) {
        emailError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Email is required`;
        email.classList.add('error');
        isValid = false;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        emailError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Please enter a valid email address`;
        email.classList.add('error');
        isValid = false;
      }

      // Validate password
      if (!password.value) {
        passwordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Password is required`;
        password.classList.add('error');
        isValid = false;
      }

      // Prevent form submission if validation fails
      if (!isValid) {
        event.preventDefault();
      }
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

  <script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
      let isValid = true;

      // Email Validation
      const email = document.getElementById('email');
      if (!email.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        document.getElementById('emailError').innerHTML = `<i class="bi bi-exclamation-circle"></i> Please enter a valid email address`;
        email.classList.add('error');
        isValid = false;
      } else {
        document.getElementById('emailError').innerHTML = '';
        email.classList.remove('error');
      }

      // Password Validation
      const password = document.getElementById('password');
      if (!password.value) {
        document.getElementById('passwordError').innerHTML = `<i class="bi bi-exclamation-circle"></i> Password is required`;
        password.classList.add('error');
        isValid = false;
      } else {
        document.getElementById('passwordError').innerHTML = '';
        password.classList.remove('error');
      }

      if (!isValid) {
        event.preventDefault();
      }
    });
  </script>

</body>

</html>
