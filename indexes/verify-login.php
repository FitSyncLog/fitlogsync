<?php
session_start();
include "db_con.php";

// Secure session settings
ini_set("session.cookie_httponly", 1);
ini_set("session.cookie_secure", 1);
ini_set("session.use_only_cookies", 1);

if (isset($_POST['login'])) {
    $otpCode = filter_input(INPUT_POST, 'otp_code', FILTER_SANITIZE_STRING);
    $user_id = $_SESSION['user_id'] ?? null;

    if (empty($otpCode) || empty($user_id)) {
        header("Location: ../login-verification.php?error=OTP Code is required");
        exit();
    }

    // Fetch OTP securely
    $sql = "SELECT otp_code, otp_code_expiration FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (hash_equals($row['otp_code'], $otpCode) && strtotime($row['otp_code_expiration']) > time()) {
            // OTP valid, regenerate session ID for security
            session_regenerate_id(true);

            // Fetch user details
            $user_sql = "SELECT * FROM users WHERE user_id = ?";
            $user_stmt = mysqli_prepare($conn, $user_sql);
            mysqli_stmt_bind_param($user_stmt, "i", $user_id);
            mysqli_stmt_execute($user_stmt);
            $user_result = mysqli_stmt_get_result($user_stmt);

            if ($row_data = mysqli_fetch_assoc($user_result)) {
                // Proceed with normal login
                $role_sql = "SELECT role_id FROM user_roles WHERE user_id = ?";
                $role_stmt = mysqli_prepare($conn, $role_sql);
                mysqli_stmt_bind_param($role_stmt, "i", $user_id);
                mysqli_stmt_execute($role_stmt);
                $role_result = mysqli_stmt_get_result($role_stmt);

                if (mysqli_num_rows($role_result) === 1) {
                    $role_row = mysqli_fetch_assoc($role_result);
                    $role_id = $role_row['role_id'];
                    $_SESSION['role_id'] = $role_id;

                    $data_sql = "SELECT * FROM users WHERE user_id = ?";
                    $data_stmt = mysqli_prepare($conn, $data_sql);
                    mysqli_stmt_bind_param($data_stmt, "i", $user_id);
                    mysqli_stmt_execute($data_stmt);
                    $data_result = mysqli_stmt_get_result($data_stmt);

                    $row_data = mysqli_fetch_assoc($data_result);

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $row_data['username'];
                    $_SESSION['firstname'] = $row_data['firstname'];
                    $_SESSION['middlename'] = $row_data['middlename'];
                    $_SESSION['lastname'] = $row_data['lastname'];
                    $_SESSION['date_of_birth'] = $row_data['date_of_birth'];
                    $_SESSION['gender'] = $row_data['gender'];
                    $_SESSION['phone_number'] = $row_data['phone_number'];
                    $_SESSION['email'] = $row_data['email'];
                    $_SESSION['address'] = $row_data['address'];
                    $_SESSION['enrolled_by'] = $row_data['enrolled_by'];
                    $_SESSION['status'] = $row_data['status'];
                    $_SESSION['profile_image'] = $row_data['profile_image'];
                    $_SESSION['account_number'] = $row_data['account_number'];
                    $_SESSION['login'] = true;

                    $status = $row_data['status'];



                    if ($status == "Banned") {
                        header("Location: ../login.php?accountBanned=This account was banned, please visit the front desk. Thank you");
                        exit;
                    } else if ($status == "Suspended") {
                        header("Location: ../login.php?accountBanned=This account was suspended, please visit the front desk. Thank you");
                        exit;
                    } else {
                        header("Location: ../dashboard.php");
                        exit;
                    }

                } else {
                    // Role not found
                    header("Location: ../login.php?UnexpectedError=Unexpected Error, please try again later&$user_data");
                    exit;
                }
            }
        } else {
            header("Location: ../login-verification.php?error=Invalid or expired OTP");
            exit();
        }
    } else {
        header("Location: ../login.php?UnexpectedError=Unexpected Error, please try again later");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>