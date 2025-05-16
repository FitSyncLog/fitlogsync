<?php
session_start();

include "db_con.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($email, $otpCode)
{
    require("PHPMailer/PHPMailer.php");
    require("PHPMailer/SMTP.php");
    require("PHPMailer/Exception.php");

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fitlogsync.official@gmail.com';
        $mail->Password = 'tjen yako tlcc knwi';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('fitlogsync.official@gmail.com', 'FiT-LOGSYNC');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Enable Two Factor Authentication | FiT-LOGSYNC';

        // Publicly Hosted Image URLs (Replace with actual URLs)
        $headerImageURL = "https://rmmccomsoc.org/fitlogsync-email-header.png";
        $googlePlayImageURL = "https://rmmccomsoc.org/google-play-email.png";

        $mail->Body = "
        <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background-color: #fff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                text-align: center;
                overflow: hidden; 
            }
            h1, h3, h5, p {
                color: #000000;
                margin: 5px 0;
            }
            p {
                color: #3d3d3d;
                margin: 5px 0;
            }
            .header-img {
                width: 100%;
                display: block;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }
            .otp-code {
                color: #F6C23E;
                font-size: 4em;
                font-weight: bold;
                text-shadow: 3px 3px 8px rgba(128, 128, 128, 0.2);
                letter-spacing: 10px;
                display: inline-block;
                margin: 5px 0;
            }
            .play-button {
                display: inline-block;
                margin-top: 10px;
            }
            .play-button img {
                width: 150px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <img src='{$headerImageURL}' alt='FitLogSync Header' class='header-img'>
            <h3>Hello!</h3>
            <p>Your Fit-LOGSYNC Login OTP Code is:</p>
            <h1 class='otp-code'>{$otpCode}</h1>
            <h5>Valid for 15 mins. NEVER share this code with others.</h5>
            <hr>
            <p>Come Shop With Us</p>
            <a href='https://play.google.com/store' class='play-button'>
                <img src='{$googlePlayImageURL}' alt='Get it on Google Play'>
            </a>
        </div>
    </body>
</html>
";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['login'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    $user_data = 'email=' . $email;

    // Select account based on email
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $row['password'])) {
            $user_id = $row['user_id'];

            // Check if 2FA is enabled
            if ($row['two_factor_authentication'] == 1) {
                // Generate OTP
                $otpCode = rand(100000, 999999);
                date_default_timezone_set('Asia/Manila');

                $otpExpiration = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                // Update user record with OTP and expiration
                $updateSql = "UPDATE users SET otp_code = ?, otp_code_expiration = ? WHERE user_id = ?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "ssi", $otpCode, $otpExpiration, $user_id);
                mysqli_stmt_execute($updateStmt);

                // Send OTP via email
                if (sendMail($email, $otpCode)) {
                    // Redirect to OTP verification page
                    $_SESSION['user_id'] = $user_id;
                    header("Location: ../login-verification.php");
                    exit();
                } else {
                    header("Location: ../login.php?UnexpectedError=Failed to send OTP. Please try again.&$user_data");
                    exit();
                }
            } else {
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
            // Incorrect password
            header("Location: ../login.php?incorrectPassword=Incorrect Username or Password&$user_data");
            exit;
        }
    } else {
        // Email not found
        header("Location: ../login.php?EmailisnotRegistered=Email Address is not found.&$user_data");
        exit;
    }
} else {
    // Redirect if login form is not submitted
    header("Location: ../login.php");
    exit();
}
?>