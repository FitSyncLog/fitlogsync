<?php
error_reporting(0); // Disable error reporting
ini_set('display_errors', 0); // Hide errors from the output

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db_conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require("../../indexes/PHPMailer/PHPMailer.php");
require("../../indexes/PHPMailer/SMTP.php");
require("../../indexes/PHPMailer/Exception.php");

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["email"])) {
    echo json_encode(["success" => false, "message" => "Email is required"]);
    exit;
}

$conn = connectToDatabase();

$email = $conn->real_escape_string($data["email"]);

// Get user ID from email
$userQuery = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$userQuery->bind_param("s", $email);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userRow = $userResult->fetch_assoc()) {
    $userId = $userRow["user_id"];
    
    // Generate new OTP
    $verificationCode = rand(100000, 999999);
    $newExpirationTime = date("Y-m-d H:i:s", strtotime("+2 days"));
    
    // Delete existing temporary OTP for this user
    $deleteTempOTPQuery = $conn->prepare("DELETE FROM temp_otp WHERE user_id = ?");
    $deleteTempOTPQuery->bind_param("i", $userId);
    $deleteTempOTPQuery->execute();
    
    // Store new OTP in temporary table
    $insertTempOTPQuery = $conn->prepare("
        INSERT INTO temp_otp (user_id, email, verification_code, expiration_time)
        VALUES (?, ?, ?, ?)
    ");
    $insertTempOTPQuery->bind_param("isss", $userId, $email, $verificationCode, $newExpirationTime);
    
    // Send OTP via email
    if (sendMail($email, $verificationCode) && $insertTempOTPQuery->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "OTP sent successfully",
            "verification_code" => (string)$verificationCode, // For debug only - should be removed in production
            "v_code_expiration" => $newExpirationTime
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to send OTP"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$conn->close();

function sendMail($newEmail, $verificationCode)
{
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
        $mail->addAddress($newEmail);

        $mail->isHTML(true);
        $mail->Subject = 'One Time Password | FiT-LOGSYNC';

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
            <h3>Hello, Lowie Jay!</h3>
            <p>Your Fit-LOGSYNC Login OTP Code is:</p>
            <h1 class='otp-code'>{$verificationCode}</h1>
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
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}