<?php
session_start();

include "db_con.php";

if (isset($_POST['login'])) {
    $otpCode = $_POST['otp_code'];
    $user_id = $_SESSION['user_id'];

    if (empty($otpCode)) {
        header("Location: ../login-verification.php?error=OTP Code is required");
        exit();
    }

    // Fetch the OTP and its expiration time from the database
    $sql = "SELECT otp_code, otp_code_expiration FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Check if OTP matches and is not expired
        if ($row['otp_code'] == $otpCode && strtotime($row['otp_code_expiration']) > time()) {
            // OTP is valid, proceed with login
            $role_sql = "SELECT role FROM user_roles WHERE user_id = ?";
            $role_stmt = mysqli_prepare($conn, $role_sql);
            mysqli_stmt_bind_param($role_stmt, "i", $user_id);
            mysqli_stmt_execute($role_stmt);
            $role_result = mysqli_stmt_get_result($role_stmt);

            if (mysqli_num_rows($role_result) === 1) {
                $role_row = mysqli_fetch_assoc($role_result);
                $role = $role_row['role'];
                $_SESSION['role'] = $role;

                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $row['username'];
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['middlename'] = $row['middlename'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['date_of_birth'] = $row['date_of_birth'];
                $_SESSION['gender'] = $row['gender'];
                $_SESSION['phone_number'] = $row['phone_number'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['address'] = $row['address'];
                $_SESSION['enrolled_by'] = $row['enrolled_by'];
                $_SESSION['status'] = $row['status'];
                $_SESSION['profile_image'] = $row['profile_image'];
                $_SESSION['account_number'] = $row['account_number'];

                $status = $row['status'];

                // Redirect based on role
                switch ($role) {
                    case 'Member':
                        header("Location: ../Member/dashboard.php");
                        exit;
                    case 'Instructor':
                        header("Location: ../Instructor/dashboard.php");
                        exit;
                    case 'Front Desk':
                        header("Location: ../Front-Desk/dashboard.php");
                        exit;
                    case 'Admin':
                        header("Location: ../Admin/dashboard.php");
                        exit;
                    case 'Super Admin':
                        header("Location: ../Super-Admin/dashboard.php");
                        exit;
                    default:
                        // Handle unexpected roles
                        header("Location: ../login.php?UnexpectedRole=Role not recognized");
                        exit;
                }
            } else {
                // Role not found
                header("Location: ../login.php?UnexpectedError=Unexpected Error, please try again later");
                exit;
            }
        } else {
            // Invalid or expired OTP
            header("Location: ../login-verification.php?error=Invalid or expired OTP");
            exit;
        }
    } else {
        // User not found
        header("Location: ../login.php?UnexpectedError=Unexpected Error, please try again later");
        exit;
    }
} else {
    // Redirect if OTP form is not submitted
    header("Location: ../login.php");
    exit();
}
?>