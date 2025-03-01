<?php
// Start the session and include the database connection
session_start();
include "indexes/db_con.php";

$query = "SELECT description FROM information WHERE information_for = 'home_video'";
$result = mysqli_query($conn, $query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    $home_video = $row['description'];
} else {
    $home_video = "https://www.youtube.com/watch?v=xvFZjo5PgG0&ab_channel=Duran";
}

$sql = "SELECT * FROM information";
$result = $conn->query($sql);

$info = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $info[$row['information_for']] = $row['description'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>FiT-LOGSYNC</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

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
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <script src="assets/js/sweetalert2.all.min.js"></script>

    <style>
        .error-message {
            color: red;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .error-message i {
            margin-right: 0.25rem;
        }

        .form-control.error {
            border: 1px solid red;
            outline: red;
        }
    </style>

</head>

<body class="index-page">

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

    <?php include 'layout/index_header.php'; ?>

    <main class="main">

        <!-- Hero Section -->
        <section id="welcome" class="hero section light-background">

            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-8 order-2 order-lg-1 d-flex flex-column justify-content-center"
                        data-aos="zoom-out">

                        <h1>Welcome to <span>FiT-LOGSYNC</span></h1>
                        <p>Fitness Gym Membership and Attendance Log Management System</p>
                        <div class="d-flex">
                            <a href="register.php" class="btn-get-started">Register Now</a>
                            <a href="<?php echo htmlspecialchars($home_video); ?>"
                                class="glightbox btn-watch-video d-flex align-items-center"><i
                                    class="bi bi-play-circle"></i><span>Watch Video</span></a>
                        </div>
                    </div>
                </div>
            </div>

        </section><!-- /Hero Section -->

        <!-- About Section -->
        <section id="about" class="about section light-background">
            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>About</h2>
                <p><span>Find Out More</span> <span class="description-title">About Us</span></p>
            </div><!-- End Section Title -->

            <div class="container">
                <div class="row gy-3 align-items-stretch"> <!-- Added align-items-stretch -->
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <img src="assets/img/about.jpg" alt="" class="img-fluid h-100"> <!-- Added h-100 -->
                    </div>

                    <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up"
                        data-aos-delay="200">
                        <div class="about-content ps-0 ps-lg-3">
                            <h3>FiT-LOGSYNC is an all-in-one Fitness Gym Membership and Attendance Log Management System
                            </h3>
                            <p class="fst-italic">
                                designed to simplify and streamline gym operations. Whether you're a gym owner, trainer,
                                or fitness enthusiast, <strong>FiT-LOGSYNC</strong> provides a seamless way to manage
                                memberships, track attendance, and boost overall gym efficiency.
                            </p>
                            <ul>
                                <li>
                                    <a href="#about">
                                        <i class="bi bi-browser-chrome" href="#"></i>
                                    </a>
                                    <a href="#about">
                                        <i class="bi bi-phone-fill"></i>
                                    </a>
                                    <a href="#about">
                                        <i class="bi bi-windows"></i>
                                    </a>
                                </li>
                                <p>Available in any Web Browsers, Android Devices, and Windows Computers</p>
                            </ul>
                            <p>
                                Our cross-platform solution works flawlessly on web, mobile, and desktop, giving both
                                staff and members the flexibility to access the system anytime, anywhere. We aim to
                                eliminate the hassle of manual logbooks and outdated systems, making gym management
                                faster, easier, and more organized.
                            </p>
                            <p>
                                At <strong>FiT-LOGSYNC</strong>, we're committed to helping fitness centers focus on
                                what truly matters—helping their members achieve their health and fitness goals—while we
                                handle the
                                backend work.
                            </p>
                            <p>
                                <strong>Stay fit. Stay synced. Anytime, anywhere. 💪✨</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- /About Section -->


        <!-- Stats Section -->
        <section id="stats" class="stats section">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4">

                    <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-emoji-smile"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>Happy Clients</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-journal-richtext"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>Projects</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-headset"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="1463" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>Hours Of Support</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6 d-flex flex-column align-items-center">
                        <i class="bi bi-people"></i>
                        <div class="stats-item">
                            <span data-purecounter-start="0" data-purecounter-end="15" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>Hard Workers</p>
                        </div>
                    </div><!-- End Stats Item -->
                </div>
            </div>

        </section><!-- /Stats Section -->

        <!-- Services Section -->
        <section id="facilities" class="services section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Facilities and Amenities</h2>
                <p><span>Check Our</span> <span class="description-title">Facilities and Amenities</span></p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row gy-4">

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-qr-code"></i>
                            </div>
                            <a class="stretched-link">
                                <h3>QR Code-Based Log</h3>
                            </a>
                            <p>Our QR Code-Based Log system makes check-ins quick and contactless. Simply scan your
                                unique QR code at the entrance to log your attendance.</p>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-wifi"></i>
                            </div>
                            <a class="stretched-link">
                                <h3>Free Wi-Fi Access</h3>
                            </a>
                            <p>Stay connected while you work out! Enjoy complimentary high-speed Wi-Fi throughout the
                                gym, perfect for streaming music, following workout videos, or staying in touch.
                            </p>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-droplet-fill"></i>
                            </div>
                            <a class="stretched-link">
                                <h3>Water Refill Station
                                </h3>
                            </a>
                            <p>Stay hydrated with access to our filtered water refill stations, so you can refill your
                                bottles anytime during your workout.</p>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-file-lock-fill"></i>
                            </div>
                            <a class="stretched-link">
                                <h3>Locker Rooms</h3>
                            </a>
                            <p>Keep your belongings safe in our secure locker rooms, equipped with spacious lockers,
                                benches, and changing areas for your convenience.</p>
                            <a class="stretched-link"></a>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-water"></i>
                            </div>
                            <a class="stretched-link">
                                <h3>Shower Facilities</h3>
                            </a>
                            <p>Freshen up post-workout in our clean and well-maintained showers. We ensure a hygienic
                                and comfortable space so you can leave the gym feeling revitalized.</p>
                            <a class="stretched-link"></a>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-flower1"></i>
                            </div>
                            <a class="stretched-link">
                                <h3>Dedicated Yoga Area</h3>
                            </a>
                            <p>Find your balance and inner peace with our Yoga classes, designed to improve flexibility,
                                strength, and mindfulness. Suitable for all levels, from beginners to seasoned yogis.
                            </p>
                            <a class="stretched-link"></a>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-lightning"></i>
                            </div>
                            <a class="stretched-link">
                                <h3>Professional Boxing Ring</h3>
                            </a>
                            <p>Unleash your power in our Boxing sessions! Build strength, improve endurance, and master
                                techniques while burning calories in a high-energy environment.
                            </p>
                            <a class="stretched-link"></a>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <a class="stretched-link">
                                <h3>Unlimited Gym Access</h3>
                            </a>
                            <p>Enjoy the freedom to work out anytime during operating hours with no limits on your
                                visits.
                            </p>
                            <a class="stretched-link"></a>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="service-item position-relative">
                            <div class="icon">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <a class="stretched-link">
                                <h3>Professional & Friendly Staff</h3>
                            </a>
                            <p>Our certified trainers and approachable staff are here to guide and support you every step of the way.
                            </p>
                            <a class="stretched-link"></a>
                        </div>
                    </div><!-- End Service Item -->

                </div>

            </div>

        </section><!-- /Services Section -->

        <!-- Testimonials Section -->
        <section id="testimonials" class="testimonials section dark-background">

            <img src="assets/img/testimonials-bg.jpg" class="testimonials-bg" alt="">

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="swiper init-swiper">
                    <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              }
            }
          </script>
                    <div class="swiper-wrapper">

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <img src="assets/img/testimonials/testimonials-1.jpg" class="testimonial-img" alt="">
                                <h3>Saul Goodman</h3>
                                <h4>Ceo &amp; Founder</h4>
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Proin iaculis purus consequat sem cure digni ssim donec porttitora entum
                                        suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et.
                                        Maecen aliquam, risus at semper.</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <img src="assets/img/testimonials/testimonials-2.jpg" class="testimonial-img" alt="">
                                <h3>Sara Wilsson</h3>
                                <h4>Designer</h4>
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Export tempor illum tamen malis malis eram quae irure esse labore quem cillum
                                        quid cillum eram malis quorum velit fore eram velit sunt aliqua noster fugiat
                                        irure amet legam anim culpa.</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <img src="assets/img/testimonials/testimonials-3.jpg" class="testimonial-img" alt="">
                                <h3>Jena Karlis</h3>
                                <h4>Store Owner</h4>
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Enim nisi quem export duis labore cillum quae magna enim sint quorum nulla
                                        quem veniam duis minim tempor labore quem eram duis noster aute amet eram fore
                                        quis sint minim.</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <img src="assets/img/testimonials/testimonials-4.jpg" class="testimonial-img" alt="">
                                <h3>Matt Brandon</h3>
                                <h4>Freelancer</h4>
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Fugiat enim eram quae cillum dolore dolor amet nulla culpa multos export minim
                                        fugiat minim velit minim dolor enim duis veniam ipsum anim magna sunt elit fore
                                        quem dolore labore illum veniam.</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <img src="assets/img/testimonials/testimonials-5.jpg" class="testimonial-img" alt="">
                                <h3>John Larson</h3>
                                <h4>Entrepreneur</h4>
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Quis quorum aliqua sint quem legam fore sunt eram irure aliqua veniam tempor
                                        noster veniam enim culpa labore duis sunt culpa nulla illum cillum fugiat legam
                                        esse veniam culpa fore nisi cillum quid.</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>
                        </div><!-- End testimonial item -->

                    </div>
                    <div class="swiper-pagination"></div>
                </div>

            </div>

        </section><!-- /Testimonials Section -->



        <!-- instructors Section -->
        <section id="instructors" class="instructors section light-background">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>instructors</h2>
                <p><span>Our Hardworking</span> <span class="description-title">Instructors</span></p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row gy-4">

                    <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
                        <div class="team-member">
                            <div class="member-img">
                                <img src="assets/img/team/team-1.jpg" class="img-fluid" alt="">
                                <div class="social">
                                    <a href=""><i class="bi bi-twitter-x"></i></a>
                                    <a href=""><i class="bi bi-facebook"></i></a>
                                    <a href=""><i class="bi bi-instagram"></i></a>
                                    <a href=""><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>Walter White</h4>
                                <span>Chief Executive Officer</span>
                            </div>
                        </div>
                    </div><!-- End instructors Member -->

                    <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
                        <div class="team-member">
                            <div class="member-img">
                                <img src="assets/img/team/team-2.jpg" class="img-fluid" alt="">
                                <div class="social">
                                    <a href=""><i class="bi bi-twitter-x"></i></a>
                                    <a href=""><i class="bi bi-facebook"></i></a>
                                    <a href=""><i class="bi bi-instagram"></i></a>
                                    <a href=""><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>Sarah Jhonson</h4>
                                <span>Product Manager</span>
                            </div>
                        </div>
                    </div><!-- End instructors Member -->

                    <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="300">
                        <div class="team-member">
                            <div class="member-img">
                                <img src="assets/img/team/team-3.jpg" class="img-fluid" alt="">
                                <div class="social">
                                    <a href=""><i class="bi bi-twitter-x"></i></a>
                                    <a href=""><i class="bi bi-facebook"></i></a>
                                    <a href=""><i class="bi bi-instagram"></i></a>
                                    <a href=""><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>William Anderson</h4>
                                <span>CTO</span>
                            </div>
                        </div>
                    </div><!-- End instructors Member -->

                    <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="400">
                        <div class="team-member">
                            <div class="member-img">
                                <img src="assets/img/team/team-4.jpg" class="img-fluid" alt="">
                                <div class="social">
                                    <a href=""><i class="bi bi-twitter-x"></i></a>
                                    <a href=""><i class="bi bi-facebook"></i></a>
                                    <a href=""><i class="bi bi-instagram"></i></a>
                                    <a href=""><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="member-info">
                                <h4>Amanda Jepson</h4>
                                <span>Accountant</span>
                            </div>
                        </div>
                    </div><!-- End instructors Member -->

                </div>

            </div>

        </section><!-- /instructors Section -->

        <!-- Pricing Section -->
        <section id="pricing" class="pricing section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Pricing</h2>
                <p><span>Check our</span> <span class="description-title">Pricing</span></p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row gy-3">

                    <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="pricing-item">
                            <h3>Free</h3>
                            <h4><sup>$</sup>0<span> / month</span></h4>
                            <ul>
                                <li>Aida dere</li>
                                <li>Nec feugiat nisl</li>
                                <li>Nulla at volutpat dola</li>
                                <li class="na">Pharetra massa</li>
                                <li class="na">Massa ultricies mi</li>
                            </ul>
                            <div class="btn-wrap">
                                <a class="btn-buy">Buy Now</a>
                            </div>
                        </div>
                    </div><!-- End Pricing Item -->

                    <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="pricing-item featured">
                            <h3>Business</h3>
                            <h4><sup>$</sup>19<span> / month</span></h4>
                            <ul>
                                <li>Aida dere</li>
                                <li>Nec feugiat nisl</li>
                                <li>Nulla at volutpat dola</li>
                                <li>Pharetra massa</li>
                                <li class="na">Massa ultricies mi</li>
                            </ul>
                            <div class="btn-wrap">
                                <a class="btn-buy">Buy Now</a>
                            </div>
                        </div>
                    </div><!-- End Pricing Item -->

                    <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="pricing-item">
                            <h3>Developer</h3>
                            <h4><sup>$</sup>29<span> / month</span></h4>
                            <ul>
                                <li>Aida dere</li>
                                <li>Nec feugiat nisl</li>
                                <li>Nulla at volutpat dola</li>
                                <li>Pharetra massa</li>
                                <li>Massa ultricies mi</li>
                            </ul>
                            <div class="btn-wrap">
                                <a class="btn-buy">Buy Now</a>
                            </div>
                        </div>
                    </div><!-- End Pricing Item -->

                    <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="pricing-item">
                            <span class="advanced">Advanced</span>
                            <h3>Ultimate</h3>
                            <h4><sup>$</sup>49<span> / month</span></h4>
                            <ul>
                                <li>Aida dere</li>
                                <li>Nec feugiat nisl</li>
                                <li>Nulla at volutpat dola</li>
                                <li>Pharetra massa</li>
                                <li>Massa ultricies mi</li>
                            </ul>
                            <div class="btn-wrap">
                                <a class="btn-buy">Buy Now</a>
                            </div>
                        </div>
                    </div><!-- End Pricing Item -->

                </div>

            </div>

        </section><!-- /Pricing Section -->

        <!-- Faq Section -->
        <section id="faq" class="faq section light-background">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>F.A.Q</h2>
                <p><span>Frequently Asked</span> <span class="description-title">Questions</span></p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row justify-content-center">

                    <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

                        <div class="faq-container">

                            <?php
                            $sql = "SELECT id, question, answer FROM faq";
                            $result = $conn->query($sql);
                            ?>
                            <div class="faq-container">
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <div class="faq-item">
                                            <h3><?php echo htmlspecialchars($row['question']); ?></h3>
                                            <div class="faq-content">
                                                <p><?php echo htmlspecialchars($row['answer']); ?></p>
                                            </div>
                                            <i class="faq-toggle bi bi-chevron-right"></i>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p>No FAQs available.</p>
                                <?php endif; ?>
                            </div>

                        </div>

                    </div><!-- End Faq Column-->

                </div>

            </div>

        </section><!-- /Faq Section -->

        <!-- Contact Section -->
        <section id="contact" class="contact section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Contact</h2>
                <p><span>Need Help?</span> <span class="description-title">Contact Us</span></p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4">

                    <div class="col-lg-5">

                        <div class="info-wrap">
                            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                                <i class="bi bi-geo-alt flex-shrink-0"></i>
                                <div>
                                    <h3>Address</h3>
                                    <p><?= $info['address'] ?></p>
                                </div>
                            </div><!-- End Info Item -->

                            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                                <i class="bi bi-telephone flex-shrink-0"></i>
                                <div>
                                    <h3>Call Us</h3>
                                    <p><?= $info['phone_number'] ?></p>
                                </div>
                            </div><!-- End Info Item -->

                            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                                <i class="bi bi-envelope flex-shrink-0"></i>
                                <div>
                                    <h3>Email Us</h3>
                                    <p><?= $info['email'] ?></p>
                                </div>
                            </div><!-- End Info Item -->

                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d48389.78314118045!2d125.17240950323836!3d6.111281350340498!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMDEwJzM2LjMiTiAxMjUuMTcyNDA5MjExMjMiVQ!5e0!3m2!1sen!2sus!4v1676961268712!5m2!1sen!2sus"
                                frameborder="0" style="border:0; width: 100%; height: 270px;" allowfullscreen=""
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                        </div>
                    </div>

                    <div class="col-lg-7">
                        <form action="indexes/messages.php" method="post" class="php-email-form" data-aos="fade-up"
                            data-aos-delay="200" id="contactForm">
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <label for="name-field" class="pb-2">Your Name</label>
                                    <input type="text" name="name" id="name-field" class="form-control">
                                    <div class="error-message" id="name-error"><i class="bi bi-exclamation-circle"></i>
                                        This field is required</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email-field" class="pb-2">Your Email</label>
                                    <input type="text" class="form-control" name="email" id="email-field">
                                    <div class="error-message" id="email-error"><i class="bi bi-exclamation-circle"></i>
                                        Please enter a valid email address</div>
                                </div>

                                <div class="col-md-12">
                                    <label for="subject-field" class="pb-2">Subject</label>
                                    <input type="text" class="form-control" name="subject" id="subject-field">
                                    <div class="error-message" id="subject-error"><i
                                            class="bi bi-exclamation-circle"></i> This field is required</div>
                                </div>

                                <div class="col-md-12">
                                    <label for="message-field" class="pb-2">Message</label>
                                    <textarea class="form-control" name="message" rows="10"
                                        id="message-field"></textarea>
                                    <div class="error-message" id="message-error"><i
                                            class="bi bi-exclamation-circle"></i> This field is required</div>
                                </div>

                                <div class="col-md-12 text-center">
                                    <div class="error-message" id="form-error"></div>
                                    <div class="sent-message">Your message has been sent. Thank you!</div>
                                    <button type="submit" name="messageButton">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div><!-- End Contact Form -->

                </div>

            </div>

        </section><!-- /Contact Section -->

    </main>

    <?php include 'layout/footer.php'; ?>

    <!-- Scroll Top -->
    <a id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>

    <script>document.getElementById('contactForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent form submission

            // Clear previous errors
            clearErrors();

            // Validate inputs
            let isValid = true;

            const nameField = document.getElementById('name-field');
            const emailField = document.getElementById('email-field');
            const subjectField = document.getElementById('subject-field');
            const messageField = document.getElementById('message-field');

            if (nameField.value.trim() === '') {
                showError(nameField, 'name-error');
                isValid = false;
            }

            if (emailField.value.trim() === '' || !validateEmail(emailField.value.trim())) {
                showError(emailField, 'email-error');
                isValid = false;
            }

            if (subjectField.value.trim() === '') {
                showError(subjectField, 'subject-error');
                isValid = false;
            }

            if (messageField.value.trim() === '') {
                showError(messageField, 'message-error');
                isValid = false;
            }

            // If all inputs are valid, submit the form
            if (isValid) {
                this.submit();
            }
        });

        function showError(inputField, errorId) {
            inputField.classList.add('error');
            document.getElementById(errorId).style.display = 'block';
        }

        function clearErrors() {
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(function (error) {
                error.style.display = 'none';
            });

            const inputFields = document.querySelectorAll('.form-control');
            inputFields.forEach(function (input) {
                input.classList.remove('error');
            });
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
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