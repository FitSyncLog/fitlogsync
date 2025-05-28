<?php
session_start();
require "../indexes/db_con.php";
require "../phpqrcode/qrlib.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

function validate($data)
{
    $data = trim($data ?? '');
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isValidPhoneNumber($phone)
{
    return preg_match('/^09\d{9}$/', $phone);
}

function generateAccountNumber($conn)
{
    // Get the current year and month
    $year = date('Y');
    $month = date('m');

    // Format the month to be 4 digits (e.g., 0001 for January)
    $monthFormatted = str_pad($month, 4, '0', STR_PAD_LEFT);

    // Generate a random 4-digit number
    $random = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

    // Get the last increment number for the current month
    $sql = "SELECT MAX(SUBSTRING(account_number, 13, 4)) AS last_increment
            FROM users
            WHERE SUBSTRING(account_number, 1, 4) = ?
            AND SUBSTRING(account_number, 5, 4) = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $year, $monthFormatted);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    $lastIncrement = $row['last_increment'] ?? 0;
    $increment = str_pad($lastIncrement + 1, 4, '0', STR_PAD_LEFT);

    // Construct the account number
    $accountNumber = $year . $monthFormatted . $random . $increment;

    return $accountNumber;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Validate and sanitize inputs
    $username = validate($_POST['username'] ?? '');
    $lastname = mb_convert_case(validate($_POST['lastname'] ?? ''), MB_CASE_TITLE, "UTF-8");
    $firstname = mb_convert_case(validate($_POST['firstname'] ?? ''), MB_CASE_TITLE, "UTF-8");
    $middlename = mb_convert_case(validate($_POST['middlename'] ?? ''), MB_CASE_TITLE, "UTF-8");
    $dateofbirth = validate($_POST['dateofbirth'] ?? '');
    $gender = validate($_POST['gender'] ?? '');
    $address = validate($_POST['address'] ?? '');
    $phonenumber = validate($_POST['phonenumber'] ?? '');
    $email = validate($_POST['email'] ?? '');
    $password = validate($_POST['password'] ?? '');
    $confirm_password = validate($_POST['confirm_password'] ?? '');
    $medical_conditions = validate($_POST['medical_conditions'] ?? '');
    $current_medications = validate($_POST['current_medications'] ?? '');
    $previous_injuries = validate($_POST['previous_injuries'] ?? '');
    $q1 = validate($_POST['q1'] ?? '');
    $q2 = validate($_POST['q2'] ?? '');
    $q3 = validate($_POST['q3'] ?? '');
    $q4 = validate($_POST['q4'] ?? '');
    $q5 = validate($_POST['q5'] ?? '');
    $q6 = validate($_POST['q6'] ?? '');
    $q7 = validate($_POST['q7'] ?? '');
    $q8 = validate($_POST['q8'] ?? '');
    $q9 = validate($_POST['q9'] ?? '');
    $q10 = validate($_POST['q10'] ?? '');
    $security_question1 = validate($_POST['security_question1'] ?? '');
    $security_answer1 = validate($_POST['security_answer1'] ?? '');
    $security_question2 = validate($_POST['security_question2'] ?? '');
    $security_answer2 = validate($_POST['security_answer2'] ?? '');
    $security_question3 = validate($_POST['security_question3'] ?? '');
    $security_answer3 = validate($_POST['security_answer3'] ?? '');
    $waiver_rules = isset($_POST['waiver_rules']);
    $waiver_liability = isset($_POST['waiver_liability']);
    $waiver_cancel = isset($_POST['waiver_cancel']);
    $contact_person = mb_convert_case(validate($_POST['contact_person'] ?? ''), MB_CASE_TITLE, "UTF-8");
    $contact_number = validate($_POST['contact_number'] ?? '');
    $relationship = validate($_POST['relationship'] ?? '');
    $enrolled_by = $_SESSION['firstname'] . ' ' . $_SESSION['middlename'] . ' ' . $_SESSION['lastname'];

    $user_data = 'username=' . $username .
        '&lastname=' . $lastname .
        '&firstname=' . $firstname .
        '&middlename=' . $middlename .
        '&dateofbirth=' . $dateofbirth .
        '&gender=' . $gender .
        '&address=' . $address .
        '&phonenumber=' . $phonenumber .
        '&email=' . $email .
        '&password=' . $password .
        '&confirm_password=' . $confirm_password .
        '&medical_conditions=' . $medical_conditions .
        '&current_medications=' . $current_medications .
        '&previous_injuries=' . $previous_injuries .
        '&q1=' . $q1 .
        '&q2=' . $q2 .
        '&q3=' . $q3 .
        '&q4=' . $q4 .
        '&q5=' . $q5 .
        '&q6=' . $q6 .
        '&q7=' . $q7 .
        '&q8=' . $q8 .
        '&q9=' . $q9 .
        '&q10=' . $q10 .
        '&security_question1=' . $security_question1 .
        '&security_answer1=' . $security_answer1 .
        '&security_question2=' . $security_question2 .
        '&security_answer2=' . $security_answer2 .
        '&security_question3=' . $security_question3 .
        '&security_answer3=' . $security_answer3 .
        '&waiver_rules=' . $waiver_rules .
        '&waiver_liability=' . $waiver_liability .
        '&waiver_cancel=' . $waiver_cancel .
        '&contact_person=' . $contact_person .
        '&contact_number=' . $contact_number .
        '&relationship=' . $relationship;

    // Validate required fields
    if (empty($username) || empty($lastname) || empty($firstname) || empty($dateofbirth) || empty($gender) || empty($address) || empty($phonenumber) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required except middle name.";
        header("Location: ../create-new-member.php?Failed=" . urlencode($error_message));
        exit();
    }

    // Validate email and phone number
    if (!isValidEmail($email)) {
        $error_message = "Invalid email address.";
        header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
        exit();
    }

    if (!isValidPhoneNumber($phonenumber)) {
        $error_message = "Phone number must be 11 digits and start with '09'.";
        header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
        exit();
    }

    // Validate password match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
        header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification code and expiration
    $verification_code = "123456";
    $datetime = new DateTime('now');
    $datetime->modify('+10 minutes');
    $v_code_expiration = $datetime->format('Y-m-d H:i:s');

    // Set registration date and status
    $registration_date = date('Y-m-d');
    $status = "Active";

    // Check if username already exists
    $sql_check_username = "SELECT * FROM users WHERE username=?";
    $stmt_check_username = mysqli_prepare($conn, $sql_check_username);
    mysqli_stmt_bind_param($stmt_check_username, "s", $username);
    mysqli_stmt_execute($stmt_check_username);
    $result_check_username = mysqli_stmt_get_result($stmt_check_username);

    if (mysqli_num_rows($result_check_username) > 0) {
        header("Location: ../create-new-member.php?username_already_exist=Username already exists&" . $user_data);
        exit();
    }

    // Check if email already exists
    $sql_check_email = "SELECT * FROM users WHERE email=?";
    $stmt_check_email = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt_check_email, "s", $email);
    mysqli_stmt_execute($stmt_check_email);
    $result_check_email = mysqli_stmt_get_result($stmt_check_email);

    if (mysqli_num_rows($result_check_email) > 0) {
        header("Location: ../create-new-member.php?email_already_taken=Email is already taken.&" . $user_data);
        exit();
    }

    // Generate the account number
    do {
        $accountNumber = generateAccountNumber($conn);
        $sql_check_account = "SELECT * FROM users WHERE account_number = ?";
        $stmt_check_account = mysqli_prepare($conn, $sql_check_account);
        mysqli_stmt_bind_param($stmt_check_account, "s", $accountNumber);
        mysqli_stmt_execute($stmt_check_account);
        $result_check_account = mysqli_stmt_get_result($stmt_check_account);
    } while (mysqli_num_rows($result_check_account) > 0);

    // Insert data into the users table
    $sql_new_user = "INSERT INTO users (username, firstname, middlename, lastname, date_of_birth, password, gender, phone_number, email, address, enrolled_by, status, registration_date, account_number)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_new_user_query = mysqli_prepare($conn, $sql_new_user);

    if ($stmt_new_user_query === false) {
        $error_message = "Failed to prepare the SQL statement.";
        header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
        exit();
    }

    mysqli_stmt_bind_param($stmt_new_user_query, "ssssssssssssss", $username, $firstname, $middlename, $lastname, $dateofbirth, $hashed_password, $gender, $phonenumber, $email, $address, $enrolled_by, $status, $registration_date, $accountNumber);
    $result_new_user_query = mysqli_stmt_execute($stmt_new_user_query);

    if ($result_new_user_query) {
        $user_id = mysqli_insert_id($conn);

        // Insert medical background and PAR-Q data into the medical_backgrounds table
        $sql_medical_background = "INSERT INTO medical_backgrounds (user_id, medical_conditions, current_medications, previous_injuries, par_q_1, par_q_2, par_q_3, par_q_4, par_q_5, par_q_6, par_q_7, par_q_8, par_q_9, par_q_10)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_medical_background = mysqli_prepare($conn, $sql_medical_background);

        if ($stmt_medical_background === false) {
            $error_message = "Failed to prepare the SQL statement for medical background.";
            header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        mysqli_stmt_bind_param($stmt_medical_background, "isssssssssssss", $user_id, $medical_conditions, $current_medications, $previous_injuries, $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10);
        $result_medical_background = mysqli_stmt_execute($stmt_medical_background);

        if (!$result_medical_background) {
            $error_message = "Failed to insert medical background data.";
            header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        // Insert waiver/agreement data into the waiver_agreements table
        $sql_waiver_agreements = "INSERT INTO waivers (user_id, rules_and_policy, liability_waiver, cancellation_and_refund_policy)
                VALUES (?, ?, ?, ?)";
        $stmt_waiver_agreements = mysqli_prepare($conn, $sql_waiver_agreements);

        if ($stmt_waiver_agreements === false) {
            $error_message = "Failed to prepare the SQL statement for waiver agreements.";
            header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        mysqli_stmt_bind_param($stmt_waiver_agreements, "isss", $user_id, $waiver_rules, $waiver_liability, $waiver_cancel);
        $result_waiver_agreements = mysqli_stmt_execute($stmt_waiver_agreements);

        if (!$result_waiver_agreements) {
            $error_message = "Failed to insert waiver agreements data.";
            header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        // Insert emergency contact data into the emergency_contacts table
        $sql_emergency_contact = "INSERT INTO emergency_contacts (user_id, contact_person, contact_number, relationship)
                VALUES (?, ?, ?, ?)";
        $stmt_emergency_contact = mysqli_prepare($conn, $sql_emergency_contact);

        if ($stmt_emergency_contact === false) {
            $error_message = "Failed to prepare the SQL statement for emergency contact.";
            header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        mysqli_stmt_bind_param($stmt_emergency_contact, "isss", $user_id, $contact_person, $contact_number, $relationship);
        $result_emergency_contact = mysqli_stmt_execute($stmt_emergency_contact);

        if (!$result_emergency_contact) {
            $error_message = "Failed to insert emergency contact data.";
            header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        // Insert security questions data into the security_questions table
        $sql_security_questions = "INSERT INTO security_questions (user_id, sq1, sq1_res, sq2, sq2_res, sq3, sq3_res)
VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_security_questions = mysqli_prepare($conn, query: $sql_security_questions);

        if ($stmt_security_questions === false) {
            $error_message = "Failed to prepare the SQL statement for security questions.";
            header("Location: ../register.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        mysqli_stmt_bind_param($stmt_security_questions, "issssss", $user_id, $security_question1, $security_answer1, $security_question2, $security_answer2, $security_question3, $security_answer3);
        $result_security_questions = mysqli_stmt_execute($stmt_security_questions);

        if (!$result_security_questions) {
            $error_message = "Failed to insert security questions data.";
            header("Location: ../register.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        // Insert user role
        $sql_user_role = "INSERT INTO user_roles (user_id, role_id) VALUES (?, 5)";
        $stmt_user_role = mysqli_prepare($conn, $sql_user_role);

        if ($stmt_user_role === false) {
            $error_message = "Failed to prepare the SQL statement for user role.";
            header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        mysqli_stmt_bind_param($stmt_user_role, "i", $user_id);
        $result_user_role = mysqli_stmt_execute($stmt_user_role);

        if (!$result_user_role) {
            $error_message = "Failed to insert user role data.";
            header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
            exit();
        }

        $qrCodePath = "../assets/qr_codes/{$accountNumber}.png";
        $moduleSize = 50;
        QRcode::png($accountNumber, $qrCodePath, 'L', $moduleSize, 2);

        // Generate the back card with QR code
        $backgroundPath = '../assets/qr_codes/template.png';
        $outputPath = "../assets/access-card-back/{$accountNumber}-qr.png";

        // Create directory if it doesn't exist
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        // Load background image
        $background = imagecreatefrompng($backgroundPath);
        if (!$background) {
            error_log("Failed to load back card background image.");
        } else {
            // Load QR code image
            $qrCode = imagecreatefrompng($qrCodePath);
            if (!$qrCode) {
                error_log("Failed to load QR code image.");
            } else {
                // Get dimensions
                $bgWidth = imagesx($background);
                $bgHeight = imagesy($background);
                $qrWidth = imagesx($qrCode);
                $qrHeight = imagesy($qrCode);

                // Calculate position to center QR code
                $x = (int) (($bgWidth - $qrWidth) / 2);
                $y = (int) (($bgHeight - $qrHeight) / 2);

                // Merge QR code onto background
                imagecopy($background, $qrCode, $x, $y, 0, 0, $qrWidth, $qrHeight);

                // Save result
                imagepng($background, $outputPath);

                // Free memory
                imagedestroy($qrCode);
            }
            imagedestroy($background);
        }

        // Generate the access card
        $full_name = $firstname . " " . $lastname;
        $member_since = date('m/y', strtotime($registration_date));

        // Paths
        $templatePath = '../assets/access-card/template.png';
        $font = '../assets/fonts/arial.ttf'; // Ensure this font file exists
        $namefont = '../assets/fonts/GOTHIC.TTF'; // Ensure this font file exists
        $numberfont = '../assets/fonts/Kredit Front.otf'; // Ensure this font file exists

        // Check if template and font exist
        if (!file_exists($templatePath)) {
            die("Background template not found at $templatePath");
        }
        if (!file_exists($font)) {
            die("Font file not found at $font");
        }

        // Load background image
        $image = imagecreatefrompng($templatePath);
        if (!$image) {
            die("Failed to load template image.");
        }

        // Set text color (white)
        $textColor = imagecolorallocate($image, 255, 255, 255); // white
        $yellowtextColor = imagecolorallocate($image, 255, 202, 43); // white

        $formattedAccountNumber = implode(' ', str_split($accountNumber, 4));

        // Write text on image
        imagettftext($image, 160, 0, 210, 1450, $textColor, $numberfont, $formattedAccountNumber);
        imagettftext($image, 80, 0, 545, 1640, $yellowtextColor, $font, $member_since);
        imagettftext($image, 80, 0, 210, 1850, $textColor, $namefont, $full_name);

        // Save the new image
        $account_numberClean = str_replace(' ', '', $accountNumber);
        $outputPath = "../assets/access-card/{$account_numberClean}-accesscard.jpg";

        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }
        imagejpeg($image, $outputPath, 90);
        imagedestroy($image);

        // Send welcome email with e-access card
        require_once "send-welcome-email.php";
        $fullName = $firstname . ' ' . ($middlename ? $middlename . ' ' : '') . $lastname;
        $emailSent = sendWelcomeEmail($email, $fullName, $accountNumber);

        if (!$emailSent) {
            // Log the error but continue with registration
            error_log("Failed to send welcome email to: " . $email);
            header("Location: ../manage-members.php?Success=New member registered successfully! (Note: Welcome email could not be sent)");
        } else {
            header("Location: ../manage-members.php?Success=New member registered successfully!");
        }
        exit();
    } else {
        $error_message = "Failed to insert user data.";
        header("Location: ../create-new-member.php?Failed=" . urlencode($error_message) . "&" . $user_data);
        exit();
    }

    mysqli_stmt_close($stmt_new_user_query);
    mysqli_close($conn);
} else {
    header("Location: ../login.php?register=Please fill out the form below.");
    exit();
}
?>
