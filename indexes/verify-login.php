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
                // Store user details in session
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

                // Fetch user role
                $role_sql = "SELECT role FROM user_roles WHERE user_id = ?";
                $role_stmt = mysqli_prepare($conn, $role_sql);
                mysqli_stmt_bind_param($role_stmt, "i", $user_id);
                mysqli_stmt_execute($role_stmt);
                $role_result = mysqli_stmt_get_result($role_stmt);

                if ($role_row = mysqli_fetch_assoc($role_result)) {
                    $_SESSION['role'] = $role_row['role'];

                    // Redirect based on role
                    $redirect_map = [
                        "Member" => "../Member/dashboard.php",
                        "Instructor" => "../Instructor/dashboard.php",
                        "Front Desk" => "../Front-Desk/dashboard.php",
                        "Admin" => "../Admin/dashboard.php",
                        "Super Admin" => "../Super-Admin/dashboard.php"
                    ];

                    header("Location: " . ($redirect_map[$_SESSION['role']] ?? "../login.php?UnexpectedRole=Role not recognized"));
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
