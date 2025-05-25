<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db_conn.php';

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if required fields are present
if (!isset($data['account_number']) || empty($data['account_number'])) {
    echo json_encode(['success' => false, 'message' => 'Account number is required']);
    exit;
}

// Connect to the database
$conn = connectToDatabase();

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Start a transaction
    $conn->begin_transaction();

    // Get the user ID from account number
    $userIdQuery = $conn->prepare("SELECT user_id FROM users WHERE account_number = ?");
    $userIdQuery->bind_param("s", $data['account_number']);
    $userIdQuery->execute();
    $userIdResult = $userIdQuery->get_result();
    
    if ($userIdResult->num_rows === 0) {
        throw new Exception("User not found");
    }
    
    $userId = $userIdResult->fetch_assoc()['user_id'];

    // Delete from related tables first (foreign key constraints)
    
    // Delete medical background
    $deleteMedicalQuery = $conn->prepare("DELETE FROM medical_backgrounds WHERE user_id = ?");
    $deleteMedicalQuery->bind_param("i", $userId);
    $deleteMedicalQuery->execute();

    // Delete emergency contacts
    $deleteEmergencyQuery = $conn->prepare("DELETE FROM emergency_contacts WHERE user_id = ?");
    $deleteEmergencyQuery->bind_param("i", $userId);
    $deleteEmergencyQuery->execute();

    // Delete waivers
    $deleteWaiversQuery = $conn->prepare("DELETE FROM waivers WHERE user_id = ?");
    $deleteWaiversQuery->bind_param("i", $userId);
    $deleteWaiversQuery->execute();

    // Delete user roles
    $deleteRolesQuery = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
    $deleteRolesQuery->bind_param("i", $userId);
    $deleteRolesQuery->execute();

    // Finally, delete the user
    $deleteUserQuery = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $deleteUserQuery->bind_param("i", $userId);
    $deleteUserQuery->execute();

    // Commit the transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Member deleted successfully']);

} catch (Exception $e) {
    // Rollback the transaction
    $conn->rollback();
    error_log("Error deleting member: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error deleting member: ' . $e->getMessage()]);
} finally {
    $conn->close();
}