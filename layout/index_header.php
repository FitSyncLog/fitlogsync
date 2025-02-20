<?php
include "indexes/db_con.php";

$sql = "SELECT * FROM information";
$result = $conn->query($sql);

$info = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $info[$row['information_for']] = $row['description'];
    }
}
?>

<header id="header" class="header sticky-top">
    <div class="topbar d-flex align-items-center">
        <div class="container d-flex justify-content-center justify-content-md-between">
            <div class="contact-info d-flex align-items-center">
                <i class="bi bi-envelope d-flex align-items-center">
                    <a href="mailto:<?= $info['email'] ?? 'fitlogsync.official@gmail.com' ?>">
                        <?= $info['email'] ?? 'No email for now' ?>
                    </a>
                </i>
                <i class="bi bi-phone d-flex align-items-center ms-4">
                    <span><?= $info['phone_number'] ?? 'No phone number for now' ?></span>
                </i>
            </div>
            <div class="social-links d-none d-md-flex align-items-center">
                <?php if (!empty($info['x'])): ?>
                    <a href="https://<?= $info['x'] ?>" class="twitter" target="_blank"><i class="bi bi-twitter-x"></i></a>
                <?php endif; ?>
                <?php if (!empty($info['facebook'])): ?>
                    <a href="https://<?= $info['facebook'] ?>" class="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
                <?php endif; ?>
                <?php if (!empty($info['instagram'])): ?>
                    <a href="https://<?= $info['instagram'] ?>" class="instagram" target="_blank"><i class="bi bi-instagram"></i></a>
                <?php endif; ?>
                <?php if (!empty($info['youtube'])): ?>
                    <a href="https://<?= $info['youtube'] ?>" class="linkedin" target="_blank"><i class="bi bi-youtube"></i></a>
                <?php endif; ?>
                <?php if (!empty($info['tiktok'])): ?>
                    <a href="https://<?= $info['tiktok'] ?>" class="linkedin" target="_blank"><i class="bi bi-tiktok"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-center">
        <div class="container position-relative d-flex align-items-center justify-content-between">
            <a href="index.php" class="logo d-flex align-items-center">
                <img src="assets/fitlogsync1.png" alt="FiT-LOGSYNC Logo" class="img-fluid" style="height: 100px;">
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="./#welcome" class="active">Home</a></li>
                    <li><a href="./#about">About</a></li>
                    <li><a href="./#facilities">Facilities</a></li>
                    <li><a href="./#instructors">Instructors</a></li>
                    <li><a href="./#pricing">Pricing</a></li>
                    <li><a href="./#contact">Contact</a></li>
                    <li><a href="./login.php" class="btn btn-warning py-2 px-4">Login</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
        </div>
    </div>
</header>
