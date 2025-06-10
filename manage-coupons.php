<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "manage-coupons.php";
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
        <title>Manage Coupon | FiT-LOGSYNC</title>
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
                            <h1 class="h3 mb-0 text-gray-800">Manage Coupons</h1>
                            <a class="btn btn-warning" href="create-new-coupon.php">Add New Coupon</a>
                        </div>

                        <?php
                        $query = "SELECT * FROM coupons";
                        $result = $conn->query($query);
                        ?>

                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Coupon Name</th>
                                                <th>Status</th>
                                                <th>Value</th>
                                                <th>No. of Coupons</th>
                                                <th>Starting Date</th>
                                                <th>Valid Until</th>
                                                <th>Created At</th>
                                                <th>Created By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <?php
                                        $query = "SELECT * FROM coupons";
                                        $result = $conn->query($query);
                                        ?>

                                        <tbody>
                                            <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    // Fetch created_by user details
                                                    $created_by_id = $row['created_by'];
                                                    $user_query = "SELECT lastname, firstname FROM users WHERE user_id = $created_by_id";
                                                    $user_result = $conn->query($user_query);
                                                    $user = $user_result->fetch_assoc();
                                                    $created_by = $user ? $user['lastname'] . ', ' . $user['firstname'] : 'Unknown';

                                                    // Format the dates
                                                    $created_at = date('F d, Y | g:i A', strtotime($row['created_at']));
                                                    $start_date = date('F d, Y', strtotime($row['start_date']));
                                                    $end_date = date('F d, Y', strtotime($row['end_date']));

                                                    // Determine the value display
                                                    $value = $row['coupon_type'] == 'percentage'
                                                        ? $row['coupon_value'] . '%'
                                                        : '₱ ' . $row['coupon_value'] . ".00";
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?= htmlspecialchars($row['coupon_name']) ?></td>

                                                        <td class="text-center">
                                                            <?php
                                                            // Set the time zone to Manila/Asia
                                                            date_default_timezone_set('Asia/Manila');

                                                            // Dummy example (replace these with real values from DB or wherever)
                                                            // $start_date = '2025-05-24';
                                                            // $end_date = '2025-05-31';
                                                
                                                            $badgeClass = '';
                                                            $status = '';
                                                            $currentDate = new DateTime(); // current date
                                                
                                                            if (
                                                                !empty($start_date) && $start_date != '0000-00-00' &&
                                                                !empty($end_date) && $end_date != '0000-00-00'
                                                            ) {
                                                                $start = new DateTime($start_date);
                                                                $end = new DateTime($end_date);

                                                                if ($currentDate < $start) {
                                                                    $status = 'Upcoming';
                                                                } elseif ($currentDate >= $start && $currentDate <= $end) {
                                                                    $status = 'Active';
                                                                } else {
                                                                    $status = 'Expired';
                                                                }
                                                            } else {
                                                                $status = 'Unknown';
                                                            }

                                                            // Assign badge class based on status
                                                            switch ($status) {
                                                                case 'Upcoming':
                                                                    $badgeClass = 'badge-warning';
                                                                    break;
                                                                case 'Active':
                                                                    $badgeClass = 'badge-success';
                                                                    break;
                                                                case 'Expired':
                                                                    $badgeClass = 'badge-danger';
                                                                    break;
                                                                default:
                                                                    $badgeClass = 'badge-light';
                                                            }
                                                            ?>
                                                            <span
                                                                class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                                        </td>


                                                        <td class="text-center"><?= $value ?></td>
                                                        <td class="text-center"><?= htmlspecialchars($row['number_of_coupons']) ?>
                                                        </td>
                                                        <td class="text-center"><?= $start_date ?></td>
                                                        <td class="text-center"><?= $end_date ?></td>
                                                        <td class="text-center"><?= $created_at ?></td>
                                                        <td class="text-center"><?= htmlspecialchars($created_by) ?></td>
                                                        <td class="text-center">
                                                            <!-- View Button triggers the modal -->
                                                            <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                                data-target="#couponModal<?= $row['coupon_id'] ?>">
                                                                View
                                                            </button>

                                                            <!-- Modal -->
                                                            <div class="modal fade" id="couponModal<?= $row['coupon_id'] ?>"
                                                                tabindex="-1" role="dialog"
                                                                aria-labelledby="couponModalLabel<?= $row['coupon_id'] ?>"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered modal-lg"
                                                                    role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-warning text-dark">
                                                                            <h5 class="modal-title"
                                                                                id="couponModalLabel<?= $row['coupon_id'] ?>">
                                                                                <strong><?= htmlspecialchars($row['coupon_name']) ?></strong>
                                                                            </h5>
                                                                            <button type="button" class="close" data-dismiss="modal"
                                                                                aria-label="Close"><span
                                                                                    aria-hidden="true">&times;</span></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="text-left mb-4">
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>Coupon Value:</strong>
                                                                                            <?= htmlspecialchars($value); ?></p>
                                                                                        <p><strong>Start Date:</strong>
                                                                                            <?= htmlspecialchars($start_date); ?>
                                                                                        </p>
                                                                                        <p><strong>Created At:</strong>
                                                                                            <?= htmlspecialchars($created_at); ?>
                                                                                        </p>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <p><strong>Number of Coupons:</strong>
                                                                                            <?= htmlspecialchars($row['number_of_coupons']); ?>
                                                                                        </p>
                                                                                        <p><strong>Valid Until:</strong>
                                                                                            <?= htmlspecialchars($end_date); ?></p>
                                                                                        <p><strong>Created By:</strong>
                                                                                            <?= htmlspecialchars($created_by); ?>
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Coupon Code Table -->
                                                                            <div class="table-responsive">
                                                                                <table class="table table-bordered table-sm">
                                                                                    <thead class="thead-light">
                                                                                        <tr>
                                                                                            <th>No.</th>
                                                                                            <th>Coupon Code</th>
                                                                                            <th>Status</th>
                                                                                            <th>Date Used</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <?php
                                                                                        $code_query = "SELECT * FROM coupon_codes WHERE coupon_id = " . $row['coupon_id'];
                                                                                        $code_result = $conn->query($code_query);
                                                                                        $i = 1;
                                                                                        if ($code_result && $code_result->num_rows > 0) {
                                                                                            while ($code = $code_result->fetch_assoc()) {
                                                                                                $dateUsed = ($code['date_time_used'] == '0000-00-00 00:00:00') ? '' : date('F d, Y | g:i A', strtotime($code['date_time_used']));
                                                                                                $status = $code['status'];
                                                                                                echo "<tr>
                                                                                                    <td class='text-center'>{$i}</td>
                                                                                                    <td class='text-center'>" . htmlspecialchars($code['coupon_code']) . "</td>
                                                                                                    <td class='text-center'>{$status}</td>
                                                                                                    <td class='text-center'>{$dateUsed}</td>
                                                                                                </tr>";
                                                                                                $i++;
                                                                                            }
                                                                                        } else {
                                                                                            echo "<tr><td colspan='4' class='text-center'>No codes found.</td></tr>";
                                                                                        }
                                                                                        ?>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                            <!-- End Coupon Code Table -->
                                                                        </div>
                                                                        <div class="modal-footer">



                                                                            <!-- Delete Button -->
                                                                            <?php
                                                                            date_default_timezone_set('Asia/Manila');

                                                                            $today = new DateTime(); // today
                                                                            $eventDate = new DateTime($start_date);

                                                                            // Calculate the difference
                                                                            $interval = $today->diff($eventDate);
                                                                            $daysDifference = $interval->days;
                                                                            $isBeforeEvent = $today < $eventDate;

                                                                            // Show buttons only if we're more than 1 day before the event
                                                                            if ($isBeforeEvent && $daysDifference > 1) {
                                                                                ?>
                                                                                <a href="edit-coupon.php?coupon_id=<?= $row['coupon_id'] ?>"
                                                                                    class="btn btn-warning">Edit</a>
                                                                                <button class="btn btn-danger delete-btn"
                                                                                    data-id="<?= $row['coupon_id'] ?>">Delete</button>
                                                                                <?php
                                                                            }
                                                                            ?>

                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='7' class='text-center'>No coupons found.</td></tr>";
                                            }
                                            ?>
                                        </tbody>

                                    </table>
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


        <script>
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    const couponId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create a form dynamically
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = 'indexes/delete-coupon.php';

                            // Create an input for the coupon ID
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'coupon_id';
                            input.value = couponId;

                            // Append the input to the form
                            form.appendChild(input);

                            // Append the form to the body and submit it
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

        </script>


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
    header("Location: dashboard.php?AccessDenied=You do not have permission to access this page.");
    exit();
}