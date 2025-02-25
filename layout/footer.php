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
<footer id="footer" class="footer">

    <div class="container footer-top">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6 footer-about">
                <a href="index.html" class="logo d-flex align-items-center">
                    <img src="assets/fitlogsync1.png" alt="FiT-LOGSYNC Logo" class="img-fluid" style="height: 100px;">
                </a>
                <div class="footer-contact pt-3">
                    <p><?php echo isset($info['address']) ? $info['address'] : 'Address not available'; ?></p>
                    <p class="mt-3"><strong>Phone:</strong>
                        <span><?php echo isset($info['phone_number']) ? $info['phone_number'] : 'Phone not available'; ?></span>
                    </p>
                    <p><strong>Email:</strong>
                        <span><?php echo isset($info['email']) ? $info['email'] : 'Email not available'; ?></span></p>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>About FiT-LOGSYNC</h4>
                <ul>
                    <li><i class="bi bi-chevron-right"></i> <a href="./#welcome">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="./#about">About us</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="./#facilities">Facilities and Amenities</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="./#instructors">Instructors</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="./#pricing">Pricing</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="./#contact">Contact</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="./#faq">F.A.Qs</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-12">
                <h4>Follow Us</h4>
                <p>Don't forget to follow us on our social media platforms.</p>
                <div class="social-links d-flex">
                    <?php if (!empty($info['x'])): ?>
                        <a href="https://<?= $info['x'] ?>" class="twitter" target="_blank"><i
                                class="bi bi-twitter-x"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($info['facebook'])): ?>
                        <a href="https://<?= $info['facebook'] ?>" class="facebook" target="_blank"><i
                                class="bi bi-facebook"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($info['instagram'])): ?>
                        <a href="https://<?= $info['instagram'] ?>" class="instagram" target="_blank"><i
                                class="bi bi-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($info['youtube'])): ?>
                        <a href="https://<?= $info['youtube'] ?>" class="linkedin" target="_blank"><i
                                class="bi bi-youtube"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($info['tiktok'])): ?>
                        <a href="https://<?= $info['tiktok'] ?>" class="linkedin" target="_blank"><i
                                class="bi bi-tiktok"></i></a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <hr class="border-secondary my-4">
    <div class="text-center">
        <p class="mb-3">Download the Application</p>
        <div class="d-flex gap-2 gap-sm-3 justify-content-center">
            <a href="#!" class="btn btn-outline-success bsb-btn-circle bsb-btn-circle-2xl">
                <i class="bi bi-google-play fs-4"></i>
            </a>
            <a href="#!" class="btn btn-outline-dark bsb-btn-circle bsb-btn-circle-2xl">
                <i class="bi bi-cloud-download-fill fs-4"></i>
            </a>
        </div>
    </div>

</footer>