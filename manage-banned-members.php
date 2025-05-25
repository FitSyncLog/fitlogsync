<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "manage-banned-members.php";
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
    <title>Manage Banned Members | FiT-LOGSYNC</title>
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
                        <h1 class="h3 mb-0 text-gray-800">Manage Banned Members</h1>
                        <a class="btn btn-warning" href="create-new-member.php">Add New Member</a>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Account Number</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Gender</th>
                                            <th class="text-center">Date of Birth</th>
                                            <th class="text-center">Subscription Status</th>
                                            <th class="text-center">Registration Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>

                                    <?php
                                    $query = "SELECT users.* 
                                    FROM users 
                                    JOIN user_roles ON users.user_id = user_roles.user_id 
                                    WHERE user_roles.role_id = 5 
                                    AND users.status != 'Delete' 
                                    AND users.status = 'Banned'";
                                    $result = mysqli_query($conn, $query);

                                    if (!$result) {
                                        die("Query Failed: " . mysqli_error($conn));
                                    }
                                    ?>

                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?php
                                                    $account_number = $row['account_number'];
                                                    $formatted_account = substr($account_number, 0, 4) . '-' .
                                                        substr($account_number, 4, 4) . '-' .
                                                        substr($account_number, 8, 4) . '-' .
                                                        substr($account_number, 12, 4);
                                                    echo htmlspecialchars($formatted_account);
                                                    ?>
                                                </td>

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
                                                    
                                                </td>



                                                <td class="text-center">
                                                    <?php
                                                    $reg_date = $row['registration_date'];
                                                    echo htmlspecialchars(date("F j, Y", strtotime($reg_date)));
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
                                                                            <?php echo htmlspecialchars(date("F j, Y", strtotime($reg_date))); ?>
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
                                                            <!-- Medical Background -->
                                                            <?php
                                                            $medical_backgrounds_query = "SELECT * FROM medical_backgrounds WHERE user_id = {$row['user_id']}";
                                                            $medical_backgrounds_result = mysqli_query($conn, $medical_backgrounds_query);

                                                            if (!$medical_backgrounds_result) {
                                                                die("Query Failed: " . mysqli_error($conn));
                                                            }
                                                            $medical_backgrounds_row = mysqli_fetch_assoc($medical_backgrounds_result);

                                                            ?>

                                                            <div class="section">
                                                                <h5 class="text-center text-warning"><strong><i
                                                                            class="fas fa-notes-medical"></i> Medical
                                                                        Background</strong></h5>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <p><strong>Medical Conditions:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['medical_conditions']); ?>
                                                                        </p>
                                                                        <p><strong>Current Medications:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['current_medications']); ?>
                                                                        </p>
                                                                        <p><strong>Previous Injuries:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['previous_injuries']); ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <!-- Physical Activity Readiness Questions (PAR-Q) -->
                                                            <div class="section">
                                                                <h5 class="text-center text-warning"><strong><i
                                                                            class="fas fa-running"></i> Physical Activity
                                                                        Readiness Questions (PAR-Q)</strong></h5>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <strong>Q1:</strong> Has your doctor ever said that
                                                                        you have a heart condition and that you should only
                                                                        do physical activity recommended by a doctor?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_1']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <strong>Q2:</strong> Do you feel pain in your chest
                                                                        when you perform physical activity?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_2']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <strong>Q3:</strong> In the past month, have you had
                                                                        chest pain when you were not doing physical
                                                                        activity?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_3']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <strong>Q4:</strong> Do you lose your balance
                                                                        because of dizziness or do you ever lose
                                                                        consciousness?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_4']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <strong>Q5:</strong> Do you have a bone or joint
                                                                        problem that could be worsened by a change in your
                                                                        physical activity?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_5']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <strong>Q6:</strong> Is your doctor currently
                                                                        prescribing any medication for your blood pressure
                                                                        or heart condition?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_6']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <strong>Q7:</strong> Do you have any chronic medical
                                                                        conditions that may affect your ability to exercise
                                                                        safely?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_7']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <strong>Q8:</strong> Are you pregnant or have you
                                                                        given birth in the last 6 months?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_8']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <strong>Q9:</strong> Do you have any recent injuries
                                                                        or surgeries that may limit your physical activity?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_9']); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <strong>Q10:</strong> Do you know of any other
                                                                        reason why you should not do physical activity?
                                                                        <p><strong>Answer:</strong>
                                                                            <?php echo htmlspecialchars($medical_backgrounds_row['par_q_10']); ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <hr>
                                                            <!-- Waiver/Agreements -->
                                                            <?php
                                                            $waivers_query = "SELECT * FROM waivers WHERE user_id = {$row['user_id']}";
                                                            $waivers_result = mysqli_query($conn, $waivers_query);

                                                            if (!$waivers_result) {
                                                                die("Query Failed: " . mysqli_error($conn));
                                                            }
                                                            $waivers_row = mysqli_fetch_assoc($waivers_result);

                                                            ?>

                                                            <div class="section">
                                                                <h5 class="text-center text-warning"><strong><i
                                                                            class="fas fa-file-signature"></i> Waiver and
                                                                        Agreements</strong></h5>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label
                                                                            style="pointer-events: none; cursor: default;">
                                                                            <input type="checkbox" <?php echo ($waivers_row['rules_and_policy'] == '1') ? 'checked' : ''; ?>
                                                                                style="accent-color: #F6C23E; pointer-events: none; opacity: 1;">
                                                                            <strong>Agree to the Rules and Policy</strong>
                                                                        </label>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label
                                                                            style="pointer-events: none; cursor: default;">
                                                                            <input type="checkbox" <?php echo ($waivers_row['liability_waiver'] == '1') ? 'checked' : ''; ?>
                                                                                style="accent-color: #F6C23E; pointer-events: none; opacity: 1;">
                                                                            <strong>Agree to the Liability Waiver</strong>
                                                                        </label>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label
                                                                            style="pointer-events: none; cursor: default;">
                                                                            <input type="checkbox" <?php echo ($waivers_row['cancellation_and_refund_policy'] == '1') ? 'checked' : ''; ?>
                                                                                style="accent-color: #F6C23E; pointer-events: none; opacity: 1;">
                                                                            <strong>Agree to the Cancellation and Refund
                                                                                Policy</strong>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer"
                                                            style="display: flex; justify-content: center; gap: 10px;">
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

    <script>
        document.getElementById('delete-button').addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default link behavior
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');

            Swal.fire({
                title: 'Are you sure?',
                text: `Are you sure you want to delete ${userName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete-member.php?user_id=${userId}`;
                }
            });
        });
    </script>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
</body>

</html>

<?php
} else {
    header("Location: dashboard.php?AccessDenied=You have no permission to access this page");
    exit();
}