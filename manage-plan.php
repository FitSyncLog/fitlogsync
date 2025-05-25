<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "manage-plan.php";
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
        <title>Manage Plan | FiT-LOGSYNC</title>
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
                            <h1 class="h3 mb-0 text-gray-800">Manage Plan</h1>
                            <a class="btn btn-warning" data-toggle="modal" data-target="#viewHistoryModal">View Full
                                History</a>

                            <?php
                            $query = "
                                SELECT 
                                    ph.date_time,
                                    p.plan_name,
                                    ph.price,
                                    ph.status,
                                    u.lastname,
                                    u.firstname
                                FROM 
                                    plan_history ph
                                JOIN 
                                    plans p ON ph.plan_id = p.plan_id
                                JOIN 
                                    users u ON ph.user_id = u.user_id
                                ORDER BY 
                                    ph.date_time DESC
                                ";
                            $result = $conn->query($query);
                            ?>

                            <!-- View History Modal -->
                            <div class="modal fade" id="viewHistoryModal" tabindex="-1" role="dialog"
                                aria-labelledby="viewHistoryLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewHistoryLabel">Plan History</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <?php if ($result->num_rows > 0): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead class="thead-warning">
                                                            <tr>
                                                                <th>Date and Time</th>
                                                                <th>Plan Name</th>
                                                                <th>Updated Price</th>
                                                                <th>Updated Status</th>
                                                                <th>Updated By</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                                <tr>
                                                                    <td><?= date('F d, Y | g:i A', strtotime($row['date_time'])) ?>
                                                                    </td>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($row['plan_name']) ?></td>
                                                                    <td>₱<?= number_format($row['price'], 2) ?></td>
                                                                    <td>
                                                                        <?= $row['status'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>' ?>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($row['lastname'] . ', ' . $row['firstname']) ?>
                                                                    </td>

                                                                </tr>
                                                            <?php endwhile; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php else: ?>
                                                <p>No plan history found.</p>
                                            <?php endif; ?>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                        <?php
                        $query = "SELECT * FROM plans ";
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
                                                    <strong><?= htmlspecialchars($row['plan_name']) ?></strong>
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <h2>
                                                    <sup><strong>₱</sup><?= htmlspecialchars($row['price']) ?></strong>
                                                    <h5 class="text-muted">per month</h5>
                                                </h2>
                                                <hr>
                                                <p class="mt-3 text-muted"><?= htmlspecialchars($row['description']) ?></p>
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
                                                    data-id="<?= $row['plan_id'] ?>"
                                                    data-name="<?= htmlspecialchars($row['plan_name']) ?>"
                                                    data-price="<?= $row['price'] ?>" data-status="<?= $row['status'] ?>">
                                                    Edit
                                                </button>

                                                <button type="button" class="btn btn-secondary px-4 ml-2 view-history-btn"
                                                    data-id="<?= $row['plan_id'] ?>"
                                                    data-name="<?= htmlspecialchars($row['plan_name']) ?>" data-toggle="modal"
                                                    data-target="#planHistoryModal">
                                                    View Plan History
                                                </button>

                                            </div>

                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </section>


                        <!-- Plan History Modal -->
                        <div class="modal fade" id="planHistoryModal" tabindex="-1" aria-labelledby="planHistoryModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="planHistoryModalLabel">Plan History</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-warning">
                                                    <tr>
                                                        <th>Date and Time</th>
                                                        <th>Updated Price</th>
                                                        <th>Updated Status</th>
                                                        <th>Updated By</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="planHistoryTableBody">
                                                    <!-- Plan history details will be loaded here via AJAX -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Update Plan Price Modal -->
                        <div class="modal fade" id="updatePriceModal" tabindex="-1" aria-labelledby="updatePriceLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="indexes/update-plan-price.php" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updatePriceLabel">Update Plan Price</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="plan_id" id="modal_plan_id">

                                            <p><strong>Plan:</strong> <span id="modal_plan_name"
                                                    class="text-primary"></span></p>
                                            <p><strong>Current Price:</strong> ₱<span id="modal_current_price"
                                                    class="text-danger"></span></p>

                                            <div class="form-group">
                                                <label for="modal_price">New Price (₱)</label>
                                                <input type="number" class="form-control" name="price" id="modal_price"
                                                    required min="0" step="0.01">
                                            </div>

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
                    var planId = $(this).data('id');
                    var planName = $(this).data('name');
                    $('#planHistoryModalLabel').text(planName + ' Plan History');

                    // Fetch plan history data via AJAX
                    $.ajax({
                        url: 'indexes/get-plan-history.php', // Create this file to fetch plan history data
                        type: 'GET',
                        data: { plan_id: planId },
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
                var planId = button.data('id');
                var planName = button.data('name');
                var price = button.data('price');
                var status = button.data('status');

                var modal = $(this);
                modal.find('#modal_plan_id').val(planId);
                modal.find('#modal_plan_name').text(planName);
                modal.find('#modal_current_price').text(price);
                modal.find('#modal_price').val(price);

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