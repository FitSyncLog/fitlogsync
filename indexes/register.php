<?php
session_start();
require "db_con.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    if (isset($_POST['register'])) {
        function validate($data)
        {
            $data = trim($data ?? ''); // Use null coalescing operator to avoid null values
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        date_default_timezone_set('Asia/Manila');

        // Validate and sanitize inputs
        $lastnameNotProper = validate($_POST['lastname'] ?? '');
        $firstnameNotProper = validate($_POST['firstname'] ?? '');
        $middlenameNotProper = validate($_POST['middlename'] ?? '');

        $username = validate($_POST['username'] ?? '');
        $lastname = mb_convert_case($lastnameNotProper, MB_CASE_TITLE, "UTF-8");
        $firstname = mb_convert_case($firstnameNotProper, MB_CASE_TITLE, "UTF-8");
        $middlename = mb_convert_case($middlenameNotProper, MB_CASE_TITLE, "UTF-8");
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
        $waiver_rules = validate($_POST['waiver_rules'] ?? '');
        $waiver_liability = validate($_POST['waiver_liability'] ?? '');
        $waiver_cancel = validate($_POST['waiver_cancel'] ?? '');

        // Generate verification code and expiration
        $verification_code = "123456";
        $datetime = new DateTime('now');
        $datetime->modify('+10 minutes');
        $v_code_expiration = $datetime->format('Y-m-d H:i:s');

        // Set registration date and status
        $registration_date = date('Y-m-d');
        $status = "Not Verified";

        // Validate required fields
        if (empty($username) || empty($lastname) || empty($firstname) || empty($dateofbirth) || empty($gender) || empty($address) || empty($phonenumber) || empty($email) || empty($password) || empty($confirm_password)) {
            $error_message = "All fields are required except middle name.";
            header("Location: ../register.php?error=" . urlencode($error_message));
            exit();
        }

        // Validate password match
        if ($password !== $confirm_password) {
            $error_message = "Passwords do not match.";
            header("Location: ../register.php?error=" . urlencode($error_message));
            exit();
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the `users` table
        $sql_new_user = "INSERT INTO users (username, firstname, middlename, lastname, date_of_birth, password, gender, phone_number, email, address, verification_code, v_code_expiration, status, registration_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_new_user_query = mysqli_prepare($conn, $sql_new_user);

        if ($stmt_new_user_query === false) {
            $error_message = "Failed to prepare the SQL statement.";
            header("Location: ../register.php?error=" . urlencode($error_message));
            exit();
        }

        mysqli_stmt_bind_param($stmt_new_user_query, "ssssssssssssss", $username, $firstname, $middlename, $lastname, $dateofbirth, $hashed_password, $gender, $phonenumber, $email, $address, $verification_code, $v_code_expiration, $status, $registration_date);
        $result_new_user_query = mysqli_stmt_execute($stmt_new_user_query);

        if ($result_new_user_query) {
            // Retrieve the `user_id` of the newly inserted user
            $user_id = mysqli_insert_id($conn);

            // Insert medical background and PAR-Q data into the `medical_backgrounds` table
            $sql_medical_background = "INSERT INTO medical_backgrounds (user_id, medical_conditions, current_medications, previous_injuries, par_q_1, par_q_2, par_q_3, par_q_4, par_q_5, par_q_6, par_q_7, par_q_8, par_q_9, par_q_10)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_medical_background = mysqli_prepare($conn, $sql_medical_background);

            if ($stmt_medical_background === false) {
                $error_message = "Failed to prepare the SQL statement for medical background.";
                header("Location: ../register.php?error=" . urlencode($error_message));
                exit();
            }

            mysqli_stmt_bind_param($stmt_medical_background, "isssssssssssss", $user_id, $medical_conditions, $current_medications, $previous_injuries, $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10);
            $result_medical_background = mysqli_stmt_execute($stmt_medical_background);

            if (!$result_medical_background) {
                $error_message = "Failed to insert medical background data: " . mysqli_error($conn);
                header("Location: ../register.php?error=" . urlencode($error_message));
                exit();
            }

            // Insert waiver/agreement data into the `waiver_agreements` table
            $sql_waiver_agreements = "INSERT INTO waiver_agreements (user_id, rules_and_policy, liability_waiver, cancellation_and_refund_policy)
                    VALUES (?, ?, ?, ?)";
            $stmt_waiver_agreements = mysqli_prepare($conn, $sql_waiver_agreements);

            if ($stmt_waiver_agreements === false) {
                $error_message = "Failed to prepare the SQL statement for waiver agreements.";
                header("Location: ../register.php?error=" . urlencode($error_message));
                exit();
            }

            mysqli_stmt_bind_param($stmt_waiver_agreements, "isss", $user_id, $waiver_rules, $waiver_liability, $waiver_cancel);
            $result_waiver_agreements = mysqli_stmt_execute($stmt_waiver_agreements);

            if (!$result_waiver_agreements) {
                $error_message = "Failed to insert waiver agreements data: " . mysqli_error($conn);
                header("Location: ../register.php?error=" . urlencode($error_message));
                exit();
            }

            // Registration successful
            header("Location: ../login.php?registrationSuccess=Registration successful!");
            exit();
        } else {
            // Database error
            $error_message = "Database error: " . mysqli_error($conn);
            header("Location: ../register.php?registrationfailed=" . urlencode($error_message));
            exit();
        }

        mysqli_stmt_close($stmt_new_user_query);
        mysqli_close($conn);
    } else {
        // Submit button not detected
        header("Location: ../register.php?register=Submit button not detected.");
        exit();
    }
} else {
    // Form not submitted via POST
    header("Location: ../register.php?register=Please fill out the form below.");
    exit();
}
?>