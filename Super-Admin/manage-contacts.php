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
        <title>Manage Contact Information | FiT-LOGSYNC</title>
        <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="../css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="icon" type="image/x-icon" href="../assets/fitlogsync.ico">

        <script src="../assets/css/sweetalert2.min.css"></script>
        <script src="../assets/js/sweetalert2.all.min.js"></script>

        <style>
            .info-card {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 15px;
                margin-bottom: 15px;
                display: flex;
                flex-direction: column;
                /* Stack content vertically on small screens */
                align-items: flex-start;
                /* Align content to the left */
            }

            .info-content {
                display: flex;
                align-items: center;
                flex-grow: 1;
                margin-right: 15px;
            }

            .info-icon {
                font-size: 1.5rem;
                margin-right: 10px;
            }

            .edit-btn {
                margin-left: 10px;
            }

            /* Responsive adjustments */
            @media (min-width: 768px) {
                .info-card {
                    flex-direction: row;
                    /* Align content horizontally on larger screens */
                    align-items: center;
                    justify-content: space-between;
                }

                .edit-btn {
                    margin-left: auto;
                    /* Push the edit button to the right */
                }
            }

            @media (max-width: 576px) {
                .info-content {
                    flex-direction: column;
                    /* Stack icon and text vertically on very small screens */
                    align-items: flex-start;
                }

                .info-icon-box {
                    margin-bottom: 10px;
                    /* Add spacing between icon and text */
                }
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
                            <h1 class="h3 mb-0 text-gray-800">Manage Contacts</h1>
                        </div>

                        <?php
                        $query = "SELECT * FROM information";
                        $result = mysqli_query($conn, $query);

                        $info = [];

                        // Fetch data and assign to variables
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $info[$row['information_for']] = $row['description'];
                            }

                            // Assign variables
                            $address = $info['address'] ?? '';
                            $phone_number = $info['phone_number'] ?? '';
                            $x = $info['x'] ?? '';
                            $facebook = $info['facebook'] ?? '';
                            $instagram = $info['instagram'] ?? '';
                            $youtube = $info['youtube'] ?? '';
                            $tiktok = $info['tiktok'] ?? '';
                            $email = $info['email'] ?? '';
                        } else {
                            echo "No data found.";
                        }
                        ?>

                        <!-- Address -->
                        <div class="info-card">
                            <div class="info-content">
                                <!-- Square yellow box with white icon and padding -->
                                <div class="d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0"
                                    style="background-color: #F6C23E; width: 50px; height: 50px; border-radius: 0; padding: 8px;">
                                    <i class="bi bi-geo-alt-fill text-white fs-4"></i>
                                </div>
                                <div style="padding: 5px 10px;">
                                    <strong>Address</strong><br>
                                    <span><?php echo htmlspecialchars($address); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal" data-target="#addressModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </div>

                        <!-- Address Modal -->
                        <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="addressModalLabel">Edit Address</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="indexes/manage-contacts.php" method="POST">
                                            <div class="form-group">
                                                <label for="newAddress">New Address</label>
                                                <input type="text" class="form-control" id="newAddress" name="newAddress"
                                                    value="<?php echo htmlspecialchars($address); ?>">
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button type="submit" class="btn btn-warning" name="">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="info-card">
                            <div class="info-content">
                                <!-- Square yellow box with white icon and padding -->
                                <div class="d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0"
                                    style="background-color: #F6C23E; width: 50px; height: 50px; border-radius: 0; padding: 8px;">
                                    <i class="bi bi-telephone-fill text-white fs-4"></i>
                                </div>
                                <div style="padding: 5px 10px;">
                                    <strong>Phone Number</strong><br>
                                    <span><?php echo htmlspecialchars($phone_number); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal" data-target="#phoneModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </div>

                        <!-- Phone Number Modal -->
                        <div class="modal fade" id="phoneModal" tabindex="-1" aria-labelledby="phoneModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="phoneModalLabel">Edit Phone Number</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="indexes/manage-contacts.php" method="POST">
                                            <div class="form-group">
                                                <label for="newPhoneNumber">New Phone Number</label>
                                                <input type="text" class="form-control" id="newPhoneNumber"
                                                    name="newPhoneNumber"
                                                    value="<?php echo htmlspecialchars($phone_number); ?>">
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button type="submit" class="btn btn-warning" name="">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Email -->
                        <div class="info-card">
                            <div class="info-content">
                                <!-- Square yellow box with white icon and padding -->
                                <div class="d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0"
                                    style="background-color: #F6C23E; width: 50px; height: 50px; border-radius: 0; padding: 8px;">
                                    <i class="bi bi-envelope-fill text-white fs-4"></i>
                                </div>
                                <div style="padding: 5px 10px;">
                                    <strong>Email</strong><br>
                                    <span><?php echo htmlspecialchars($email); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal" data-target="#emailModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </div>

                        <!-- Email Modal -->
                        <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="emailModalLabel">Edit Email</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="indexes/manage-contacts.php" method="POST">
                                            <div class="form-group">
                                                <label for="newEmail">New Email</label>
                                                <input type="text" class="form-control" id="newEmail" name="newEmail"
                                                    value="<?php echo htmlspecialchars($email); ?>">
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button type="submit" class="btn btn-warning" name="">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Twitter -->
                        <div class="info-card">
                            <div class="info-content">
                                <!-- Square yellow box with white icon and padding -->
                                <div class="d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0"
                                    style="background-color: #F6C23E; width: 50px; height: 50px; border-radius: 0; padding: 8px;">
                                    <i class="bi bi-twitter text-white fs-4"></i>
                                </div>
                                <div style="padding: 5px 10px;">
                                    <strong>Twitter</strong><br>
                                    <span><?php echo htmlspecialchars($x); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal" data-target="#twitterModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </div>

                        <!-- Twitter Modal -->
                        <div class="modal fade" id="twitterModal" tabindex="-1" aria-labelledby="twitterModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="twitterModalLabel">Edit Twitter Link
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="indexes/manage-contacts.php" method="POST">
                                            <div class="form-group">
                                                <label for="newTwitter">New Twitter Link</label>
                                                <input type="text" class="form-control" id="newTwitter" name="newTwitter"
                                                    value="<?php echo htmlspecialchars($x); ?>">
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button type="submit" class="btn btn-warning" name="">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Facebook -->
                        <div class="info-card">
                            <div class="info-content">
                                <!-- Square yellow box with white icon and padding -->
                                <div class="d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0"
                                    style="background-color: #F6C23E; width: 50px; height: 50px; border-radius: 0; padding: 8px;">
                                    <i class="bi bi-facebook text-white fs-4"></i>
                                </div>
                                <div style="padding: 5px 10px;">
                                    <strong>Facebook</strong><br>
                                    <span><?php echo htmlspecialchars($facebook); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal"
                                data-target="#facebookModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </div>

                        <!-- Facebook Modal -->
                        <div class="modal fade" id="facebookModal" tabindex="-1" aria-labelledby="facebookModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="facebookModalLabel">Edit Facebook Link
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="indexes/manage-contacts.php" method="POST">
                                            <div class="form-group">
                                                <label for="newFacebook">New Facebook Link</label>
                                                <input type="text" class="form-control" id="newFacebook" name="newFacebook"
                                                    value="<?php echo htmlspecialchars($facebook); ?>">
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button type="submit" class="btn btn-warning" name="">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="info-card">
                            <div class="info-content">
                                <!-- Square yellow box with white icon and padding -->
                                <div class="d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0"
                                    style="background-color: #F6C23E; width: 50px; height: 50px; border-radius: 0; padding: 8px;">
                                    <i class="bi bi-instagram text-white fs-4"></i>
                                </div>
                                <div style="padding: 5px 10px;">
                                    <strong>Instagram</strong><br>
                                    <span><?php echo htmlspecialchars($instagram); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal"
                                data-target="#instagramModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </div>

                        <!-- Instagram Modal -->
                        <div class="modal fade" id="instagramModal" tabindex="-1" aria-labelledby="instagramModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="instagramModalLabel">Edit Instagram
                                                Link</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="indexes/manage-contacts.php" method="POST">
                                            <div class="form-group">
                                                <label for="newInstagram">New Instagram Link</label>
                                                <input type="text" class="form-control" id="newInstagram"
                                                    name="newInstagram" value="<?php echo htmlspecialchars($instagram); ?>">
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button type="submit" class="btn btn-warning" name="">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- YouTube -->
                        <div class="info-card">
                            <div class="info-content">
                                <!-- Square yellow box with white icon and padding -->
                                <div class="d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0"
                                    style="background-color: #F6C23E; width: 50px; height: 50px; border-radius: 0; padding: 8px;">
                                    <i class="bi bi-youtube text-white fs-4"></i>
                                </div>
                                <div style="padding: 5px 10px;">
                                    <strong>YouTube</strong><br>
                                    <span><?php echo htmlspecialchars($youtube); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal" data-target="#youtubeModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </div>

                        <!-- YouTube Modal -->
                        <div class="modal fade" id="youtubeModal" tabindex="-1" aria-labelledby="youtubeModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="youtubeModalLabel">Edit YouTube Link
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="indexes/manage-contacts.php" method="POST">
                                            <div class="form-group">
                                                <label for="newYoutube">New YouTube Link</label>
                                                <input type="text" class="form-control" id="newYoutube" name="newYoutube"
                                                    value="<?php echo htmlspecialchars($youtube); ?>">
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button type="submit" class="btn btn-warning" name="">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TikTok -->
                        <div class="info-card">
                            <div class="info-content">
                                <!-- Square yellow box with white icon and padding -->
                                <div class="d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0"
                                    style="background-color: #F6C23E; width: 50px; height: 50px; border-radius: 0; padding: 8px;">
                                    <i class="bi bi-tiktok text-white fs-4"></i>
                                </div>
                                <div style="padding: 5px 10px;">
                                    <strong>TikTok</strong><br>
                                    <span><?php echo htmlspecialchars($tiktok); ?></span>
                                </div>
                            </div>
                            <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal" data-target="#tiktokModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </div>

                        <!-- TikTok Modal -->
                        <div class="modal fade" id="tiktokModal" tabindex="-1" aria-labelledby="tiktokModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title" id="tiktokModalLabel">Edit TikTok Link</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="indexes/manage-contacts.php" method="POST">
                                            <div class="form-group">
                                                <label for="newTiktok">New TikTok Link</label>
                                                <input type="text" class="form-control" id="newTiktok" name="newTiktok"
                                                    value="<?php echo htmlspecialchars($tiktok); ?>">
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button type="submit" class="btn btn-warning" name="">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


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