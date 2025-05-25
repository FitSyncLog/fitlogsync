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
        <title>Create New Transaction | FiT-LOGSYNC</title>
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
        <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />
        <script src="assets/js/sweetalert2.all.min.js"></script>
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
                            <h1 class="h3 mb-0 text-gray-800">Create New Transaction</h1>
                            <a class="btn btn-secondary" href="manage-payments.php">Back</a>

                        </div>


                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="text-center text-muted my-3">
                                    <i class="fas fa-solid fa-cash-register fa-10x"></i>
                                </div>

                                <form action="indexes/create-new-transaction.php" method="POST">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="member_id">Member's Name</label>
                                            <select class="form-control select2" id="member_id" name="member_id" required>
                                                <option value="">Select a member</option>
                                                <?php
                                                // Fetch active members with role_id = 5
                                                $member_query = "SELECT u.user_id, u.account_number, u.lastname, u.firstname 
                                                               FROM users u 
                                                               INNER JOIN user_roles ur ON u.user_id = ur.user_id 
                                                               WHERE ur.role_id = 5 AND u.status = 'Active' 
                                                               ORDER BY u.lastname, u.firstname";
                                                $member_result = $conn->query($member_query);
                                                
                                                while ($member = $member_result->fetch_assoc()) {
                                                    $display_text = $member['account_number'] . ' - ' . 
                                                                  $member['lastname'] . ', ' . $member['firstname'];
                                                    echo '<option value="' . $member['user_id'] . '">' . 
                                                         htmlspecialchars($display_text) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <a class="btn btn-secondary" href="manage-payments.php">Cancel</a>
                                        <button type="submit" class="btn btn-warning" name="newTransaction">Create New Transaction</button>
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
        <!-- Add Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- Rest -->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="js/demo/datatables-demo.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize Select2
                $('.select2').select2({
                    placeholder: 'Search by account number or name...',
                    allowClear: true,
                    width: '100%',
                    // Configure search
                    matcher: function(params, data) {
                        // If there are no search terms, return all of the data
                        if ($.trim(params.term) === '') {
                            return data;
                        }

                        // Do not display the item if there is no 'text' property
                        if (typeof data.text === 'undefined') {
                            return null;
                        }

                        // Search in both account number and name
                        if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                            return data;
                        }

                        // Return `null` if the term should not be displayed
                        return null;
                    }
                });
            });
        </script>
    </body>

    </html>
    <?php
} else {
    header("Location: indexes/logout.php");
    exit();
}