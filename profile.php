<?php include 'session-management.php'; ?>

<?php
// Check if the user was logged in
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "profile.php";
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
        <title>My Profile | FiT-LOGSYNC</title>
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
                            <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
                        </div>

                        <!-- Main content -->
                        <section class="content">
                            <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <div class="col-md-10">
                                        <div class="card card-primary card-outline">
                                            <div class="card-body box-profile">
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
                                                <p class="text-muted text-center">@<?php echo $_SESSION['username']; ?>
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
                                                    <li class="list-group-item">
                                                        <b>Address</b> <a class="float-right">
                                                            <?php echo $_SESSION['address']; ?>
                                                        </a>
                                                    </li>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <b>Date of Birth</b> <a class="float-right">
                                                            <?php $dob = $_SESSION['date_of_birth'];
                                                            echo htmlspecialchars(date("F j, Y", strtotime($dob))); ?>
                                                        </a>
                                                    </li>
                                                </ul>
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
                                                                    class="fas fa-notes-medical"></i> Medical Background</strong></h5>
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
                                                                <strong>Q1:</strong> Has your doctor ever said that you have a
                                                                heart condition and that you should only do physical activity
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
                                                                <strong>Q3:</strong> In the past month, have you had chest pain
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
                                                                <strong>Q5:</strong> Do you have a bone or joint problem that
                                                                could be worsened by a change in your physical activity?
                                                                <p><strong>Answer:</strong>
                                                                    <?php echo htmlspecialchars($medical_backgrounds_row['par_q_5']); ?>
                                                                </p>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <strong>Q6:</strong> Is your doctor currently prescribing any
                                                                medication for your blood pressure or heart condition?
                                                                <p><strong>Answer:</strong>
                                                                    <?php echo htmlspecialchars($medical_backgrounds_row['par_q_6']); ?>
                                                                </p>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <strong>Q7:</strong> Do you have any chronic medical conditions
                                                                that may affect your ability to exercise safely?
                                                                <p><strong>Answer:</strong>
                                                                    <?php echo htmlspecialchars($medical_backgrounds_row['par_q_7']); ?>
                                                                </p>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <strong>Q8:</strong> Are you pregnant or have you given birth in
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
                                                                <strong>Q10:</strong> Do you know of any other reason why you
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



                                                <hr>
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
                        </section>
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
    </body>

    </html>

    <?php
} else {
    header("Location: dashboard.php?AccessDenied=You have no permission to access this page");
    exit();
}
?>