<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "edit-member.php";
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
    <title>Edit Member Information | FiT-LOGSYNC</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <script src="assets/js/sweetalert2.all.min.js"></script>
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">

    <!-- Fonts -->
    <link href="https://fonts.googleapis" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- jQuery and jQuery UI -->
    <link rel="stylesheet" href="assets/css/jquery-ui.css">
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/jquery-ui.js"></script>

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">

    <script src="assets/js/sessionExpired.js"></script>

    <style>
        .form-check-input:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }

        .error {
            border: 1px solid #DC3545 !important;
        }

        .error-message {
            color: #DC3545;
            font-size: 0.875rem;
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

                <?php $user_id = $_GET['user_id']; ?>

                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Edit Member Information</h1>
                        <a class="btn btn-secondary" href="manage-members.php"><i class="bi bi-arrow-return-left"></i>
                            Back</a>
                    </div>

                    <?php
                    $query = "
                        SELECT r.role_id
                        FROM users u
                        JOIN user_roles ur ON u.user_id = ur.user_id
                        JOIN roles r ON ur.role_id = r.role_id
                        WHERE u.user_id = $user_id
                    ";
                    $result = mysqli_query($conn, $query);

                    if (!$result) {
                        die("Query Failed: " . mysqli_error($conn));
                    }

                    $row = mysqli_fetch_assoc($result);

                    if ($row) {
                        $role_id = $row['role_id']; // Get role_id

                        if ($role_id == 5) {

                            ?>

                            <?php
                            $query = "SELECT * FROM users WHERE user_id = $user_id";
                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                die("Query Failed: " . mysqli_error($conn));
                            }
                            $row = mysqli_fetch_assoc($result);

                            $account_number = $row['account_number'];
                            $username = $row['username'];
                            $firstname = $row['firstname'];
                            $middlename = $row['middlename'];
                            $lastname = $row['lastname'];
                            $date_of_birth = $row['date_of_birth'];
                            $gender = $row['gender'];
                            $phone_number = $row['phone_number'];
                            $email = $row['email'];
                            $address = $row['address'];
                            $enrolled_by = $row['enrolled_by'];
                            $status = $row['status'];
                            $registration_date = $row['registration_date'];
                            $profile_image = $row['profile_image'];

                            // Fetch emergency contact details
                            $query = "SELECT * FROM emergency_contacts WHERE user_id = $user_id";
                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                die("Query Failed: " . mysqli_error($conn));
                            }

                            $emergency_contact = mysqli_fetch_assoc($result);

                            $contact_person = $emergency_contact['contact_person'];
                            $contact_number = $emergency_contact['contact_number'];
                            $relationship = $emergency_contact['relationship'];

                            // Fetch medical background details
                            $query = "SELECT * FROM medical_backgrounds WHERE user_id = $user_id";
                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                die("Query Failed: " . mysqli_error($conn));
                            }

                            $medical_background = mysqli_fetch_assoc($result);

                            $medical_conditions = $medical_background['medical_conditions'];
                            $current_medications = $medical_background['current_medications'];
                            $previous_injuries = $medical_background['previous_injuries'];
                            $par_q_1 = $medical_background['par_q_1'];
                            $par_q_2 = $medical_background['par_q_2'];
                            $par_q_3 = $medical_background['par_q_3'];
                            $par_q_4 = $medical_background['par_q_4'];
                            $par_q_5 = $medical_background['par_q_5'];
                            $par_q_6 = $medical_background['par_q_6'];
                            $par_q_7 = $medical_background['par_q_7'];
                            $par_q_8 = $medical_background['par_q_8'];
                            $par_q_9 = $medical_background['par_q_9'];
                            $par_q_10 = $medical_background['par_q_10'];

                            // Fetch waiver details
                            $query = "SELECT * FROM waivers WHERE user_id = $user_id";
                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                die("Query Failed: " . mysqli_error($conn));
                            }

                            $waiver = mysqli_fetch_assoc($result);

                            $rules_and_policy = $waiver['rules_and_policy'];
                            $liability_waiver = $waiver['liability_waiver'];
                            $cancellation_and_refund_policy = $waiver['cancellation_and_refund_policy'];
                            ?>

                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <div class="card-body p-4">
                                        <h3 class="mb-1 text-center"><strong>Edit Form</strong></h3>
                                        <?php
                                        $account_number = $row['account_number'];
                                        $formatted_account = substr($account_number, 0, 4) . '-' .
                                            substr($account_number, 4, 4) . '-' .
                                            substr($account_number, 8, 4) . '-' .
                                            substr($account_number, 12, 4);
                                        ?>
                                        <h4 class="h5 mb-0 text-gray-800"><strong>Account Number:</strong>
                                            <?php echo $formatted_account ?></h4>
                                        <h5 class="h5 mb-0 text-gray-800"><strong>Registration Date:</strong>
                                            <?php echo htmlspecialchars(date("F j, Y", timestamp: strtotime($registration_date))); ?>
                                        </h5>
                                        <h5 class="h5 mb-0 text-gray-800"><strong>Registered By:</strong>
                                            <?php echo $enrolled_by ?></h5>
                                        <h5 class="h5 mb-0 text-gray-800"><strong>Status:</strong>
                                            <?php echo $status ?></h5>

                                        <form id="registrationForm" action="indexes/edit-member-information.php" method="POST">
                                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                            <!-- Personal Information -->
                                            <h5 class="text-warning text-center"><strong>Personal Information</strong></h5>
                                            <div class="row g-3">
                                                <!-- Username -->
                                                <?php if (isset($_GET['username_already_exist'])): ?>
                                                    <div class="alert alert-danger text-center">
                                                        <?php echo htmlspecialchars($_GET['username_already_exist']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="username" name="username"
                                                            placeholder="Username"
                                                            value="<?php echo htmlspecialchars($username); ?>">
                                                        <label for="username">Username</label>
                                                        <div class="error-message" id="usernameError"></div>
                                                    </div>
                                                </div>
                                                <!-- Last Name -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="lastname" name="lastname"
                                                            placeholder="Last Name"
                                                            value="<?php echo htmlspecialchars($lastname); ?>">
                                                        <label for="lastname">Last Name</label>
                                                        <div class="error-message" id="lastnameError"></div>
                                                    </div>
                                                </div>
                                                <!-- First Name -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="firstname" name="firstname"
                                                            placeholder="First Name"
                                                            value="<?php echo htmlspecialchars($firstname); ?>">
                                                        <label for="firstname">First Name</label>
                                                        <div class="error-message" id="firstnameError"></div>
                                                    </div>
                                                </div>
                                                <!-- Middle Name -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="middlename"
                                                            name="middlename" placeholder="Middle Name"
                                                            value="<?php echo htmlspecialchars($middlename); ?>">
                                                        <label for="middlename">Middle Name</label>
                                                        <div class="error-message" id="middlenameError"></div>
                                                    </div>
                                                </div>
                                                <!-- Date of Birth -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="date" class="form-control" id="dateofbirth"
                                                            name="dateofbirth" placeholder="Date of Birth"
                                                            value="<?php echo htmlspecialchars($date_of_birth); ?>">
                                                        <label for="dateofbirth">Date of Birth</label>
                                                        <div class="error-message" id="dateofbirthError"></div>
                                                    </div>
                                                </div>
                                                <!-- Gender -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <select class="form-select" id="gender" name="gender">
                                                            <option value="">Select Gender</option>
                                                            <option value="Male" <?php echo ($gender === 'Male' ? 'selected' : ''); ?>>Male</option>
                                                            <option value="Female" <?php echo ($gender === 'Female' ? 'selected' : ''); ?>>Female</option>
                                                            <option value="Prefer not to say" <?php echo ($gender === 'Prefer not to say' ? 'selected' : ''); ?>>Prefer not to say</option>
                                                        </select>
                                                        <label for="gender">Gender</label>
                                                        <div class="error-message" id="genderError"></div>
                                                    </div>
                                                </div>
                                                <!-- Address -->
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="address" name="address"
                                                            placeholder="Address"
                                                            value="<?php echo htmlspecialchars($address); ?>">
                                                        <label for="address">Address</label>
                                                        <div class="error-message" id="addressError"></div>
                                                    </div>
                                                </div>
                                                <?php if (isset($_GET['email_already_taken'])): ?>
                                                    <div class="alert alert-danger text-center">
                                                        <?php echo htmlspecialchars($_GET['email_already_taken']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <!-- Phone Number -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="phonenumber"
                                                            name="phonenumber" placeholder="Phone Number"
                                                            value="<?php echo htmlspecialchars($phone_number); ?>">
                                                        <label for="phonenumber">Phone Number</label>
                                                        <div class="error-message" id="phonenumberError"></div>
                                                    </div>
                                                </div>
                                                <!-- Email -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="email" class="form-control" id="email" name="email"
                                                            placeholder="Email"
                                                            value="<?php echo htmlspecialchars($email); ?>">
                                                        <label for="email">Email</label>
                                                        <div class="error-message" id="emailError"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Medical Background -->
                                            <hr class="my-4">
                                            <h5 class="text-warning text-center"><strong>Contact of Emergency</strong></h5>
                                            <div class="row g-3">
                                                <!-- Contact Person -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="contact_person"
                                                            name="contact_person" placeholder="Contact Person"
                                                            value="<?php echo htmlspecialchars($contact_person); ?>">
                                                        <label for="contact_person">Contact Person</label>
                                                        <div class="error-message" id="contact_personError"></div>
                                                    </div>
                                                </div>
                                                <!-- Contact Number -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="contact_number"
                                                            name="contact_number" placeholder="Contact Number"
                                                            value="<?php echo htmlspecialchars($contact_number); ?>">
                                                        <label for="contact_number">Contact Number</label>
                                                        <div class="error-message" id="contact_numberError"></div>
                                                    </div>
                                                </div>
                                                <!-- Relationship -->
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <select class="form-select" id="relationship" name="relationship">
                                                            <option value="">Select Relationship</option>
                                                            <option value="Mother" <?php echo ($relationship === 'Mother' ? 'selected' : ''); ?>>Mother</option>
                                                            <option value="Father" <?php echo ($relationship === 'Father' ? 'selected' : ''); ?>>Father</option>
                                                            <option value="Sister" <?php echo ($relationship === 'Sister' ? 'selected' : ''); ?>>Sister</option>
                                                            <option value="Brother" <?php echo ($relationship === 'Brother' ? 'selected' : ''); ?>>Brother</option>
                                                            <option value="Friend" <?php echo ($relationship === 'Friend' ? 'selected' : ''); ?>>Friend</option>
                                                            <option value="Boyfriend" <?php echo ($relationship === 'Boyfriend' ? 'selected' : ''); ?>>Boyfriend</option>
                                                            <option value="Girlfriend" <?php echo ($relationship === 'Girlfriend' ? 'selected' : ''); ?>>Girlfriend</option>
                                                            <option value="Sister in Law" <?php echo ($relationship === 'Sister in Law' ? 'selected' : ''); ?>>Sister in Law</option>
                                                            <option value="Brother in Law" <?php echo ($relationship === 'Brother in Law' ? 'selected' : ''); ?>>Brother in Law</option>
                                                            <option value="Mother in Law" <?php echo ($relationship === 'Mother in Law' ? 'selected' : ''); ?>>Mother in Law</option>
                                                            <option value="Father in Law" <?php echo ($relationship === 'Father in Law' ? 'selected' : ''); ?>>Father in Law</option>
                                                            <option value="Wife" <?php echo ($relationship === 'Wife' ? 'selected' : ''); ?>>Wife</option>
                                                            <option value="Husband" <?php echo ($relationship === 'Husband' ? 'selected' : ''); ?>>Husband</option>
                                                            <option value="Daughter" <?php echo ($relationship === 'Daughter' ? 'selected' : ''); ?>>Daughter</option>
                                                            <option value="Cousin" <?php echo ($relationship === 'Cousin' ? 'selected' : ''); ?>>Cousin</option>
                                                            <option value="Godmother" <?php echo ($relationship === 'Godmother' ? 'selected' : ''); ?>>Godmother</option>
                                                            <option value="Godfather" <?php echo ($relationship === 'Godfather' ? 'selected' : ''); ?>>Godfather</option>
                                                        </select>
                                                        <label for="relationship">Relationship</label>
                                                        <div class="error-message" id="relationshipError"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Medical Background -->
                                            <hr class="my-4">
                                            <h5 class="text-warning text-center"><strong>Medical Background</strong></h5>
                                            <div class="row g-3">
                                                <!-- Medical Conditions -->
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <textarea class="form-control" id="medical_conditions"
                                                            name="medical_conditions" placeholder="Medical Conditions"
                                                            style="height: 100px"><?php echo htmlspecialchars($medical_conditions); ?></textarea>
                                                        <label for="medical_conditions">Medical Conditions</label>
                                                        <div class="error-message" id="medical_conditionsError"></div>
                                                    </div>
                                                </div>
                                                <!-- Current Medications -->
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <textarea class="form-control" id="current_medications"
                                                            name="current_medications" placeholder="Current Medications"
                                                            style="height: 100px"><?php echo htmlspecialchars($current_medications); ?></textarea>
                                                        <label for="current_medications">Current Medications</label>
                                                        <div class="error-message" id="current_medicationsError"></div>
                                                    </div>
                                                </div>
                                                <!-- Previous Injuries -->
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <textarea class="form-control" id="previous_injuries"
                                                            name="previous_injuries" placeholder="Previous Injuries"
                                                            style="height: 100px"><?php echo htmlspecialchars($previous_injuries); ?></textarea>
                                                        <label for="previous_injuries">Previous Injuries</label>
                                                        <div class="error-message" id="previous_injuriesError"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Physical Activity Readiness Questions (PAR-Q) -->
                                            <hr class="my-4">
                                            <h5 class="text-warning text-center"><strong>Physical Activity Readiness Questions
                                                    (PAR-Q)</strong></h5>
                                            <div class="row g-3">
                                                <!-- Q1 -->
                                                <div class="col-md-12">
                                                    <strong>Q1:</strong> Has your doctor ever said that you have a heart
                                                    condition and that you should only do physical activity recommended by a
                                                    doctor?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q1" id="q1_yes"
                                                            value="Yes" <?php echo ($par_q_1 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q1_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q1" id="q1_no"
                                                            value="No" <?php echo ($par_q_1 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q1_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q1Error"></div>
                                                </div>

                                                <!-- Q2 -->
                                                <div class="col-md-12">
                                                    <strong>Q2:</strong> Do you feel pain in your chest when you perform
                                                    physical
                                                    activity?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q2" id="q2_yes"
                                                            value="Yes" <?php echo ($par_q_2 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q2_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q2" id="q2_no"
                                                            value="No" <?php echo ($par_q_2 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q2_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q2Error"></div>
                                                </div>

                                                <!-- Q3 -->
                                                <div class="col-md-12">
                                                    <strong>Q3:</strong> In the past month, have you had chest pain when you
                                                    were
                                                    not doing physical activity?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q3" id="q3_yes"
                                                            value="Yes" <?php echo ($par_q_3 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q3_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q3" id="q3_no"
                                                            value="No" <?php echo ($par_q_3 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q3_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q3Error"></div>
                                                </div>

                                                <!-- Q4 -->
                                                <div class="col-md-12">
                                                    <strong>Q4:</strong> Do you lose your balance because of dizziness or do you
                                                    ever lose consciousness?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q4" id="q4_yes"
                                                            value="Yes" <?php echo ($par_q_4 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q4_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q4" id="q4_no"
                                                            value="No" <?php echo ($par_q_4 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q4_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q4Error"></div>
                                                </div>

                                                <!-- Q5 -->
                                                <div class="col-md-12">
                                                    <strong>Q5:</strong> Do you have a bone or joint problem that could be
                                                    worsened by a change in your physical activity?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q5" id="q5_yes"
                                                            value="Yes" <?php echo ($par_q_5 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q5_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q5" id="q5_no"
                                                            value="No" <?php echo ($par_q_5 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q5_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q5Error"></div>
                                                </div>

                                                <!-- Q6 -->
                                                <div class="col-md-12">
                                                    <strong>Q6:</strong> Is your doctor currently prescribing any medication for
                                                    your blood pressure or heart condition?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q6" id="q6_yes"
                                                            value="Yes" <?php echo ($par_q_6 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q6_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q6" id="q6_no"
                                                            value="No" <?php echo ($par_q_6 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q6_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q6Error"></div>
                                                </div>

                                                <!-- Q7 -->
                                                <div class="col-md-12">
                                                    <strong>Q7:</strong> Do you have any chronic medical conditions that may
                                                    affect your ability to exercise safely?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q7" id="q7_yes"
                                                            value="Yes" <?php echo ($par_q_7 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q7_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q7" id="q7_no"
                                                            value="No" <?php echo ($par_q_7 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q7_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q7Error"></div>
                                                </div>

                                                <!-- Q8 -->
                                                <div class="col-md-12">
                                                    <strong>Q8:</strong> Are you pregnant or have you given birth in the last 6
                                                    months?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q8" id="q8_yes"
                                                            value="Yes" <?php echo ($par_q_8 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q8_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q8" id="q8_no"
                                                            value="No" <?php echo ($par_q_8 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q8_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q8Error"></div>
                                                </div>

                                                <!-- Q9 -->
                                                <div class="col-md-12">
                                                    <strong>Q9:</strong> Do you have any recent injuries or surgeries that may
                                                    limit your physical activity?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q9" id="q9_yes"
                                                            value="Yes" <?php echo ($par_q_9 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q9_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q9" id="q9_no"
                                                            value="No" <?php echo ($par_q_9 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q9_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q9Error"></div>
                                                </div>

                                                <!-- Q10 -->
                                                <div class="col-md-12">
                                                    <strong>Q10:</strong> Do you know of any other reason why you should not do
                                                    physical activity?
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q10" id="q10_yes"
                                                            value="Yes" <?php echo ($par_q_10 === 'Yes' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q10_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="q10" id="q10_no"
                                                            value="No" <?php echo ($par_q_10 === 'No' ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="q10_no">No</label>
                                                    </div>
                                                    <div class="error-message" id="q10Error"></div>
                                                </div>
                                            </div>

                                            <!-- Waiver/Agreements -->
                                            <hr class="my-4">
                                            <h5 class="text-warning text-center"><strong>Waiver and Agreements</strong></h5>
                                            <div class="row g-3">
                                                <!-- Rules and Policy -->
                                                <div class="col-md-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="waiver_rules"
                                                            name="waiver_rules" <?php echo ($rules_and_policy ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="waiver_rules">The member agrees to the <a href="rule_and_policy.php" target="_blank"><strong>Rules and Policy</strong></a>.</label>
                                                        <div class="error-message" id="waiver_rulesError"></div>
                                                    </div>
                                                </div>
                                                <!-- Liability Waiver -->
                                                <div class="col-md-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="waiver_liability"
                                                            name="waiver_liability" <?php echo ($liability_waiver ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="waiver_liability">The member agrees to the <a href="liability_waiver.php" target="_blank"><strong>Liability Waiver</strong></a>.</label>
                                                        <div class="error-message" id="waiver_liabilityError"></div>
                                                    </div>
                                                </div>
                                                <!-- Cancellation and Refund Policy -->
                                                <div class="col-md-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="waiver_cancel"
                                                            name="waiver_cancel" <?php echo ($cancellation_and_refund_policy ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="waiver_cancel">The member agrees to the <a href="cancellation_and_refund_policy.php" target="_blank"><strong>Cancellation and Refund Policy</strong></a>.</label>
                                                        <div class="error-message" id="waiver_cancelError"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="d-grid mt-12 my-4 ">
                                                <button type="submit" class="btn btn-warning" name="register">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php

                        } else {
                            ?>
                        <div class="d-flex justify-content-center align-items-center" style="height: 50vh;">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <div class="card-body p-4">
                                        <h3 class="mb-1 text-center text-secondary"><strong>This user is not a member</strong>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                    } else {
                        ?>
                    <div class="d-flex justify-content-center align-items-center" style="height: 50vh;">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="card-body p-4">
                                    <h3 class="mb-1 text-center text-secondary"><strong>This user is not existing</strong>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    }

                    ?>
            </div>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function (event) {
            let isValid = true;

            // Personal Information Validation
            const username = document.getElementById('username');
            const lastname = document.getElementById('lastname');
            const firstname = document.getElementById('firstname');
            const dateofbirth = document.getElementById('dateofbirth');
            const gender = document.getElementById('gender');
            const address = document.getElementById('address');
            const phonenumber = document.getElementById('phonenumber');
            const email = document.getElementById('email');
            const contact_person = document.getElementById('contact_person');
            const contact_number = document.getElementById('contact_number');
            const relationship = document.getElementById('relationship');

            const fields = [{
                element: username,
                errorId: 'usernameError',
                message: 'Username is required'
            },
            {
                element: lastname,
                errorId: 'lastnameError',
                message: 'Last Name is required'
            },
            {
                element: firstname,
                errorId: 'firstnameError',
                message: 'First Name is required'
            },
            {
                element: dateofbirth,
                errorId: 'dateofbirthError',
                message: 'Date of Birth is required'
            },
            {
                element: gender,
                errorId: 'genderError',
                message: 'Gender is required'
            },
            {
                element: address,
                errorId: 'addressError',
                message: 'Address is required'
            },
            {
                element: phonenumber,
                errorId: 'phonenumberError',
                message: 'Phone Number must be 11 digits and start with "09"'
            },
            {
                element: email,
                errorId: 'emailError',
                message: 'Please enter a valid email address'
            },
            {
                element: contact_person,
                errorId: 'contact_personError',
                message: 'Contact Person name is required'
            },
            {
                element: contact_number,
                errorId: 'contact_numberError',
                message: 'Phone Number must be 11 digits and start with "09"'
            },
            {
                element: relationship,
                errorId: 'relationshipError',
                message: 'Relationship is required'
            }
            ];

            fields.forEach(field => {
                if (!field.element.value) {
                    document.getElementById(field.errorId).innerHTML = `<i class="bi bi-exclamation-circle"></i> ${field.message}`;
                    field.element.classList.add('error');
                    isValid = false;
                } else {
                    document.getElementById(field.errorId).innerHTML = '';
                    field.element.classList.remove('error');
                }
            });

            // Phone Number Validation
            if (phonenumber.value && !/^09\d{9}$/.test(phonenumber.value)) {
                document.getElementById('phonenumberError').innerHTML = `<i class="bi bi-exclamation-circle"></i> Phone Number must be 11 digits and start with "09"`;
                phonenumber.classList.add('error');
                isValid = false;
            }

            // Email Validation
            if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                document.getElementById('emailError').innerHTML = `<i class="bi bi-exclamation-circle"></i> Please enter a valid email address`;
                email.classList.add('error');
                isValid = false;
            }

            // Contact Phone Number Validation
            if (contact_number.value && !/^09\d{9}$/.test(contact_number.value)) {
                document.getElementById('contact_numberError').innerHTML = `<i class="bi bi-exclamation-circle"></i> Phone Number must be 11 digits and start with "09"`;
                contact_number.classList.add('error');
                isValid = false;
            }

            // Medical Background Validation
            const medicalFields = [{
                element: document.getElementById('medical_conditions'),
                errorId: 'medical_conditionsError',
                message: 'Medical Conditions is required'
            },
            {
                element: document.getElementById('current_medications'),
                errorId: 'current_medicationsError',
                message: 'Current Medications is required'
            },
            {
                element: document.getElementById('previous_injuries'),
                errorId: 'previous_injuriesError',
                message: 'Previous Injuries is required'
            }
            ];

            medicalFields.forEach(field => {
                if (!field.element.value) {
                    document.getElementById(field.errorId).innerHTML = `<i class="bi bi-exclamation-circle"></i> ${field.message}`;
                    field.element.classList.add('error');
                    isValid = false;
                } else {
                    document.getElementById(field.errorId).innerHTML = '';
                    field.element.classList.remove('error');
                }
            });

            // PAR-Q Validation
            for (let i = 1; i <= 10; i++) {
                const q = document.querySelector(`input[name="q${i}"]:checked`);
                if (!q) {
                    document.getElementById(`q${i}Error`).innerHTML = `<i class="bi bi-exclamation-circle"></i> This question is required`;
                    isValid = false;
                } else {
                    document.getElementById(`q${i}Error`).innerHTML = '';
                }
            }

            // Waiver/Agreements Validation
            const waiverFields = [{
                element: document.getElementById('waiver_rules'),
                errorId: 'waiver_rulesError',
                message: 'You must agree to the rules and policy.'
            },
            {
                element: document.getElementById('waiver_liability'),
                errorId: 'waiver_liabilityError',
                message: 'You must agree to the liability waiver'
            },
            {
                element: document.getElementById('waiver_cancel'),
                errorId: 'waiver_cancelError',
                message: 'You must agree to the cancellation and refund policy'
            }
            ];

            waiverFields.forEach(field => {
                if (!field.element.checked) {
                    document.getElementById(field.errorId).innerHTML = `<i class="bi bi-exclamation-circle"></i> ${field.message}`;
                    isValid = false;
                } else {
                    document.getElementById(field.errorId).innerHTML = '';
                }
            });

            if (isValid) {
                this.submit();
            } else {
                event.preventDefault();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const usernameError = document.getElementById('usernameError');
            const emailError = document.getElementById('emailError');

            // Function to check if a value exists in the database
            async function checkIfExists(type, value) {
                try {
                    const response = await fetch(`check_exists.php?type=${type}&value=${encodeURIComponent(value)}`);
                    const data = await response.json();
                    return data.exists;
                } catch (error) {
                    console.error('Error checking existence:', error);
                    return false;
                }
            }

            // Validate username in real-time
            usernameInput.addEventListener('blur', async () => {
                const username = usernameInput.value.trim();
                if (username) {
                    const exists = await checkIfExists('username', username);
                    if (exists) {
                        usernameInput.classList.add('error');
                        usernameError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Username is already taken`;
                    } else {
                        usernameInput.classList.remove('error');
                        usernameError.innerHTML = '';
                    }
                }
            });

            // Validate email in real-time
            emailInput.addEventListener('blur', async () => {
                const email = emailInput.value.trim();
                if (email) {
                    const exists = await checkIfExists('email', email);
                    if (exists) {
                        emailInput.classList.add('error');
                        emailError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Email is already used`;
                    } else {
                        email.classList.remove('error');
                        emailError.innerHTML = '';
                    }
                }
            });
        });

        document.getElementById('registrationForm').addEventListener('submit', async function (event) {
            let isValid = true;

            // Personal Information Validation
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const usernameError = document.getElementById('usernameError');
            const emailError = document.getElementById('emailError');

            // Function to check if a value exists in the database
            async function checkIfExists(type, value) {
                try {
                    const response = await fetch(`check_exists.php?type=${type}&value=${encodeURIComponent(value)}`);
                    const data = await response.json();
                    return data.exists;
                } catch (error) {
                    console.error('Error checking existence:', error);
                    return false;
                }
            }

            // Check if username exists
            if (username.value.trim()) {
                const usernameExists = await checkIfExists('username', username.value.trim());
                if (usernameExists) {
                    username.classList.add('error');
                    usernameError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Username is already taken`;
                    isValid = false;
                } else {
                    username.classList.remove('error');
                    usernameError.innerHTML = '';
                }
            }

            // Check if email exists
            if (email.value.trim()) {
                const emailExists = await checkIfExists('email', email.value.trim());
                if (emailExists) {
                    email.classList.add('error');
                    emailError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Email is already used`;
                    isValid = false;
                } else {
                    email.classList.remove('error');
                    emailError.innerHTML = '';
                }
            }

            // If username or email is already taken, prevent form submission
            if (!isValid) {
                event.preventDefault();
            }
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