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

  <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">

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
                    <h3><strong>Login</strong></h3>
                  </div>
                </div>
              </div>

              <?php if (isset($_GET['registrationfailed'])): ?>
                <div class="alert alert-warning">
                  <?php echo htmlspecialchars($_GET['registrationfailed']); ?>
                </div>
              <?php endif; ?>

              <?php if (isset($_GET['registrationSuccess'])): ?>
                <div class="alert alert-success">
                  <?php echo htmlspecialchars($_GET['registrationSuccess']); ?>
                </div>
              <?php endif; ?>

              <form action="#!">
                <div class="row gy-3 overflow-hidden">
                  <div class="col-12">
                    <div class="form-floating mb-3">
                      <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com"
                        required>
                      <label for="email" class="form-label">Email</label>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-floating mb-3">
                      <input type="password" class="form-control" name="password" id="password" value=""
                        placeholder="Password" required>
                      <label for="password" class="form-label">Password</label>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-grid">
                      <button class="btn btn-warning btn-lg" type="submit">Login</button>
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
              <div class="row">
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