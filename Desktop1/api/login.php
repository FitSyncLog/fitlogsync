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

date_default_timezone_set("UTC");


$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit;
}

$conn = connectToDatabase();

$email = $conn->real_escape_string($data["email"]);
$password = $data["password"];

$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row["password"])) {
        $userId = $row["user_id"];

        // Check OTP expiration
        $expirationTime = $row["v_code_expiration"];
        $currentTime = date("Y-m-d H:i:s");
        $hasValidOTP = $expirationTime && strtotime($expirationTime) > strtotime($currentTime);

        // Get user roles
        $rolesQuery = $conn->prepare("SELECT role FROM user_roles WHERE user_id = ?");
        $rolesQuery->bind_param("i", $userId);
        $rolesQuery->execute();
        $rolesResult = $rolesQuery->get_result();

        $roles = [];
        while ($roleRow = $rolesResult->fetch_assoc()) {
            $roles[] = $roleRow['role'];
        }

        // Check for existing valid session
        $checkSessionQuery = $conn->prepare("
            SELECT auth_token, expires_at FROM user_sessions
            WHERE user_id = ? AND expires_at > NOW()
        ");
        $checkSessionQuery->bind_param("i", $userId);
        $checkSessionQuery->execute();
        $checkSessionResult = $checkSessionQuery->get_result();

        if ($existingSession = $checkSessionResult->fetch_assoc()) {
            // Use existing session
            $sessionToken = $existingSession['auth_token'];
            $expiresAt = $existingSession['expires_at'];
        } else {
            // Create new session
            $sessionToken = bin2hex(random_bytes(32));
            $expiresAt = date("Y-m-d H:i:s", strtotime("+7 days"));

            $insertSessionQuery = $conn->prepare("
                INSERT INTO user_sessions (user_id, auth_token, expires_at)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE auth_token = VALUES(auth_token), expires_at = VALUES(expires_at)
            ");
            $insertSessionQuery->bind_param("iss", $userId, $sessionToken, $expiresAt);
            $insertSessionQuery->execute();
        }

        // Clean up expired sessions
        $deleteExpiredQuery = $conn->prepare("DELETE FROM user_sessions WHERE expires_at < NOW()");
        $deleteExpiredQuery->execute();

        if ($hasValidOTP) {
            // OTP is still valid, proceed to login directly - NO OTP required
            $userData = [
                "success" => true,
                "token" => $sessionToken,
                "expires_at" => $expiresAt,
                "user_id" => $row["user_id"],
                "email" => $row["email"],
                "username" => $row["username"],
                "firstname" => $row["firstname"],
                "middlename" => $row["middlename"],
                "lastname" => $row["lastname"],
                "date_of_birth" => $row["date_of_birth"],
                "gender" => $row["gender"],
                "phone_number" => $row["phone_number"],
                "address" => $row["address"],
                "roles" => array_values($roles),
                "verification_code" => $row["verification_code"],
                "v_code_expiration" => $row["v_code_expiration"],
                "otp_required" => false, // This is the key change - set to false
                "message" => "Login successful"
            ];

            echo json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            // OTP expired or doesn't exist, generate new OTP
            $verificationCode = rand(100000, 999999);
            $newExpirationTime = date("Y-m-d H:i:s", strtotime("+2 days")); 

            // Store OTP in temporary table instead of users table
            // First, delete any existing temporary OTPs for this user
            $deleteTempOTPQuery = $conn->prepare("DELETE FROM temp_otp WHERE user_id = ?");
            $deleteTempOTPQuery->bind_param("i", $userId);
            $deleteTempOTPQuery->execute();
            
            // Insert new temporary OTP
            $insertTempOTPQuery = $conn->prepare("
                INSERT INTO temp_otp (user_id, email, verification_code, expiration_time)
                VALUES (?, ?, ?, ?)
            ");
            $insertTempOTPQuery->bind_param("isss", $userId, $email, $verificationCode, $newExpirationTime);
            
            // Send OTP via email
            if (sendMail($email, $verificationCode) && $insertTempOTPQuery->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "OTP sent to your email. Please verify.",
                    "otp_required" => true,
                    "token" => $sessionToken,
                    "expires_at" => $expiresAt,
                    "email" => $row["email"],
                    "username" => $row["username"],
                    "firstname" => $row["firstname"],
                    "middlename" => $row["middlename"],
                    "lastname" => $row["lastname"],
                    "date_of_birth" => $row["date_of_birth"],
                    "gender" => $row["gender"],
                    "phone_number" => $row["phone_number"],
                    "address" => $row["address"],
                    "roles" => array_values($roles),
                    "verification_code" => (string)$verificationCode, // For debug only - should be removed in production
                    "v_code_expiration" => $newExpirationTime
                ]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "Failed to send OTP or store temporary code"]);
                exit;
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid credentials"]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    exit;
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