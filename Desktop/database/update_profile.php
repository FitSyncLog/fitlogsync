<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db_conn.php';

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check for user_id
if (empty($data["user_id"])) {
    echo json_encode(["success" => false, "message" => "User ID is required."]);
    exit;
}

// Connect to the database
$conn = connectToDatabase();

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Sanitize input data
$user_id = intval($data["user_id"]);

// Check if user exists
$checkQuery = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$checkQuery->bind_param("i", $user_id);
$checkQuery->execute();
$result = $checkQuery->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Update user data
    if (isset($data["user"])) {
        $userFields = [];
        $userParams = [];
        $userTypes = "";
        
        $allowedFields = [
            "username" => "s", 
            "firstname" => "s", 
            "middlename" => "s", 
            "lastname" => "s",
            "date_of_birth" => "s", 
            "gender" => "s", 
            "phone_number" => "s", 
            "email" => "s",
            "address" => "s"
        ];
        
        foreach ($allowedFields as $field => $type) {
            if (isset($data["user"][$field])) {
                $userFields[] = "$field = ?";
                $userParams[] = $data["user"][$field];
                $userTypes .= $type;
            }
        }
        
        // Handle password update separately
        if (!empty($data["user"]["password"]) && !empty($data["user"]["confirm_password"])) {
            if ($data["user"]["password"] !== $data["user"]["confirm_password"]) {
                throw new Exception("Passwords do not match.");
            }
            
            $userFields[] = "password = ?";
            $userParams[] = password_hash($data["user"]["password"], PASSWORD_DEFAULT);
            $userTypes .= "s";
        }
        
        if (!empty($userFields)) {
            $userSQL = "UPDATE users SET " . implode(", ", $userFields) . " WHERE user_id = ?";
            $userParams[] = $user_id;
            $userTypes .= "i";
            
            $userStmt = $conn->prepare($userSQL);
            $userStmt->bind_param($userTypes, ...$userParams);
            $userStmt->execute();
        }
    }
    
    // Update emergency contact
    if (isset($data["emergency_contact"])) {
        // Check if emergency contact exists
        $checkEmergency = $conn->prepare("SELECT * FROM emergency_contacts WHERE user_id = ?");
        $checkEmergency->bind_param("i", $user_id);
        $checkEmergency->execute();
        $emergencyResult = $checkEmergency->get_result();
        
        if ($emergencyResult->num_rows > 0) {
            // Update existing emergency contact
            $emergencySQL = "UPDATE emergency_contacts SET 
                contact_person = ?, 
                contact_number = ?, 
                relationship = ? 
                WHERE user_id = ?";
            
            $emergencyStmt = $conn->prepare($emergencySQL);
            $emergencyStmt->bind_param(
                "sssi", 
                $data["emergency_contact"]["contact_person"], 
                $data["emergency_contact"]["contact_number"], 
                $data["emergency_contact"]["relationship"], 
                $user_id
            );
            $emergencyStmt->execute();
        } else {
            // Insert new emergency contact
            $emergencySQL = "INSERT INTO emergency_contacts (user_id, contact_person, contact_number, relationship) 
                VALUES (?, ?, ?, ?)";
            
            $emergencyStmt = $conn->prepare($emergencySQL);
            $emergencyStmt->bind_param(
                "isss", 
                $user_id, 
                $data["emergency_contact"]["contact_person"], 
                $data["emergency_contact"]["contact_number"], 
                $data["emergency_contact"]["relationship"]
            );
            $emergencyStmt->execute();
        }
    }
    
    // Commit transaction
    $conn->commit();
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Failed to update profile: " . $e->getMessage()]);
}

$conn->close();
?>