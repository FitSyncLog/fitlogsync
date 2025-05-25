<?php include 'session-management.php'; ?>

<?php
// Check if the user was login
if (!isset($_SESSION['login'])) {
    header("Location: login.php?LoginFirst=Please login first");
    exit();
}

// Prepare the query to check permissions
$page_name = "create-new-member.php";
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
        <title>Register New Member | FiT-LOGSYNC</title>
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

        <link rel="stylesheet" type="text/css" href="bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="style.css" />
        <link rel="icon" type="image/x-icon" href="assets/fitlogsync.ico">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com" rel="preconnect">
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

            .toggle-password {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                padding: 5px;
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
                            <h1 class="h3 mb-0 text-gray-800">Create new member</h1>
                            <a class="btn btn-secondary" href="manage-members.php"><i class="bi bi-arrow-return-left"></i>
                                Back</a>
                        </div>


                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="card-body p-4">
                                    <h3 class="mb-4 text-center"><strong>Registration Form</strong></h3>

                                    <form id="registrationForm" action="indexes/register-new-member.php" method="POST">
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
                                                        value="<?php echo htmlspecialchars($_GET['username'] ?? ''); ?>">
                                                    <label for="username">Username</label>
                                                    <div class="error-message" id="usernameError"></div>
                                                </div>
                                            </div>
                                            <!-- Last Name -->
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                                        placeholder="Last Name"
                                                        value="<?php echo htmlspecialchars($_GET['lastname'] ?? ''); ?>">
                                                    <label for="lastname">Last Name</label>
                                                    <div class="error-message" id="lastnameError"></div>
                                                </div>
                                            </div>
                                            <!-- First Name -->
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                                        placeholder="First Name"
                                                        value="<?php echo htmlspecialchars($_GET['firstname'] ?? ''); ?>">
                                                    <label for="firstname">First Name</label>
                                                    <div class="error-message" id="firstnameError"></div>
                                                </div>
                                            </div>
                                            <!-- Middle Name -->
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="middlename"
                                                        name="middlename" placeholder="Middle Name"
                                                        value="<?php echo htmlspecialchars($_GET['middlename'] ?? ''); ?>">
                                                    <label for="middlename">Middle Name</label>
                                                    <div class="error-message" id="middlenameError"></div>
                                                </div>
                                            </div>
                                            <!-- Date of Birth -->
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="date" class="form-control" id="dateofbirth"
                                                        name="dateofbirth" placeholder="Date of Birth"
                                                        value="<?php echo htmlspecialchars($_GET['dateofbirth'] ?? ''); ?>">
                                                    <label for="dateofbirth">Date of Birth</label>
                                                    <div class="error-message" id="dateofbirthError"></div>
                                                </div>
                                            </div>
                                            <!-- Gender -->
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="gender" name="gender">
                                                        <option value="">Select Gender</option>
                                                        <option value="Male" <?php echo (isset($_GET['gender']) && $_GET['gender'] === 'Male' ? 'selected' : ''); ?>>Male</option>
                                                        <option value="Female" <?php echo (isset($_GET['gender']) && $_GET['gender'] === 'Female' ? 'selected' : ''); ?>>Female
                                                        </option>
                                                        <option value="Prefer not to say" <?php echo (isset($_GET['gender']) && $_GET['gender'] === 'Prefer not to say' ? 'selected' : ''); ?>>
                                                            Prefer not to say</option>
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
                                                        value="<?php echo htmlspecialchars($_GET['address'] ?? ''); ?>">
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
                                                        value="<?php echo htmlspecialchars($_GET['phonenumber'] ?? ''); ?>">
                                                    <label for="phonenumber">Phone Number</label>
                                                    <div class="error-message" id="phonenumberError"></div>
                                                </div>
                                            </div>
                                            <!-- Email -->
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        placeholder="Email"
                                                        value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                                                    <label for="email">Email</label>
                                                    <div class="error-message" id="emailError"></div>
                                                </div>
                                            </div>
                                            <!-- Password -->
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" placeholder="Password">
                                                    <label for="password">Password</label>
                                                    <div class="error-message" id="passwordError"></div>
                                                </div>
                                                <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                                            </div>

                                            <!-- Confirm Password -->
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="password" class="form-control" id="confirm_password"
                                                        name="confirm_password" placeholder="Confirm Password">
                                                    <label for="confirm_password">Confirm Password</label>
                                                    <div class="error-message" id="confirm_passwordError"></div>
                                                </div>
                                                <i class="bi bi-eye-slash toggle-password" id="toggleConfirmPassword"></i>
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
                                                        value="<?php echo htmlspecialchars($_GET['contact_person'] ?? ''); ?>">
                                                    <label for="contact_person">Contact Person</label>
                                                    <div class="error-message" id="contact_personError"></div>
                                                </div>
                                            </div>
                                            <!-- Contact Number -->
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="contact_number"
                                                        name="contact_number" placeholder="Contact Number"
                                                        value="<?php echo htmlspecialchars($_GET['contact_number'] ?? ''); ?>">
                                                    <label for="contact_number">Contact Number</label>
                                                    <div class="error-message" id="contact_numberError"></div>
                                                </div>
                                            </div> <!-- Relationship -->
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <select class="form-select" id="relationship" name="relationship">
                                                        <option value="">Select Relationship</option>
                                                        <option value="Mother" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Mother' ? 'selected' : ''); ?>>Mother
                                                        </option>
                                                        <option value="Father" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Father' ? 'selected' : ''); ?>>Father
                                                        </option>
                                                        <option value="Sister" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Sister' ? 'selected' : ''); ?>>Sister
                                                        </option>
                                                        <option value="Brother" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Brother' ? 'selected' : ''); ?>>Brother
                                                        </option>
                                                        <option value="Friend" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Friend' ? 'selected' : ''); ?>>
                                                            Friend</option>
                                                        <option value="Boyfriend" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Boyfriend' ? 'selected' : ''); ?>>
                                                            Boyfriend</option>
                                                        <option value="Girlfriend" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Girlfriend' ? 'selected' : ''); ?>>
                                                            Girlfriend</option>
                                                        <option value="Sister in Law" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Sister in Law' ? 'selected' : ''); ?>>
                                                            Sister in Law</option>
                                                        <option value="Brother in Law" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Brother in Law' ? 'selected' : ''); ?>>
                                                            Brother in Law</option>
                                                        <option value="Mother in Law" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Mother in Law' ? 'selected' : ''); ?>>
                                                            Mother in Law</option>
                                                        <option value="Father in Law" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Father in Law' ? 'selected' : ''); ?>>
                                                            Father in Law</option>
                                                        <option value="Wife" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Wife' ? 'selected' : ''); ?>>Wife
                                                        </option>
                                                        <option value="Husband" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Husband' ? 'selected' : ''); ?>>Husband
                                                        </option>
                                                        <option value="Daughter" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Daughter' ? 'selected' : ''); ?>>
                                                            Daughter</option>
                                                        <option value="Cousin" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Cousin' ? 'selected' : ''); ?>>
                                                            Cousin</option>
                                                        <option value="Godmother" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Godmother' ? 'selected' : ''); ?>>
                                                            Godmother</option>
                                                        <option value="Godfather" <?php echo (isset($_GET['relationship']) && $_GET['relationship'] === 'Godfather' ? 'selected' : ''); ?>>
                                                            Godfather</option>
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
                                                        style="height: 100px"><?php echo htmlspecialchars($_GET['medical_conditions'] ?? ''); ?></textarea>
                                                    <label for="medical_conditions">Medical Conditions</label>
                                                    <div class="error-message" id="medical_conditionsError"></div>
                                                </div>
                                            </div>
                                            <!-- Current Medications -->
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="current_medications"
                                                        name="current_medications" placeholder="Current Medications"
                                                        style="height: 100px"><?php echo htmlspecialchars($_GET['current_medications'] ?? ''); ?></textarea>
                                                    <label for="current_medications">Current Medications</label>
                                                    <div class="error-message" id="current_medicationsError"></div>
                                                </div>
                                            </div>
                                            <!-- Previous Injuries -->
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="previous_injuries"
                                                        name="previous_injuries" placeholder="Previous Injuries"
                                                        style="height: 100px"><?php echo htmlspecialchars($_GET['previous_injuries'] ?? ''); ?></textarea>
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
                                                        value="Yes" <?php echo (isset($_GET['q1']) && $_GET['q1'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q1_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q1" id="q1_no"
                                                        value="No" <?php echo (isset($_GET['q1']) && $_GET['q1'] === 'No' ? 'checked' : ''); ?>>
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
                                                        value="Yes" <?php echo (isset($_GET['q2']) && $_GET['q2'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q2_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q2" id="q2_no"
                                                        value="No" <?php echo (isset($_GET['q2']) && $_GET['q2'] === 'No' ? 'checked' : ''); ?>>
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
                                                        value="Yes" <?php echo (isset($_GET['q3']) && $_GET['q3'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q3_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q3" id="q3_no"
                                                        value="No" <?php echo (isset($_GET['q3']) && $_GET['q3'] === 'No' ? 'checked' : ''); ?>>
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
                                                        value="Yes" <?php echo (isset($_GET['q4']) && $_GET['q4'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q4_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q4" id="q4_no"
                                                        value="No" <?php echo (isset($_GET['q4']) && $_GET['q4'] === 'No' ? 'checked' : ''); ?>>
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
                                                        value="Yes" <?php echo (isset($_GET['q5']) && $_GET['q5'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q5_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q5" id="q5_no"
                                                        value="No" <?php echo (isset($_GET['q5']) && $_GET['q5'] === 'No' ? 'checked' : ''); ?>>
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
                                                        value="Yes" <?php echo (isset($_GET['q6']) && $_GET['q6'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q6_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q6" id="q6_no"
                                                        value="No" <?php echo (isset($_GET['q6']) && $_GET['q6'] === 'No' ? 'checked' : ''); ?>>
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
                                                        value="Yes" <?php echo (isset($_GET['q7']) && $_GET['q7'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q7_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q7" id="q7_no"
                                                        value="No" <?php echo (isset($_GET['q7']) && $_GET['q7'] === 'No' ? 'checked' : ''); ?>>
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
                                                        value="Yes" <?php echo (isset($_GET['q8']) && $_GET['q8'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q8_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q8" id="q8_no"
                                                        value="No" <?php echo (isset($_GET['q8']) && $_GET['q8'] === 'No' ? 'checked' : ''); ?>>
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
                                                        value="Yes" <?php echo (isset($_GET['q9']) && $_GET['q9'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q9_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q9" id="q9_no"
                                                        value="No" <?php echo (isset($_GET['q9']) && $_GET['q9'] === 'No' ? 'checked' : ''); ?>>
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
                                                        value="Yes" <?php echo (isset($_GET['q10']) && $_GET['q10'] === 'Yes' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q10_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="q10" id="q10_no"
                                                        value="No" <?php echo (isset($_GET['q10']) && $_GET['q10'] === 'No' ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="q10_no">No</label>
                                                </div>
                                                <div class="error-message" id="q10Error"></div>
                                            </div>
                                        </div>

                                        <!-- Security Questions -->
                                        <hr class="my-4">
                                        <h5 class="text-warning text-center"><strong>Security Questions</strong></h5>
                                        <div class="row g-3">
                                            <!-- Security Question 1 -->
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <select class="form-select" id="security_question1"
                                                        name="security_question1" onchange="updateQuestionOptions()">
                                                        <option value="">Select Security Question 1</option>
                                                        <option value="What is your mother’s maiden name?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What is your mother’s maiden name?' ? 'selected' : ''); ?>>What is your mother's maiden name?
                                                        </option>
                                                        <option value="In what city were you born?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'In what city were you born?' ? 'selected' : ''); ?>>In what city were you born?</option>
                                                        <option value="What is your father's middle name?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What is your father middle name?' ? 'selected' : ''); ?>>What is your father's middle name?
                                                        </option>
                                                        <option value="What was your childhood nickname?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What was your childhood nickname?' ? 'selected' : ''); ?>>What was your childhood
                                                            nickname?</option>
                                                        <option value="What is your grandmother's first name?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What is your grandmother first name?' ? 'selected' : ''); ?>>What is your grandmother's first
                                                            name?
                                                        </option>
                                                        <option value="What was the name of your first school?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What was the name of your first school?' ? 'selected' : ''); ?>>What was the name of your first
                                                            school?</option>
                                                        <option value="What is the name of your first pet?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What is the name of your first pet?' ? 'selected' : ''); ?>>What is the name of your first pet?
                                                        </option>
                                                        <option value="What was the make and model of your first car?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What was the make and model of your first car?' ? 'selected' : ''); ?>>What was the make and
                                                            model of
                                                            your first car?</option>
                                                        <option value="What was the name of your childhood best friend?"
                                                            <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What was the name of your childhood best friend?' ? 'selected' : ''); ?>>What was the name
                                                            of your
                                                            childhood best friend?</option>
                                                        <option value="In what city did your parents meet?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'In what city did your parents meet?' ? 'selected' : ''); ?>>In what city did your parents
                                                            meet?</option>
                                                        <option value="What is your favorite book?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What is your favorite book?' ? 'selected' : ''); ?>>What is your favorite book?</option>
                                                        <option value="What was the first concert you attended?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What was the first concert you attended?' ? 'selected' : ''); ?>>What was the first concert you
                                                            attended?</option>
                                                        <option value="What is your favorite movie?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What is your favorite movie?' ? 'selected' : ''); ?>>What is your favorite movie?</option>
                                                        <option value="What is your favorite food?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What is your favorite food?' ? 'selected' : ''); ?>>What is your favorite food?</option>
                                                        <option value="What is your favorite childhood TV show?" <?php echo (isset($_GET['security_question1']) && $_GET['security_question1'] === 'What is your favorite childhood TV show?' ? 'selected' : ''); ?>>What is your favorite childhood
                                                            TV
                                                            show?</option>
                                                    </select>
                                                    <label for="security_question1">Security Question 1</label>
                                                    <div class="error-message" id="security_question1Error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="security_answer1"
                                                        name="security_answer1" placeholder="Answer for Security Question 1"
                                                        value="<?php echo htmlspecialchars($_GET['security_answer1'] ?? ''); ?>">
                                                    <label for="security_answer1">Answer for Security Question 1</label>
                                                    <div class="error-message" id="security_answer1Error"></div>
                                                </div>
                                            </div>

                                            <!-- Security Question 2 -->
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <select class="form-select" id="security_question2"
                                                        name="security_question2" onchange="updateQuestionOptions()">
                                                        <option value="">Select Security Question 2</option>
                                                        <!-- Options will be populated by JavaScript -->
                                                    </select>
                                                    <label for="security_question2">Security Question 2</label>
                                                    <div class="error-message" id="security_question2Error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="security_answer2"
                                                        name="security_answer2" placeholder="Answer for Security Question 2"
                                                        value="<?php echo htmlspecialchars($_GET['security_answer2'] ?? ''); ?>">
                                                    <label for="security_answer2">Answer for Security Question 2</label>
                                                    <div class="error-message" id="security_answer2Error"></div>
                                                </div>
                                            </div>

                                            <!-- Security Question 3 -->
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <select class="form-select" id="security_question3"
                                                        name="security_question3" onchange="updateQuestionOptions()">
                                                        <option value="">Select Security Question 3</option>
                                                        <!-- Options will be populated by JavaScript -->
                                                    </select>
                                                    <label for="security_question3">Security Question 3</label>
                                                    <div class="error-message" id="security_question3Error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="security_answer3"
                                                        name="security_answer3" placeholder="Answer for Security Question 3"
                                                        value="<?php echo htmlspecialchars($_GET['security_answer3'] ?? ''); ?>">
                                                    <label for="security_answer3">Answer for Security Question 3</label>
                                                    <div class="error-message" id="security_answer3Error"></div>
                                                </div>
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
                                                        name="waiver_rules" <?php echo (isset($_GET['waiver_rules']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="waiver_rules">The member agree to
                                                        the <a href="rule_and_policy.php" target="_blank"><strong>Rules and
                                                                Policy</strong></a>.</label>
                                                    <div class="error-message" id="waiver_rulesError"></div>
                                                </div>
                                            </div>
                                            <!-- Liability Waiver -->
                                            <div class="col-md-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="waiver_liability"
                                                        name="waiver_liability" <?php echo (isset($_GET['waiver_liability']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="waiver_liability">The member agree
                                                        to the <a href="liability_waiver.php"
                                                            target="_blank"><strong>Liability
                                                                Waiver</strong></a>.</label>
                                                    <div class="error-message" id="waiver_liabilityError"></div>
                                                </div>
                                            </div>
                                            <!-- Cancellation and Refund Policy -->
                                            <div class="col-md-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="waiver_cancel"
                                                        name="waiver_cancel" <?php echo (isset($_GET['waiver_cancel']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="waiver_cancel">The member agree to
                                                        the <a href="cancellation_and_refund_policy.php"
                                                            target="_blank"><strong>Cancellation and Refund
                                                                Policy</strong></a>.</label>
                                                    <div class="error-message" id="waiver_cancelError"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="d-grid mt-12 my-4 ">
                                            <button type="submit" class="btn btn-warning" name="register">Register</button>
                                        </div>
                                    </form>
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

        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/main.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const allQuestions = [
                    "What is your mother’s maiden name?",
                    "In what city were you born?",
                    "What is your father's middle name?",
                    "What was your childhood nickname?",
                    "What is your grandmother's first name?",
                    "What was the name of your first school?",
                    "What is the name of your first pet?",
                    "What was the make and model of your first car?",
                    "What was the name of your childhood best friend?",
                    "In what city did your parents meet?",
                    "What is your favorite book?",
                    "What was the first concert you attended?",
                    "What is your favorite movie?",
                    "What is your favorite food?",
                    "What is your favorite childhood TV show?"
                ];

                function updateQuestionOptions() {
                    const question1 = document.getElementById('security_question1').value;
                    const question2 = document.getElementById('security_question2').value;
                    const question3 = document.getElementById('security_question3').value;

                    // Get all selected questions
                    const selectedQuestions = [question1, question2, question3];

                    // Update options for question 2
                    updateQuestionDropdown('security_question2', selectedQuestions, question2);

                    // Update options for question 3
                    updateQuestionDropdown('security_question3', selectedQuestions, question3);
                }

                function updateQuestionDropdown(dropdownId, selectedQuestions, currentValue) {
                    const dropdown = document.getElementById(dropdownId);

                    // Clear the dropdown
                    dropdown.innerHTML = '<option value="">Select Security Question</option>';

                    // Add available options
                    allQuestions.forEach(question => {
                        // Only add if not selected in other dropdowns and not the current value of this dropdown
                        if (!selectedQuestions.includes(question) || question === currentValue) {
                            const option = document.createElement('option');
                            option.value = question;
                            option.textContent = question;
                            if (question === currentValue) {
                                option.selected = true;
                            }
                            dropdown.appendChild(option);
                        }
                    });
                }

                // Initialize the dropdowns on page load
                updateQuestionOptions();

                // Add event listeners to update options when a question is selected
                document.getElementById('security_question1').addEventListener('change', updateQuestionOptions);
                document.getElementById('security_question2').addEventListener('change', updateQuestionOptions);
                document.getElementById('security_question3').addEventListener('change', updateQuestionOptions);

                // If there are previously selected values from GET parameters, set them
                <?php if (isset($_GET['security_question2'])): ?>
                    document.getElementById('security_question2').value = "<?php echo htmlspecialchars($_GET['security_question2']); ?>";
                <?php endif; ?>
                <?php if (isset($_GET['security_question3'])): ?>
                    document.getElementById('security_question3').value = "<?php echo htmlspecialchars($_GET['security_question3']); ?>";
                <?php endif; ?>

                // Update the options after setting the values
                updateQuestionOptions();
            });
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
                const password = document.getElementById('password');
                const confirm_password = document.getElementById('confirm_password');
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
                    element: password,
                    errorId: 'passwordError',
                    message: 'Password is required'
                },
                {
                    element: confirm_password,
                    errorId: 'confirm_passwordError',
                    message: 'Please confirm your password'
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

                // Password Validation
                if (password.value) {
                    const passwordError = document.getElementById('passwordError');
                    if (password.value.length < 8) {
                        passwordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Password must be at least 8 characters long`;
                        password.classList.add('error');
                        isValid = false;
                    } else if (!/[A-Z]/.test(password.value)) {
                        passwordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Password must contain at least one uppercase letter`;
                        password.classList.add('error');
                        isValid = false;
                    } else if (!/[0-9]/.test(password.value)) {
                        passwordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Password must contain at least one numeric character`;
                        password.classList.add('error');
                        isValid = false;
                    } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password.value)) {
                        passwordError.innerHTML = `<i class="bi bi-exclamation-circle"></i> Password must contain at least one special character`;
                        password.classList.add('error');
                        isValid = false;
                    } else {
                        passwordError.innerHTML = '';
                        password.classList.remove('error');
                    }
                }

                // Password Match Validation
                if (password.value !== confirm_password.value) {
                    document.getElementById('confirm_passwordError').innerHTML = `<i class="bi bi-exclamation-circle"></i> Passwords do not match`;
                    confirm_password.classList.add('error');
                    isValid = false;
                }

                // Contact Phone Number Validation
                if (contact_number.value && !/^09\d{9}$/.test(contact_number.value)) {
                    document.getElementById('contact_numberError').innerHTML = `<i class="bi bi-exclamation-circle"></i> Phone Number must be 11 digits and start with "09"`;
                    contact_number.classList.add('error');
                    isValid = false;
                }

                // Security Questions Validation
                const securityFields = [
                    { element: document.getElementById('security_question1'), errorId: 'security_question1Error', message: 'Security Question 1 is required' },
                    { element: document.getElementById('security_answer1'), errorId: 'security_answer1Error', message: 'Answer for Security Question 1 is required' },
                    { element: document.getElementById('security_question2'), errorId: 'security_question2Error', message: 'Security Question 2 is required' },
                    { element: document.getElementById('security_answer2'), errorId: 'security_answer2Error', message: 'Answer for Security Question 2 is required' },
                    { element: document.getElementById('security_question3'), errorId: 'security_question3Error', message: 'Security Question 3 is required' },
                    { element: document.getElementById('security_answer3'), errorId: 'security_answer3Error', message: 'Answer for Security Question 3 is required' }
                ];

                securityFields.forEach(field => {
                    if (!field.element.value) {
                        document.getElementById(field.errorId).innerHTML = `<i class="bi bi-exclamation-circle"></i> ${field.message}`;
                        field.element.classList.add('error');
                        isValid = false;
                    } else {
                        document.getElementById(field.errorId).innerHTML = '';
                        field.element.classList.remove('error');
                    }
                });

                // Check for duplicate questions
                const question1 = document.getElementById('security_question1').value;
                const question2 = document.getElementById('security_question2').value;
                const question3 = document.getElementById('security_question3').value;

                if (question1 && question2 && question1 === question2) {
                    document.getElementById('security_question2Error').innerHTML = `<i class="bi bi-exclamation-circle"></i> You must select different security questions`;
                    document.getElementById('security_question2').classList.add('error');
                    isValid = false;
                }

                if (question1 && question3 && question1 === question3) {
                    document.getElementById('security_question3Error').innerHTML = `<i class="bi bi-exclamation-circle"></i> You must select different security questions`;
                    document.getElementById('security_question3').classList.add('error');
                    isValid = false;
                }

                if (question2 && question3 && question2 === question3) {
                    document.getElementById('security_question3Error').innerHTML = `<i class="bi bi-exclamation-circle"></i> You must select different security questions`;
                    document.getElementById('security_question3').classList.add('error');
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
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');

                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('bi-eye');
                });

                const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
                const confirmPasswordInput = document.getElementById('confirm_password');

                toggleConfirmPassword.addEventListener('click', function () {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);
                    this.classList.toggle('bi-eye');
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