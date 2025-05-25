<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "create-new-coupon.php";
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
        <title>Create New Coupons | FiT-LOGSYNC</title>
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
                            <h1 class="h3 mb-0 text-gray-800">Create New Coupon</h1>
                            <a class="btn btn-secondary" href="manage-coupons.php">Back</a>

                        </div>


                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="text-center text-muted my-3">
                                    <i class="fas fa-solid fa-ticket fa-10x"></i>
                                </div>

                                <form action="indexes/create-new-coupon.php" method="POST">
                                    <div class="modal-body">

                                        <?php
                                        $coupon_name = isset($_GET['coupon_name']) ? htmlspecialchars($_GET['coupon_name']) : '';
                                        $coupon_type = isset($_GET['coupon_type']) ? $_GET['coupon_type'] : '';
                                        $coupon_value = isset($_GET['coupon_value']) ? htmlspecialchars($_GET['coupon_value']) : '';
                                        $number_of_coupons = isset($_GET['number_of_coupons']) ? htmlspecialchars($_GET['number_of_coupons']) : '';
                                        $start_date = isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '';
                                        $end_date = isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : '';
                                        ?>

                                        <div class="form-group">
                                            <label for="couponName">Coupon Name/Event Name</label>
                                            <input type="text" class="form-control" id="couponName" name="coupon_name"
                                                required value="<?= $coupon_name ?>">
                                        </div>

                                        <div class="form-group">
                                            <label>Coupon Type</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="coupon_type"
                                                    id="percentage" value="percentage" <?= $coupon_type === 'percentage' ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="percentage">Percentage %</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="coupon_type" id="amount"
                                                    value="amount" <?= $coupon_type === 'amount' ? 'checked' : '' ?> required>
                                                <label class="form-check-label" for="amount">Amount ₱</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="couponValue">Value</label>
                                            <input type="number" class="form-control" id="couponValue" name="coupon_value"
                                                required value="<?= $coupon_value ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="numberOfCoupons">Number of Coupons</label>
                                            <input type="number" class="form-control" id="numberOfCoupons"
                                                name="number_of_coupons" required value="<?= $number_of_coupons ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="dateStarted">Date Started</label>
                                            <input type="date" class="form-control" id="dateStarted" name="start_date"
                                                required value="<?= $start_date ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="validUntil">Valid Until</label>
                                            <input type="date" class="form-control" id="validUntil" name="end_date" required
                                                value="<?= $end_date ?>">
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <a class="btn btn-secondary" href="manage-coupons.php">Cancel</a>
                                        <button type="submit" class="btn btn-warning" name="newCoupon">Create New
                                            Coupon</button>
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