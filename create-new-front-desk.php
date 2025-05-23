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
        <title>Register New Front Desk Account | FiT-LOGSYNC</title>
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
                            <h1 class="h3 mb-0 text-gray-800">Create New Front Desk Account</h1>
                            <a class="btn btn-secondary" href="manage-front-desk.php"><i class="bi bi-arrow-return-left"></i>
                                Back</a>
                        </div>


                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="card-body p-4">
                                    <h3 class="mb-4 text-center"><strong>Front Desk Registration Form</strong></h3>

                                    <form id="registrationForm" action="indexes/register-new-front-desk.php" method="POST">
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
                // Password toggle functionality
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');
                const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
                const confirmPasswordInput = document.getElementById('confirm_password');

                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('bi-eye');
                });

                toggleConfirmPassword.addEventListener('click', function () {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);
                    this.classList.toggle('bi-eye');
                });

                // Form validation
                document.getElementById('registrationForm').addEventListener('submit', function (event) {
                    let isValid = true;

                    // Clear previous error messages
                    const errorMessages = document.querySelectorAll('.error-message');
                    errorMessages.forEach(el => el.textContent = '');

                    const errorFields = document.querySelectorAll('.error');
                    errorFields.forEach(el => el.classList.remove('error'));

                    // Validation functions
                    function validateRequired(field, errorId, message) {
                        if (!field.value.trim()) {
                            document.getElementById(errorId).textContent = message;
                            field.classList.add('error');
                            return false;
                        }
                        return true;
                    }

                    function validatePattern(field, errorId, pattern, message) {
                        if (field.value.trim() && !pattern.test(field.value.trim())) {
                            document.getElementById(errorId).textContent = message;
                            field.classList.add('error');
                            return false;
                        }
                        return true;
                    }

                    function validateMatch(field1, field2, errorId, message) {
                        if (field1.value !== field2.value) {
                            document.getElementById(errorId).textContent = message;
                            field2.classList.add('error');
                            return false;
                        }
                        return true;
                    }

                    function validateDate(field, errorId, message) {
                        const selectedDate = new Date(field.value);
                        const currentDate = new Date();

                        if (selectedDate >= currentDate) {
                            document.getElementById(errorId).textContent = message;
                            field.classList.add('error');
                            return false;
                        }
                        return true;
                    }

                    // Personal Information Validation
                    isValid = validateRequired(document.getElementById('username'), 'usernameError', 'Username is required') && isValid;
                    isValid = validateRequired(document.getElementById('lastname'), 'lastnameError', 'Last name is required') && isValid;
                    isValid = validateRequired(document.getElementById('firstname'), 'firstnameError', 'First name is required') && isValid;
                    isValid = validateRequired(document.getElementById('dateofbirth'), 'dateofbirthError', 'Date of birth is required') && isValid;
                    isValid = validateDate(document.getElementById('dateofbirth'), 'dateofbirthError', 'Date of birth must be in the past') && isValid;
                    isValid = validateRequired(document.getElementById('gender'), 'genderError', 'Gender is required') && isValid;
                    isValid = validateRequired(document.getElementById('address'), 'addressError', 'Address is required') && isValid;

                    // Phone number validation (must start with 09 and be 11 digits)
                    isValid = validatePattern(
                        document.getElementById('phonenumber'),
                        'phonenumberError',
                        /^09\d{9}$/,
                        'Phone number must be 11 digits starting with 09'
                    ) && isValid;

                    // Email validation
                    isValid = validatePattern(
                        document.getElementById('email'),
                        'emailError',
                        /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                        'Please enter a valid email address'
                    ) && isValid;

                    // Password validation
                    const password = document.getElementById('password').value;
                    if (!password) {
                        document.getElementById('passwordError').textContent = 'Password is required';
                        document.getElementById('password').classList.add('error');
                        isValid = false;
                    } else {
                        const passwordErrors = [];
                        if (password.length < 8) passwordErrors.push('at least 8 characters');
                        if (!/[A-Z]/.test(password)) passwordErrors.push('one uppercase letter');
                        if (!/[0-9]/.test(password)) passwordErrors.push('one number');
                        if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) passwordErrors.push('one special character');

                        if (passwordErrors.length > 0) {
                            document.getElementById('passwordError').textContent =
                                'Password must contain: ' + passwordErrors.join(', ');
                            document.getElementById('password').classList.add('error');
                            isValid = false;
                        }
                    }

                    // Confirm password validation
                    isValid = validateMatch(
                        document.getElementById('password'),
                        document.getElementById('confirm_password'),
                        'confirm_passwordError',
                        'Passwords do not match'
                    ) && isValid;

                    // Emergency Contact Validation
                    isValid = validateRequired(document.getElementById('contact_person'), 'contact_personError', 'Contact person is required') && isValid;

                    isValid = validatePattern(
                        document.getElementById('contact_number'),
                        'contact_numberError',
                        /^09\d{9}$/,
                        'Contact number must be 11 digits starting with 09'
                    ) && isValid;

                    isValid = validateRequired(document.getElementById('relationship'), 'relationshipError', 'Relationship is required') && isValid;

                    // Security Questions Validation
                    isValid = validateRequired(document.getElementById('security_question1'), 'security_question1Error', 'Security question 1 is required') && isValid;
                    isValid = validateRequired(document.getElementById('security_answer1'), 'security_answer1Error', 'Answer for question 1 is required') && isValid;

                    isValid = validateRequired(document.getElementById('security_question2'), 'security_question2Error', 'Security question 2 is required') && isValid;
                    isValid = validateRequired(document.getElementById('security_answer2'), 'security_answer2Error', 'Answer for question 2 is required') && isValid;

                    isValid = validateRequired(document.getElementById('security_question3'), 'security_question3Error', 'Security question 3 is required') && isValid;
                    isValid = validateRequired(document.getElementById('security_answer3'), 'security_answer3Error', 'Answer for question 3 is required') && isValid;

                    // Check if all security questions are unique
                    const question1 = document.getElementById('security_question1').value;
                    const question2 = document.getElementById('security_question2').value;
                    const question3 = document.getElementById('security_question3').value;

                    if (question1 && question2 && question3) {
                        if (question1 === question2 || question1 === question3 || question2 === question3) {
                            document.getElementById('security_question2Error').textContent = 'All security questions must be unique';
                            document.getElementById('security_question3Error').textContent = 'All security questions must be unique';
                            isValid = false;
                        }
                    }

                    if (!isValid) {
                        event.preventDefault();

                        // Scroll to the first error
                        const firstError = document.querySelector('.error');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }

                        // Show a SweetAlert with the validation errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Form Validation Failed',
                            text: 'Please correct the highlighted fields before submitting.',
                            confirmButtonColor: '#ffc107'
                        });
                    }
                });

                // Security questions dynamic population
                function updateQuestionOptions() {
                    const allQuestions = [
                        "What is your mother's maiden name?",
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

                    const question1 = document.getElementById('security_question1').value;
                    const question2 = document.getElementById('security_question2');
                    const question3 = document.getElementById('security_question3');

                    // Update question 2 options
                    updateSelectOptions(question2, allQuestions, [question1, question3.value]);

                    // Update question 3 options
                    updateSelectOptions(question3, allQuestions, [question1, question2.value]);
                }

                function updateSelectOptions(selectElement, allQuestions, excludedQuestions) {
                    const currentValue = selectElement.value;
                    selectElement.innerHTML = '<option value="">Select Security Question</option>';

                    allQuestions.forEach(question => {
                        if (!excludedQuestions.includes(question)) {
                            const option = document.createElement('option');
                            option.value = question;
                            option.textContent = question;
                            selectElement.appendChild(option);
                        }
                    });

                    // Restore the current value if it's still valid
                    if (currentValue && !excludedQuestions.includes(currentValue)) {
                        selectElement.value = currentValue;
                    }
                }

                // Initialize the security questions
                updateQuestionOptions();

                // Add event listeners for security question changes
                document.getElementById('security_question1').addEventListener('change', updateQuestionOptions);
                document.getElementById('security_question2').addEventListener('change', updateQuestionOptions);
                document.getElementById('security_question3').addEventListener('change', updateQuestionOptions);
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