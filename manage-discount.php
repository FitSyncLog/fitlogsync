<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "manage-discount.php";
$role_id = $_SESSION['role_id'];
$query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $page_name, $role_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Manage Discount | FiT-LOGSYNC</title>
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
        <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
        <script src="assets/js/sweetalert2.all.min.js"></script>
        <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />
        <script src="assets/js/sessionExpired.js"></script>

        <style>
            .card:hover .card-header,
            .card:hover .card-footer {
                background-color: #ffc107 !important;
                color: #000 !important;
                transition: background-color 0.3s ease;
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
            <?php include 'layout/sidebar.php'; ?>
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <?php include 'layout/navbar.php'; ?>
                    <div class="container-fluid">
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">Manage Discount</h1>


                        </div>

                        <?php
                        $query = "SELECT * FROM discounts";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        ?>

                        <!-- Main content -->
                        <section class="content">
                            <div class="row justify-content-center">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card text-center shadow h-100">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0 text-uppercase">
                                                    <strong><?= htmlspecialchars($row['discount_name']) ?></strong>
                                                </h5>
                                            </div>
                                            <div class="card-body">

                                                <?php
                                                $discount_name = $row['discount_name'];
                                                ?>

                                                <?php if ($discount_name == 'Person With Disability'): ?>
                                                    <div class="text-center text-muted my-3">
                                                        <i class="fas fa-wheelchair fa-10x"></i>
                                                    </div>
                                                <?php elseif ($discount_name == 'Student'): ?>
                                                    <div class="text-center text-muted my-3">
                                                        <i class="fas fa-graduation-cap fa-10x"></i>
                                                    </div>
                                                <?php elseif ($discount_name == 'Senior Citizen'): ?>
                                                    <div class="text-center text-muted my-3">
                                                        <i class="fas fa-user fa-10x"></i>
                                                        <!-- Or use another icon like fa-user-old if using Font Awesome Pro -->
                                                    </div>
                                                <?php else: ?>
                                                    <!-- blank -->
                                                <?php endif; ?>


                                                <h2>
                                                    <sup><strong></sup><?= htmlspecialchars($row['discount_value']) ?>%</strong>
                                                    <h5 class="text-muted">discount</h5>
                                                </h2>

                                                <hr>
                                                <?php
                                                $status = $row['status'];
                                                if ($status == 1) {
                                                    ?>
                                                    <p class="mt-3 text-muted">Active</p>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <p class="mt-3 text-muted">Deactivate</p>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="card-footer bg-light">
                                                <button type="button" class="btn btn-warning text-white px-4"
                                                    data-toggle="modal" data-target="#updatePriceModal"
                                                    data-id="<?= $row['discount_id'] ?>"
                                                    data-name="<?= htmlspecialchars($row['discount_name']) ?>"
                                                    data-status="<?= $row['status'] ?>">
                                                    Edit Status
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </section>

                        <!-- Update Plan Price Modal -->
                        <div class="modal fade" id="updatePriceModal" tabindex="-1" aria-labelledby="updatePriceLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="indexes/update-discount-status.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updatePriceLabel">Update Discount Status</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="discount_id" id="modal_discount_id">

                                            <p><strong>Discount:</strong> <span id="modal_plan_name"
                                                    class="text-primary"></span></p>

                                            <style>
                                                .toggle-warning:checked+.custom-control-label::before {
                                                    background-color: #ffc107 !important;
                                                    border-color: #ffc107 !important;
                                                }

                                                .toggle-warning:checked+.custom-control-label::after {
                                                    background-color: white;
                                                }
                                            </style>

                                            <div class="form-group">
                                                <label for="statusToggle">Status</label><br>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input toggle-warning"
                                                        id="statusToggle" name="status" value="1">
                                                    <label class="custom-control-label" for="statusToggle">Active</label>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-warning">Save Changes</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Cancel</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- jQuery and Bootstrap -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function () {
                $('.view-history-btn').on('click', function () {
                    var discountId = $(this).data('id');

                    // Fetch plan history data via AJAX
                    $.ajax({
                        url: 'indexes/get-plan-history.php', // Create this file to fetch plan history data
                        type: 'GET',
                        data: { discount_id: discountId },
                        success: function (response) {
                            $('#planHistoryTableBody').html(response);
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                        }
                    });
                });
            });
        </script>


        <script>
            $(document).ready(function () {
                $('.view-history-btn').on('click', function () {
                    var planName = $(this).data('name');
                    $('#planHistoryModalLabel').text(planName + ' Plan History');
                });
            });
        </script>


        <script>
            $('#updatePriceModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var discountId = button.data('id');
                var planName = button.data('name');
                var status = button.data('status');

                var modal = $(this);
                modal.find('#modal_discount_id').val(discountId);
                modal.find('#modal_plan_name').text(planName);

                // Set toggle status
                if (status == 1) {
                    modal.find('#statusToggle').prop('checked', true);
                } else {
                    modal.find('#statusToggle').prop('checked', false);
                }
            });
        </script>

        <!-- Rest -->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="js/demo/datatables-demo.js"></script>
    </body>

    </html>
    <?php
} else {
    header("Location: indexes/logout.php");
    exit();
}