<?php include 'session-management.php'; ?>
<?php
// Check if the user was logged in
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "settings.php";
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
        <title>Settings | FiT-LOGSYNC</title>
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
            /* Custom switch color */
            .custom-switch .custom-control-input:checked~.custom-control-label::before {
                background-color: #F6C23E;
                border-color: #F6C23E;
            }

            .custom-switch .custom-control-input:focus~.custom-control-label::before {
                box-shadow: 0 0 0 0.2rem rgba(246, 194, 62, 0.25);
            }

            .custom-switch .custom-control-label::before {
                background-color: #e9ecef;
                border-color: #adb5bd;
            }

            .custom-switch .custom-control-label::after {
                background-color: #ffffff;
            }

            .password-toggle-icon {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
            }

            .card-content {
                display: none;
            }

            .card-header {
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px;
                background-color: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
            }

            .card-header h2 {
                margin: 0;
            }

            .card {
                margin-bottom: 10px;
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

        if (isset($_GET['success'])) {
            $message = htmlspecialchars($_GET['success']);
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

        if (isset($_GET['error'])) {
            $message = htmlspecialchars($_GET['error']);
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
                            <h1 class="h3 mb-0 text-gray-800">strong</h1>
                        </div>

                        <!-- Main content -->
                        <section class="content">
                            <div class="row justify-content-left">

                                <div class="col-xl-12 col-md-8 mb-2">
                                    <div class="card shadow h-100 py-2">
                                        <div class="card-header">
                                            <h4 class="mb-0">My Profile</h4>
                                            <span>▼</span>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="container mt-1">
                                                    <div class="row justify-content-center">
                                                        <div class="col-md-8">
                                                            <div class="card-body ">
                                                                <div class="row justify-content-center">
                                                                    <div class="text-center">
                                                                        <a href="change-profile-photo.php">
                                                                            <img class="profile-picture img-fluid rounded-circle"
                                                                                style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; cursor: pointer;"
                                                                                src="assets/profile-pictures/<?php echo $_SESSION['profile_image']; ?>?<?php echo time(); ?>"
                                                                                alt="User profile picture">
                                                                        </a>
                                                                    </div>

                                                                </div>

                                                                <h3 class="text-center">
                                                                    <?php echo $_SESSION['firstname'] . ' ' . $_SESSION['middlename'] . ' ' . $_SESSION['lastname']; ?>
                                                                </h3>
                                                                <p class="text-muted text-center">
                                                                    @<?php echo $_SESSION['username']; ?>
                                                                </p>
                                                                <ul class="list-group list-group-unbordered mb-3">
                                                                    <li class="list-group-item">
                                                                        <b>Last Name</b> <a class="float-right">
                                                                            <?php echo $_SESSION['lastname']; ?>
                                                                        </a>
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <b>First Name</b> <a class="float-right">
                                                                            <?php echo $_SESSION['firstname']; ?>
                                                                        </a>
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <b>Middle Name</b> <a class="float-right">
                                                                            <?php echo $_SESSION['middlename']; ?>
                                                                        </a>
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <?php
                                                                        $account_number = $_SESSION['account_number'];
                                                                        $formatted_account = substr($account_number, 0, 4) . '-' .
                                                                            substr($account_number, 4, 4) . '-' .
                                                                            substr($account_number, 8, 4) . '-' .
                                                                            substr($account_number, 12, 4);
                                                                        ?>
                                                                        <b>Account Number</b> <a class="float-right">
                                                                            <?php echo $formatted_account; ?>
                                                                        </a>
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <b>Gender</b> <a class="float-right">
                                                                            <?php echo $_SESSION['gender']; ?>
                                                                        </a>
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <b>Phone Number</b>
                                                                        <a class="float-right">
                                                                            <?php echo $_SESSION['phone_number']; ?>
                                                                        </a>
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        <b>Email Address</b> <a class="float-right">
                                                                            <?php echo $_SESSION['email']; ?>
                                                                        </a>
                                                                    </li>
                                                                    <br>
                                                                    <!-- <a href="edit-profile.php"
                                                                        class="btn btn-warning btn-block">Edit my
                                                                        profile</a> -->
                                                                </ul>
                                                                <hr>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Contact of Emergency -->
                                                    <?php
                                                    $emergency_contact_query = "SELECT user_id, contact_person, contact_number, relationship FROM emergency_contacts WHERE user_id = {$_SESSION['user_id']}";
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
                                                    <?php

                                                    $role_id = $_SESSION['role_id'];

                                                    if ($role_id == 5) {
                                                        $medical_backgrounds_query = "SELECT * FROM medical_backgrounds WHERE user_id = {$_SESSION['user_id']}";
                                                        $medical_backgrounds_result = mysqli_query($conn, $medical_backgrounds_query);

                                                        if (!$medical_backgrounds_result) {
                                                            die("Query Failed: " . mysqli_error($conn));
                                                        }
                                                        $medical_backgrounds_row = mysqli_fetch_assoc($medical_backgrounds_result);
                                                        ?>

                                                        <hr>
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
                                                                        class="fas fa-running"></i> Physical Activity Readiness
                                                                    Questions (PAR-Q)</strong></h5>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <strong>Q1:</strong> Has your doctor ever said that you have
                                                                    a
                                                                    heart condition and that you should only do physical
                                                                    activity
                                                                    recommended by a doctor?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_1']); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Q2:</strong> Do you feel pain in your chest when you
                                                                    perform physical activity?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_2']); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Q3:</strong> In the past month, have you had chest
                                                                    pain
                                                                    when you were not doing physical activity?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_3']); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Q4:</strong> Do you lose your balance because of
                                                                    dizziness or do you ever lose consciousness?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_4']); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Q5:</strong> Do you have a bone or joint problem
                                                                    that
                                                                    could be worsened by a change in your physical activity?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_5']); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Q6:</strong> Is your doctor currently prescribing
                                                                    any
                                                                    medication for your blood pressure or heart condition?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_6']); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Q7:</strong> Do you have any chronic medical
                                                                    conditions
                                                                    that may affect your ability to exercise safely?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_7']); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Q8:</strong> Are you pregnant or have you given
                                                                    birth in
                                                                    the last 6 months?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_8']); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Q9:</strong> Do you have any recent injuries or
                                                                    surgeries that may limit your physical activity?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_9']); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <strong>Q10:</strong> Do you know of any other reason why
                                                                    you
                                                                    should not do physical activity?
                                                                    <p><strong>Answer:</strong>
                                                                        <?php echo htmlspecialchars($medical_backgrounds_row['par_q_10']); ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    $security_questions_query = "SELECT * FROM security_questions WHERE user_id = {$_SESSION['user_id']}";
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
                                                    <?php
                                                    $role_id = $_SESSION['role_id'];

                                                    if ($role_id == 5) {
                                                        $waivers_query = "SELECT * FROM waivers WHERE user_id = {$_SESSION['user_id']}";
                                                        $waivers_result = mysqli_query($conn, $waivers_query);

                                                        if (!$waivers_result) {
                                                            die("Query Failed: " . mysqli_error($conn));
                                                        }
                                                        $waivers_row = mysqli_fetch_assoc($waivers_result);
                                                    ?>
                                                    <hr>
                                                        <div class="section">
                                                            <h5 class="text-center text-warning"><strong><i class="fas fa-file-signature"></i> Waiver and Agreements</strong></h5>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label style="pointer-events: none; cursor: default;">
                                                                        <input type="checkbox" <?php echo ($waivers_row['rules_and_policy'] == '1') ? 'checked' : ''; ?>
                                                                            style="accent-color: #F6C23E; pointer-events: none; opacity: 1;">
                                                                        <strong>Agree to the Rules and Policy</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label style="pointer-events: none; cursor: default;">
                                                                        <input type="checkbox" <?php echo ($waivers_row['liability_waiver'] == '1') ? 'checked' : ''; ?>
                                                                            style="accent-color: #F6C23E; pointer-events: none; opacity: 1;">
                                                                        <strong>Agree to the Liability Waiver</strong>
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label style="pointer-events: none; cursor: default;">
                                                                        <input type="checkbox" <?php echo ($waivers_row['cancellation_and_refund_policy'] == '1') ? 'checked' : ''; ?>
                                                                            style="accent-color: #F6C23E; pointer-events: none; opacity: 1;">
                                                                        <strong>Agree to the Cancellation and Refund Policy</strong>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <?php
                                                    }
                                                    ?>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-8 mb-2">
                                    <div class="card shadow h-100 py-2">
                                        <div class="card-header">
                                            <h4 class="mb-0">Change Password</h4>
                                            <span>▼</span>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="container mt-1">
                                                    <form method="POST" action="indexes/change_password.php">
                                                        <div class="form-group position-relative">
                                                            <label for="current_password">Current Password</label>
                                                            <input type="password" class="form-control"
                                                                name="current_password" id="current_password">
                                                            <div class="error-message" id="currentPasswordError"></div>
                                                            <i class="bi bi-eye-slash password-toggle-icon"
                                                                id="toggleCurrentPassword"></i>
                                                        </div>
                                                        <div class="form-group position-relative">
                                                            <label for="new_password">New Password</label>
                                                            <input type="password" class="form-control" name="new_password"
                                                                id="new_password">
                                                            <div class="error-message" id="newPasswordError"></div>
                                                            <i class="bi bi-eye-slash password-toggle-icon"
                                                                id="toggleNewPassword"></i>
                                                        </div>
                                                        <div class="form-group position-relative">
                                                            <label for="confirm_password">Confirm New Password</label>
                                                            <input type="password" class="form-control"
                                                                name="confirm_password" id="confirm_password">
                                                            <div class="error-message" id="confirmPasswordError"></div>
                                                            <i class="bi bi-eye-slash password-toggle-icon"
                                                                id="toggleConfirmPassword"></i>
                                                        </div>
                                                        <button type="submit" class="btn btn-warning mt-3"
                                                            name="changePassword">Update Password</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-md-8 mb-2">
                                    <div class="card shadow h-100 py-2">
                                        <div class="card-header">
                                            <h4 class="mb-0">Two-Factor Authentication</h4>
                                            <span>▼</span>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="container mt-1">
                                                    <form id="twoFactorForm" method="POST" action="indexes/2fa.php">
                                                        <div class="form-group">
                                                            <label for="twoFactorSwitch">Enable Two-Factor
                                                                Authentication</label>
                                                            <div class="custom-control custom-switch">
                                                                <?php
                                                                $id = $_SESSION['user_id'];
                                                                $query = "SELECT * FROM users WHERE user_id = $id";
                                                                $result = mysqli_query($conn, $query);
                                                                $row = mysqli_fetch_assoc($result);
                                                                ?>
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="twoFactorSwitch" name="twoFactorSwitch" <?php echo $row['two_factor_authentication'] ? 'checked' : ''; ?>>
                                                                <label class="custom-control-label"
                                                                    for="twoFactorSwitch"></label>
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-warning mt-3"
                                                            name="2faButton">Save
                                                            Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Verification Modal -->
        <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordModalLabel">Verify Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="indexes/2fa.php">
                            <div class="form-group">
                                <label for="password">Enter your password to continue:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <input type="hidden" name="twoFactorSwitch" id="modalTwoFactorSwitch" value="">
                            <button type="submit" class="btn btn-warning mt-3" name="verifyPassword">Verify</button>
                        </form>
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
            $(document).ready(function () {
                // Show the password modal when the form is submitted
                $('#twoFactorForm').on('submit', function (e) {
                    e.preventDefault(); // Prevent the form from submitting immediately

                    // Get the current state of the switch
                    var twoFactorSwitch = $('#twoFactorSwitch').is(':checked') ? 1 : 0;

                    // Set the value of the hidden input in the modal
                    $('#modalTwoFactorSwitch').val(twoFactorSwitch);

                    // Show the password modal
                    $('#passwordModal').modal('show');
                });

                // Toggle password visibility
                $('#toggleCurrentPassword').click(function () {
                    const currentPassword = $('#current_password');
                    const type = currentPassword.attr('type') === 'password' ? 'text' : 'password';
                    currentPassword.attr('type', type);
                    $(this).toggleClass('bi-eye bi-eye-slash');
                });

                $('#toggleNewPassword').click(function () {
                    const newPassword = $('#new_password');
                    const type = newPassword.attr('type') === 'password' ? 'text' : 'password';
                    newPassword.attr('type', type);
                    $(this).toggleClass('bi-eye bi-eye-slash');
                });

                $('#toggleConfirmPassword').click(function () {
                    const confirmPassword = $('#confirm_password');
                    const type = confirmPassword.attr('type') === 'password' ? 'text' : 'password';
                    confirmPassword.attr('type', type);
                    $(this).toggleClass('bi-eye bi-eye-slash');
                });

                // Password validation
                $('form[action="indexes/change_password.php"]').on('submit', function (event) {
                    let isValid = true;

                    // Current Password Validation
                    const currentPassword = $('#current_password');
                    const currentPasswordError = $('#currentPasswordError');

                    // Reset error messages and styles
                    currentPasswordError.html('');
                    currentPassword.removeClass('is-invalid');

                    // Validate current password
                    if (!currentPassword.val()) {
                        currentPasswordError.html('<i class="bi bi-exclamation-circle"></i> Current password is required');
                        currentPassword.addClass('is-invalid');
                        isValid = false;
                    }

                    // New Password Validation
                    const newPassword = $('#new_password');
                    const newPasswordError = $('#newPasswordError');

                    // Reset error messages and styles
                    newPasswordError.html('');
                    newPassword.removeClass('is-invalid');

                    // Validate new password
                    if (!newPassword.val()) {
                        newPasswordError.html('<i class="bi bi-exclamation-circle"></i> New password is required');
                        newPassword.addClass('is-invalid');
                        isValid = false;
                    } else if (newPassword.val().length < 8) {
                        newPasswordError.html('<i class="bi bi-exclamation-circle"></i> Password must be at least 8 characters long');
                        newPassword.addClass('is-invalid');
                        isValid = false;
                    } else if (!/[A-Z]/.test(newPassword.val())) {
                        newPasswordError.html('<i class="bi bi-exclamation-circle"></i> Password must contain at least one uppercase letter');
                        newPassword.addClass('is-invalid');
                        isValid = false;
                    } else if (!/[0-9]/.test(newPassword.val())) {
                        newPasswordError.html('<i class="bi bi-exclamation-circle"></i> Password must contain at least one numeric character');
                        newPassword.addClass('is-invalid');
                        isValid = false;
                    } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(newPassword.val())) {
                        newPasswordError.html('<i class="bi bi-exclamation-circle"></i> Password must contain at least one special character');
                        newPassword.addClass('is-invalid');
                        isValid = false;
                    } else {
                        newPasswordError.html('');
                        newPassword.removeClass('is-invalid');
                    }

                    // Confirm Password Validation
                    const confirmPassword = $('#confirm_password');
                    const confirmPasswordError = $('#confirmPasswordError');

                    // Reset error messages and styles
                    confirmPasswordError.html('');
                    confirmPassword.removeClass('is-invalid');

                    // Validate confirm password
                    if (!confirmPassword.val()) {
                        confirmPasswordError.html('<i class="bi bi-exclamation-circle"></i> Confirm password is required');
                        confirmPassword.addClass('is-invalid');
                        isValid = false;
                    } else if (newPassword.val() !== confirmPassword.val()) {
                        confirmPasswordError.html('<i class="bi bi-exclamation-circle"></i> Passwords do not match');
                        confirmPassword.addClass('is-invalid');
                        isValid = false;
                    } else {
                        confirmPasswordError.html('');
                        confirmPassword.removeClass('is-invalid');
                    }

                    // Prevent form submission if validation fails
                    if (!isValid) {
                        event.preventDefault(); // Prevent the form from submitting
                    }
                });

                // Toggle card content
                $('.card-header').click(function () {
                    // Close any open card content
                    $('.card-content').not($(this).siblings('.card-content')).slideUp();
                    $('.card-header span').not($(this).find('span')).text('▼');

                    // Toggle the clicked card content
                    $(this).siblings('.card-content').slideToggle();
                    $(this).find('span').text($(this).find('span').text() === '▼' ? '▲' : '▼');
                });
            });
        </script>

    </body>

    </html>


    <?php
} else {
    header("Location: dashboard.php?AccessDenied=You have no permission to access this page");
    exit();
}
