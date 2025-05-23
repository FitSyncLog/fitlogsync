<?php include 'session-management.php'; ?>

<?php
// Check if the user was logged in
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}
// Prepare the query to check permissions
$page_name = "manage-front-desk.php";
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
        <title>Manage Front Desk | FiT-LOGSYNC</title>
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
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
            .form-check-input[type="radio"] {
                accent-color: #F6C23E;
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
                            <h1 class="h3 mb-0 text-gray-800">Manage Front Desk</h1>
                            <a class="btn btn-warning" href="create-new-front-desk.php">Add Front Desk</a>
                        </div>

                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Name</th>
                                                <th class="text-center">Gender</th>
                                                <th class="text-center">Date of Birth</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>

                                        <?php
                                        $query = "SELECT users.*  FROM users  JOIN user_roles ON users.user_id = user_roles.user_id  WHERE user_roles.role_id = 3  AND users.status != 'Deleted'";
                                        $result = mysqli_query($conn, $query);

                                        if (!$result) {
                                            die("Query Failed: " . mysqli_error($conn));
                                        }
                                        ?>

                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>

                                                    <td class="text-center">
                                                        <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']); ?>
                                                    </td>
                                                    <td class="text-center"><?php echo htmlspecialchars($row['gender']); ?></td>
                                                    <td class="text-center">
                                                        <?php
                                                        $dob = $row['date_of_birth'];
                                                        echo htmlspecialchars(date("F j, Y", strtotime($dob)));
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                            data-target="#viewMemberModal<?php echo $row['user_id']; ?>">View</button>
                                                    </td>
                                                </tr>

                                                <!-- Modal -->
                                                <div class="modal fade" id="viewMemberModal<?php echo $row['user_id']; ?>"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="viewMemberModalLabel<?php echo $row['user_id']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div
                                                                class="modal-header bg-warning text-white d-flex justify-content-between align-items-center">
                                                                <h5 class="modal-title"
                                                                    id="viewMemberModalLabel<?php echo $row['user_id']; ?>">
                                                                    Member Details
                                                                </h5>

                                                                <div class="d-flex align-items-center">
                                                                    <!-- Dropdown Button -->
                                                                    <div class="dropdown">
                                                                        <button
                                                                            class="btn btn-outline-light text-light dropdown-toggle"
                                                                            type="button" id="statusDropdown"
                                                                            data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            Change Account Status
                                                                        </button>
                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="statusDropdown">
                                                                            <?php
                                                                            $statuses = ['Pending', 'Active', 'Suspended', 'Banned'];
                                                                            foreach ($statuses as $status) {
                                                                                echo '<a class="dropdown-item" href="indexes/edit-status-member.php?user_id=' . $row['user_id'] . '&status=' . urlencode($status) . '">' . $status . '</a>';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Close Button -->
                                                                    <button type="button" class="close ml-2"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="modal-body">
                                                                <!-- Personal Information -->
                                                                <div class="section">
                                                                    <h5 class="text-center text-warning"><strong><i
                                                                                class="fas fa-user"></i> Personal
                                                                            Information</strong></h5>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <p><strong>Account Number:</strong>
                                                                                <?php
                                                                                $account_number = $row['account_number'];
                                                                                $formatted_account = substr($account_number, 0, 4) . '-' .
                                                                                    substr($account_number, 4, 4) . '-' .
                                                                                    substr($account_number, 8, 4) . '-' .
                                                                                    substr($account_number, 12, 4);
                                                                                ?>
                                                                                <?php echo htmlspecialchars($formatted_account); ?>
                                                                            </p>
                                                                            <p><strong>Last Name:</strong>
                                                                                <?php echo htmlspecialchars($row['lastname']); ?>
                                                                            </p>
                                                                            <p><strong>First Name:</strong>
                                                                                <?php echo htmlspecialchars($row['firstname']); ?>
                                                                            </p>
                                                                            <p><strong>Middle Name:</strong>
                                                                                <?php echo htmlspecialchars($row['middlename']); ?>
                                                                            </p>
                                                                            <p><strong>Gender:</strong>
                                                                                <?php echo htmlspecialchars($row['gender']); ?>
                                                                            </p>
                                                                            <p><strong>Address:</strong>
                                                                                <?php echo htmlspecialchars($row['address']); ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <p><strong>Date of Birth:</strong>
                                                                                <?php echo htmlspecialchars(date("F j, Y", strtotime($dob))); ?>
                                                                            </p>
                                                                            <p><strong>Status:</strong>
                                                                                <?php echo htmlspecialchars($row['status']); ?>
                                                                            </p>
                                                                            <p><strong>Registration Date:</strong>
                                                                                <?php
                                                                                $reg_date = $row['registration_date'];
                                                                                echo htmlspecialchars(date("F j, Y", strtotime($reg_date)));
                                                                                ?>
                                                                            </p>
                                                                            <p><strong>Email:</strong>
                                                                                <?php echo htmlspecialchars($row['email']); ?>
                                                                            </p>
                                                                            <p><strong>Phone Number:</strong>
                                                                                <?php echo htmlspecialchars($row['phone_number']); ?>
                                                                            </p>
                                                                            <p><strong>Registered By:</strong>
                                                                                <?php echo htmlspecialchars($row['enrolled_by']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <!-- Contact of Emergency -->
                                                                <?php
                                                                $emergency_contact_query = "SELECT user_id, contact_person, contact_number, relationship FROM emergency_contacts WHERE user_id = {$row['user_id']}";
                                                                $emergency_contact_result = mysqli_query($conn, $emergency_contact_query);

                                                                if (!$emergency_contact_result) {
                                                                    die("Query Failed: " . mysqli_error($conn));
                                                                }
                                                                $emergency_contact_row = mysqli_fetch_assoc($emergency_contact_result);
                                                                ?>
                                                                <div class="section">
                                                                    <h5 class="text-center text-warning"><strong><i
                                                                                class="fas fa-exclamation-triangle"></i> Contact
                                                                            of Emergency</strong></h5>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <p><strong>Contact Person:</strong>
                                                                                <?php echo htmlspecialchars($emergency_contact_row['contact_person']); ?>
                                                                            </p>
                                                                            <p><strong>Contact Number:</strong>
                                                                                <?php echo htmlspecialchars($emergency_contact_row['contact_number']); ?>
                                                                            </p>
                                                                            <p><strong>Relationship:</strong>
                                                                                <?php echo htmlspecialchars($emergency_contact_row['relationship']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <hr>
                                                                <?php
                                                                $security_questions_query = "SELECT * FROM security_questions WHERE user_id = {$row['user_id']}";
                                                                $security_questions_result = mysqli_query($conn, $security_questions_query);

                                                                if (!$security_questions_result) {
                                                                    die("Query Failed: " . mysqli_error($conn));
                                                                }
                                                                $security_questions_row = mysqli_fetch_assoc($security_questions_result);

                                                                ?>

                                                                <!-- Personal Information -->
                                                                <div class="section">
                                                                    <h5 class="text-center text-warning"><strong><i
                                                                                class="fas fa-solid fa-question"></i> Security
                                                                            Questions</strong></h5>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <strong>Security Question 1:</strong>
                                                                            <?php echo htmlspecialchars($security_questions_row['sq1']); ?>
                                                                            <p><strong>Answer:</strong>
                                                                                <?php echo htmlspecialchars($security_questions_row['sq1_res']); ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <strong>Security Question 2:</strong>
                                                                            <?php echo htmlspecialchars($security_questions_row['sq2']); ?>
                                                                            <p><strong>Answer:</strong>
                                                                                <?php echo htmlspecialchars($security_questions_row['sq2_res']); ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <strong>Security Question 3:</strong>
                                                                            <?php echo htmlspecialchars($security_questions_row['sq3']); ?>
                                                                            <p><strong>Answer:</strong>
                                                                                <?php echo htmlspecialchars($security_questions_row['sq3_res']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>


                                                            </div>
                                                            <div class="modal-footer"
                                                                style="display: flex; justify-content: center; gap: 10px;">

                                                                <button class="btn btn-danger"
                                                                    onclick="confirmDelete(<?php echo $row['user_id']; ?>)">Delete</button>

                                                                <a href="edit-member.php?user_id=<?php echo $row['user_id']; ?>"
                                                                    class="btn btn-warning">Edit</a>

                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
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

        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="js/demo/datatables-demo.js"></script>

        <script>
            function confirmDelete(userId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the delete-member.php page with the user_id as a query parameter
                        window.location.href = 'indexes/delete-member.php?user_id=' + userId;
                    }
                });
            }
        </script>

    </body>

    </html>

    <?php
} else {
    header("Location: dashboard.php?AccessDenied=You have no permission to access this page");
    exit();
}
?>