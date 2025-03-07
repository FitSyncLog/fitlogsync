<?php
error_reporting(E_ALL); // Enable full error reporting for debugging
ini_set('display_errors', 1); // Show errors

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db_conn.php';

// Set consistent timezone
date_default_timezone_set("UTC");

// Log function to record debugging information
function debug_log($message) {
    error_log("[OTP Debug] " . $message);
}

$data = json_decode(file_get_contents("php://input"), true);

// Validate input data
if (empty($data["email"]) || empty($data["verification_code"])) {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    debug_log("Missing required fields: " . json_encode($data));
    exit;
}

$conn = connectToDatabase();

$email = $conn->real_escape_string($data["email"]);
$verificationCode = $conn->real_escape_string($data["verification_code"]);

debug_log("Verifying OTP - Email: $email, Code: $verificationCode");

// First, retrieve user data
$userQuery = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$userQuery->bind_param("s", $email);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userRow = $userResult->fetch_assoc()) {
    $userId = $userRow["user_id"];
    debug_log("User found with ID: $userId");
    
    // Check if there's a valid OTP in the temporary table
    $checkOTPQuery = $conn->prepare("
        SELECT * FROM temp_otp 
        WHERE user_id = ? AND email = ? AND verification_code = ? AND expiration_time > NOW()
    ");
    $checkOTPQuery->bind_param("iss", $userId, $email, $verificationCode);
    $checkOTPQuery->execute();
    $otpResult = $checkOTPQuery->get_result();
    
    // Check if the query found matching results
    if ($otpResult->num_rows === 0) {
        debug_log("No valid OTP found. Checking if expired...");
        
        // Check if OTP exists but expired
        $checkExpiredQuery = $conn->prepare("
            SELECT id, expiration_time FROM temp_otp 
            WHERE user_id = ? AND email = ? AND verification_code = ?
        ");
        $checkExpiredQuery->bind_param("iss", $userId, $email, $verificationCode);
        $checkExpiredQuery->execute();
        $expiredResult = $checkExpiredQuery->get_result();
        
        if ($expiredRow = $expiredResult->fetch_assoc()) {
            debug_log("OTP found but expired: " . $expiredRow['expiration_time'] . " vs NOW(): " . date('Y-m-d H:i:s'));
            echo json_encode(["success" => false, "message" => "OTP has expired. Please request a new one."]);
        } else {
            debug_log("OTP not found at all in temp_otp table");
            echo json_encode(["success" => false, "message" => "Invalid OTP"]);
        }
        exit;
    }
    
    $otpRow = $otpResult->fetch_assoc();
    debug_log("Valid OTP found: " . json_encode($otpRow));
    
    // OTP is valid, update the user's record with the verified OTP
    $updateUserQuery = $conn->prepare("
        UPDATE users 
        SET verification_code = ?, v_code_expiration = ? 
        WHERE user_id = ?
    ");
    $updateUserQuery->bind_param("ssi", $verificationCode, $otpRow["expiration_time"], $userId);
    
    if ($updateUserQuery->execute()) {
        debug_log("User record updated successfully");
        
        // Delete the temporary OTP record
        $deleteTempOTPQuery = $conn->prepare("DELETE FROM temp_otp WHERE id = ?");
        $deleteTempOTPQuery->bind_param("i", $otpRow["id"]);
        $deleteTempOTPQuery->execute();
        debug_log("Temporary OTP record deleted");
        
        // Get user roles
        $rolesQuery = $conn->prepare("SELECT role FROM user_roles WHERE user_id = ?");
        $rolesQuery->bind_param("i", $userId);
        $rolesQuery->execute();
        $rolesResult = $rolesQuery->get_result();

        $roles = [];
        while ($roleRow = $rolesResult->fetch_assoc()) {
            $roles[] = $roleRow['role'];
        }
        debug_log("User roles retrieved: " . json_encode($roles));
        
        // Get user data
        $userDataQuery = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $userDataQuery->bind_param("i", $userId);
        $userDataQuery->execute();
        $userDataResult = $userDataQuery->get_result();
        $userData = $userDataResult->fetch_assoc();
        debug_log("User data retrieved successfully");
        
        // Get session data
        $sessionQuery = $conn->prepare("
            SELECT auth_token, expires_at FROM user_sessions
            WHERE user_id = ? AND expires_at > NOW()
            ORDER BY expires_at DESC LIMIT 1
        ");
        $sessionQuery->bind_param("i", $userId);
        $sessionQuery->execute();
        $sessionResult = $sessionQuery->get_result();
        
        if ($sessionResult->num_rows === 0) {
            debug_log("No valid session found, creating new session");
            
            // Create new session if none exists
            $sessionToken = bin2hex(random_bytes(32));
            $expiresAt = date("Y-m-d H:i:s", strtotime("+7 days"));
            
            $createSessionQuery = $conn->prepare("
                INSERT INTO user_sessions (user_id, auth_token, expires_at)
                VALUES (?, ?, ?)
            ");
            $createSessionQuery->bind_param("iss", $userId, $sessionToken, $expiresAt);
            $createSessionQuery->execute();
            
            $sessionData = [
                "auth_token" => $sessionToken,
                "expires_at" => $expiresAt
            ];
        } else {
            $sessionData = $sessionResult->fetch_assoc();
            debug_log("Valid session found: " . json_encode($sessionData));
        }
        
        echo json_encode([
            "success" => true,
            "message" => "OTP verified successfully",
            "token" => $sessionData["auth_token"],
            "expires_at" => $sessionData["expires_at"],
            "email" => $userData["email"],
            "username" => $userData["username"],
            "firstname" => $userData["firstname"],
            "middlename" => $userData["middlename"],
            "lastname" => $userData["lastname"],
            "date_of_birth" => $userData["date_of_birth"],
            "gender" => $userData["gender"],
            "phone_number" => $userData["phone_number"],
            "address" => $userData["address"],
            "roles" => array_values($roles),
            "verification_code" => $verificationCode,
            "v_code_expiration" => $otpRow["expiration_time"]
        ]);
    } else {
        debug_log("Failed to update user record: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Failed to update user verification data"]);
    }
} else {
    debug_log("User with email $email not found");
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$conn->close();
debug_log("Connection closed");