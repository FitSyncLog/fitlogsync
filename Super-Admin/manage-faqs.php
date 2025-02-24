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
        <title>Manage FAQs | FiT-LOGSYNC</title>
        <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="../css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="icon" type="image/x-icon" href="../assets/fitlogsync.ico">
        <link rel="stylesheet" href="../assets/css/sweetalert2.min.css">
        <script src="../assets/js/sweetalert2.all.min.js"></script>

        <style>
            .info-card {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 15px;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .info-content {
                display: flex;
                align-items: center;
                flex-grow: 1;
                /* Allow the content to take up available space */
                margin-right: 15px;
                /* Add some spacing between content and buttons */
            }

            .info-icon {
                font-size: 1.5rem;
                margin-right: 10px;
            }

            .button-container {
                display: flex;
                align-items: center;
                gap: 10px;
                /* Add spacing between buttons */
            }

            .edit-btn,
            .delete-btn {
                width: 80px;
                /* Set a fixed width for the buttons */
                height: 40px;
                /* Set a fixed height for the buttons */
                display: flex;
                align-items: center;
                justify-content: center;
                white-space: nowrap;
                /* Prevent text from wrapping */
            }

            .info-icon-box {
                background-color: #F6C23E;
                width: 50px;
                height: 50px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
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
                            <h1 class="h3 mb-0 text-gray-800">Manage Frequently Asked Questions</h1>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#addFAQModal">Add FAQs</button>
                        </div>

                        <?php
                        $query = "SELECT * FROM faq";
                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id = $row['id'];
                                $question = htmlspecialchars($row['question']);
                                $answer = htmlspecialchars($row['answer']);
                                ?>
                                <div class="info-card d-flex flex-column flex-md-row align-items-center p-3 border rounded">
                                    <div class="info-content d-flex align-items-center flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-center me-4 mb-3 mb-md-0"
                                            style="background-color: #F6C23E; width: 50px; height: 50px; border-radius: 0; padding: 8px;">
                                            <div class="info-icon-box me-4 mb-0">
                                                <i class="bi bi-question-lg text-white fs-4"></i>
                                            </div>
                                        </div>
                                        <div style="padding: 5px 10px;">
                                            <strong><?php echo $question; ?></strong><br>
                                            <span><?php echo $answer; ?></span>
                                        </div>
                                    </div>
                                    <div class="button-container mt-3 mt-md-0">
                                        <button class="btn btn-warning btn-sm edit-btn" data-toggle="modal"
                                            data-target="#editFAQModal<?php echo $id; ?>">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-btn" data-toggle="modal"
                                            data-target="#deleteFAQModal<?php echo $id; ?>">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>

                                <!-- Edit FAQ Modal -->
                                <div class="modal fade" id="editFAQModal<?php echo $id; ?>" tabindex="-1"
                                    aria-labelledby="editFAQModalLabel<?php echo $id; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editFAQModalLabel<?php echo $id; ?>">Edit FAQ</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="indexes/manage-faqs.php" method="POST">
                                                    <!-- Hidden input field for the ID -->
                                                    <input type="hidden" name="editID" value="<?php echo $id; ?>">
                                                    <div class="form-group">
                                                        <label for="editQuestion<?php echo $id; ?>">Question</label>
                                                        <input type="text" class="form-control" id="editQuestion<?php echo $id; ?>"
                                                            name="editQuestion" value="<?php echo $question; ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="editAnswer<?php echo $id; ?>">Answer</label>
                                                        <textarea class="form-control" id="editAnswer<?php echo $id; ?>"
                                                            name="editAnswer" rows="10"><?php echo $answer; ?></textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-center mt-3">
                                                        <button type="submit" class="btn btn-warning" name="editFAQ">Save
                                                            changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete FAQ Modal -->
                                <div class="modal fade" id="deleteFAQModal<?php echo $id; ?>" tabindex="-1"
                                    aria-labelledby="deleteFAQModalLabel<?php echo $id; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteFAQModalLabel<?php echo $id; ?>">Delete FAQ</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Question:</strong> <?php echo $question; ?></p>
                                                <p><strong>Answer:</strong> <?php echo $answer; ?></p>
                                                <p>Are you sure you want to delete this FAQ?</p>
                                            </div>
                                            <!-- Updated modal-footer with centered buttons -->
                                            <div class="modal-footer d-flex justify-content-center">
                                                <form action="indexes/manage-faqs.php" method="POST">
                                                    <input type="hidden" name="deleteID" value="<?php echo $id; ?>">
                                                    <input type="hidden" name="deleteQuestion" value="<?php echo $question; ?>">
                                                    <input type="hidden" name="deleteAnswer" value="<?php echo $answer; ?>">
                                                    <button type="submit" class="btn btn-danger" name="deleteFAQ">Delete</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancel</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "No FAQs found.";
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- Add FAQ Modal -->
        <div class="modal fade" id="addFAQModal" tabindex="-1" aria-labelledby="addFAQModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFAQModalLabel">Add FAQ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="indexes/manage-faqs.php" method="POST">
                            <div class="form-group">
                                <label for="newQuestion">Question</label>
                                <input type="text" class="form-control" id="newQuestion" name="newQuestion"
                                    value="<?php echo ($_GET['newQuestion'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="newAnswer">Answer</label>
                                <textarea class="form-control" id="newAnswer" name="newAnswer"
                                    rows="10"><?php echo ($_GET['newAnswer'] ?? ''); ?></textarea>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <button type="submit" class="btn btn-warning" name="addFAQ">Add FAQ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <script src="../vendor/jquery/jquery.min.js"></script>
        <script src="../vendor/bootstrap/js/boitstrap.bundle.min.js"></script>
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