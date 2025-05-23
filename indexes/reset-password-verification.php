<?php
include 'db_con.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($email, $otpCode, $name)
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
        $mail->Subject = "{$otpCode} is your password reset code | FiT-LOGSYNC";

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
                    <h3>One more step to change your password!</h3>
                    <p>Hi {$name},</p>
                    <p>We received a request to change your password. To proceed, please enter the OTP code below:</p>
                    <h1 class='otp-code'>{$otpCode}</h1>
                    <h5>Valid for 15 mins. NEVER share this code with others.</h5>
                    <p>Please note that after resetting your password, two-factor authentication will be disabled. To enable it again, please go to Settings.</p>
                    <hr>
                    <p>If you did not request this, please ignore this email.</p>
                    <p>This message was sent to {$email}</p>
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

if (isset($_POST['reset'])) {
    $email = $_POST['email'];
    $token = $_POST['token'];
    $resetCode = $_POST['resetCode'];
    $newPassword = $_POST['newPassword'];
    $retypenewPassword = $_POST['retypenewPassword'];

    // Prepare statement to find matching token and email
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    $exists = $result->num_rows > 0;

    if (!$exists) {
        // Token is invalid or tampered with
        header("Location: ../forgot-password-verification.php?error=" . urlencode("Token is invalid, please do not change the URL.") . "&email=" . urlencode($email) . "&token=" . urlencode($token));
        exit();
    } else {
        $row = $result->fetch_assoc();
        $otp_code = $row['code'];
        $created_at = $row['created_at'];

        // Set timezone to Asia/Manila
        $timezone = new DateTimeZone('Asia/Manila');
        $createdAt = new DateTime($created_at, $timezone);
        $currentTime = new DateTime('now', $timezone);

        $minutesElapsed = ($currentTime->getTimestamp() - $createdAt->getTimestamp()) / 60;

        // Optional: Debug log
        error_log("Created at: " . $createdAt->format('Y-m-d H:i:s'));
        error_log("Current time: " . $currentTime->format('Y-m-d H:i:s'));
        error_log("Minutes elapsed: " . $minutesElapsed);

        // Check if the timestamp is in the future (error case)
        if ($minutesElapsed < 0) {
            header("Location: ../forgot-password.php?error=" . urlencode("Invalid system time.") . "&email=" . urlencode($email));
            exit();
        }

        // Expiry check
        if ($minutesElapsed > 15) {
            header("Location: ../forgot-password.php?error=" . urlencode("Reset Code has expired.") . "&email=" . urlencode($email));
            exit();
        } else {
            if ($resetCode === $otp_code) {
                // Check if new passwords match
                if ($newPassword !== $retypenewPassword) {
                    header("Location: ../forgot-password-verification.php?error=" . urlencode("Passwords do not match.") . "&email=" . urlencode($email) . "&token=" . urlencode($token));
                    exit();
                }

                // Hash and update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $updateStmt = $conn->prepare("UPDATE users SET password = ?, two_factor_authentication = 0 WHERE email = ?");
                $updateStmt->bind_param("ss", $hashedPassword, $email);
                $updateStmt->execute();


                header("Location: ../login.php?success=" . urlencode("Password has been reset successfully."));
                exit();
            } else {
                header("Location: ../forgot-password-verification.php?error=" . urlencode("Invalid OTP code.") . "&email=" . urlencode($email) . "&token=" . urlencode($token));
                exit();
            }
        }
    }
} else {
    header("Location: ../forgot-password.php");
    exit();
}
?>
