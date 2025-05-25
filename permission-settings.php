<?php include 'session-management.php'; ?>
<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "permission-settings.php";
$role_id = $_SESSION['role_id'];
$query = "SELECT * FROM permissions WHERE page_name = ? AND role_id = ? AND permission = 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $page_name, $role_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch all roles and pages for the permissions table
    $roles_query = "SELECT role_id, role FROM roles";
    $roles_result = $conn->query($roles_query);
    $roles = [];
    while ($row = $roles_result->fetch_assoc()) {
        $roles[$row['role_id']] = $row['role'];
    }

    $pages_query = "SELECT DISTINCT page_name FROM permissions";
    $pages_result = $conn->query($pages_query);
    $pages = [];
    while ($row = $pages_result->fetch_assoc()) {
        $pages[] = $row['page_name'];
    }

    // Fetch current permissions
    $permissions_query = "SELECT role_id, page_name, permission FROM permissions";
    $permissions_result = $conn->query($permissions_query);
    $permissions = [];
    while ($row = $permissions_result->fetch_assoc()) {
        $permissions[$row['role_id']][$row['page_name']] = $row['permission'];
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Role Permission Settings | FiT-LOGSYNC</title>
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
        <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
        <script src="assets/js/sweetalert2.all.min.js"></script>
        <script src="assets/js/sessionExpired.js"></script>

        <style>
            input[type="checkbox"].custom-yellow:checked {
                background-color: #ffc107;
                border-color: #ffc107;
            }

            input[type="checkbox"].custom-yellow {
                accent-color: #ffc107;
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
        ?>

        <?php
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

        <!-- Page Wrapper -->
        <div id="wrapper">

            <?php include 'layout/sidebar.php'; ?>

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Main Content -->
                <div id="content">

                    <?php include 'layout/navbar.php'; ?>

                    <!-- Begin Page Content -->
                    <div class="container-fluid">

                        <!-- Page Heading -->
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">Role Permission Settings</h1>
                            <div class="text-right">
                                <p class="text-gray-600" id="currentDateTime"></p>
                            </div>
                        </div>

                        <!-- Content Row -->
                        <div class="row">
                            <div class="col-12">
                                <form id="permissionForm" method="POST" action="indexes/update-permissions.php">
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center">
                                            <thead class="bg-warning text-light">
                                                <tr>
                                                    <th class="text-left">Pages</th>
                                                    <?php foreach ($roles as $role_id => $role_name): ?>
                                                        <th><?php echo htmlspecialchars($role_name); ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pages as $page): ?>
                                                    <tr>
                                                        <td class="text-left">
                                                            <?php echo ucwords(str_replace('-', ' ', $page)); ?>
                                                        </td>
                                                        <?php foreach ($roles as $role_id => $role_name): ?>
                                                            <td>
                                                                <div class="form-check justify-content-center d-flex">
                                                                    <input class="form-check-input custom-yellow" type="checkbox"
                                                                        name="permissions[<?php echo $role_id; ?>][<?php echo $page; ?>]"
                                                                        <?php if (isset($permissions[$role_id][$page]) && $permissions[$role_id][$page] == 1)
                                                                            echo 'checked'; ?>>
                                                                </div>
                                                            </td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-warning" data-toggle="modal"
                                        data-target="#passwordModal">
                                        Save Changes
                                    </button>

                                </form>

                                <!-- Password Confirmation Modal -->
                                <div class="modal fade" id="passwordModal" tabindex="-1"
                                    aria-labelledby="passwordModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form id="modalForm">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="passwordModalLabel">Confirm Changes</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <label for="modalPassword">Enter your password to confirm:</label>
                                                    <input type="password" class="form-control" id="modalPassword" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-warning">Confirm</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                </div>
                <!-- End of Main Content -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Core JavaScript Files -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>

        <!-- Page Level Plugins -->
        <script src="vendor/chart.js/Chart.min.js"></script>

        <!-- Page Level Custom Scripts -->
        <script src="js/demo/chart-area-demo.js"></script>
        <script src="js/demo/chart-pie-demo.js"></script>

        <!-- Initialize Bootstrap dropdowns -->
        <script>
            $(document).ready(function () {
                // Enable all dropdown toggles
                $('.dropdown-toggle').dropdown();

                // Initialize tooltips if needed
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>

        <script>
            document.getElementById("modalForm").addEventListener("submit", function (e) {
                e.preventDefault();

                const passwordInput = document.getElementById("modalPassword").value;

                // Create hidden input and append to form
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name = "confirm_password";
                hiddenInput.value = passwordInput;

                document.getElementById("permissionForm").appendChild(hiddenInput);

                // Submit the main form
                document.getElementById("permissionForm").submit();
            });
        </script>


    </body>

    </html>
    <?php
} else {
    header("Location: dashboard.php?AccessDenied=You have no permission to access this page");
    exit();
}
?>