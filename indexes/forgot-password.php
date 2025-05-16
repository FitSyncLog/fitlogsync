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
                    <hr>
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

if (isset($_POST['search'])) {

    $email = $_POST['email'];

    // Corrected query: column name should not have $ sign
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $exists = $result->num_rows > 0;

    if (!$exists) {
        // Email not found, redirect with message
        header("Location: ../forgot-password.php?notexists=The email is not existing.");
        exit();
    } else {
        $otpCode = rand(100000, 999999);

        // Prepare the statement to get the first_name
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the first_name
        $user = $result->fetch_assoc();
        $name = $user['firstname'];

        // Corrected query: column name should not have $ sign
        $resetstmt = $conn->prepare("INSERT INTO password_resets (email, code) VALUES (?, ?)");
        $resetstmt->bind_param("ss", $email, $otpCode);
        $resetstmt->execute();
        $resetresult = $resetstmt->get_result();
        $exists = $resetresult->num_rows > 0;

        if (sendMail($email, $otpCode, $name)) {
            // Redirect to OTP verification page
            $_SESSION['user_id'] = $user_id;
            header("Location: ../forgot-password-verification.php?email=" . urlencode($email));
            exit();
        } else {
            header("Location: ../login.php?UnexpectedError=Failed to send OTP. Please try again.&$user_data");
            exit();
        }
    }


} else {
    header("Location: ../forgot-password.php");
    exit();
}

?>