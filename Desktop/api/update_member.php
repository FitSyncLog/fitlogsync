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

    // Set default values for potentially missing fields
    $data['username'] = isset($data['username']) ? $data['username'] : '';
    $data['firstname'] = isset($data['firstname']) ? $data['firstname'] : '';
    $data['middlename'] = isset($data['middlename']) ? $data['middlename'] : '';
    $data['lastname'] = isset($data['lastname']) ? $data['lastname'] : '';
    $data['email'] = isset($data['email']) ? $data['email'] : '';
    $data['date_of_birth'] = isset($data['date_of_birth']) ? $data['date_of_birth'] : '';
    $data['gender'] = isset($data['gender']) ? $data['gender'] : '';
    $data['phone_number'] = isset($data['phone_number']) ? $data['phone_number'] : '';
    $data['address'] = isset($data['address']) ? $data['address'] : '';
    $data['status'] = isset($data['status']) ? $data['status'] : 'Inactive';
    $data['subscription_status'] = isset($data['subscription_status']) ? $data['subscription_status'] : 'Pending';
    $data['medical_conditions'] = isset($data['medical_conditions']) ? $data['medical_conditions'] : '';
    $data['current_medications'] = isset($data['current_medications']) ? $data['current_medications'] : '';
    $data['previous_injuries'] = isset($data['previous_injuries']) ? $data['previous_injuries'] : '';
    $data['contact_person'] = isset($data['contact_person']) ? $data['contact_person'] : '';
    $data['contact_number'] = isset($data['contact_number']) ? $data['contact_number'] : '';
    $data['relationship'] = isset($data['relationship']) ? $data['relationship'] : '';
    $data['rules_and_policy'] = isset($data['rules_and_policy']) ? $data['rules_and_policy'] : false;
    $data['liability_waiver'] = isset($data['liability_waiver']) ? $data['liability_waiver'] : false;
    $data['cancellation_and_refund_policy'] = isset($data['cancellation_and_refund_policy']) ? $data['cancellation_and_refund_policy'] : false;

    // Update user table
    $updateUserQuery = $conn->prepare("
        UPDATE users 
        SET 
            username = ?,
            firstname = ?,
            middlename = ?,
            lastname = ?,
            email = ?,
            date_of_birth = ?,
            gender = ?,
            phone_number = ?,
            address = ?,
            status = ?,
            subscription_status = ?
        WHERE account_number = ?
    ");

    $updateUserQuery->bind_param(
        "ssssssssssss",
        $data['username'],
        $data['firstname'],
        $data['middlename'],
        $data['lastname'],
        $data['email'],
        $data['date_of_birth'],
        $data['gender'],
        $data['phone_number'],
        $data['address'],
        $data['status'],
        $data['subscription_status'],
        $data['account_number']
    );

    $updateUserQuery->execute();

    // Get the user ID from account number
    $userIdQuery = $conn->prepare("SELECT user_id FROM users WHERE account_number = ?");
    $userIdQuery->bind_param("s", $data['account_number']);
    $userIdQuery->execute();
    $userIdResult = $userIdQuery->get_result();
    
    if ($userIdResult->num_rows === 0) {
        throw new Exception("User not found");
    }
    
    $userId = $userIdResult->fetch_assoc()['user_id'];

    // Check if medical background record exists for this user
    $checkMedicalQuery = $conn->prepare("SELECT COUNT(*) as count FROM medical_backgrounds WHERE user_id = ?");
    $checkMedicalQuery->bind_param("i", $userId);
    $checkMedicalQuery->execute();
    $medicalResult = $checkMedicalQuery->get_result();
    $medicalExists = $medicalResult->fetch_assoc()['count'] > 0;

    // Update or insert medical background
    if ($medicalExists) {
        $updateMedicalQuery = $conn->prepare("
            UPDATE medical_backgrounds 
            SET 
                medical_conditions = ?,
                current_medications = ?,
                previous_injuries = ?
            WHERE user_id = ?
        ");

        $updateMedicalQuery->bind_param(
            "sssi",
            $data['medical_conditions'],
            $data['current_medications'],
            $data['previous_injuries'],
            $userId
        );

        $updateMedicalQuery->execute();
    } else {
        $insertMedicalQuery = $conn->prepare("
            INSERT INTO medical_backgrounds 
            (user_id, medical_conditions, current_medications, previous_injuries)
            VALUES (?, ?, ?, ?)
        ");

        $insertMedicalQuery->bind_param(
            "isss",
            $userId,
            $data['medical_conditions'],
            $data['current_medications'],
            $data['previous_injuries']
        );

        $insertMedicalQuery->execute();
    }

    // Check if emergency contact record exists for this user
    $checkEmergencyQuery = $conn->prepare("SELECT COUNT(*) as count FROM emergency_contacts WHERE user_id = ?");
    $checkEmergencyQuery->bind_param("i", $userId);
    $checkEmergencyQuery->execute();
    $emergencyResult = $checkEmergencyQuery->get_result();
    $emergencyExists = $emergencyResult->fetch_assoc()['count'] > 0;

    // Update or insert emergency contacts
    if ($emergencyExists) {
        $updateEmergencyQuery = $conn->prepare("
            UPDATE emergency_contacts 
            SET 
                contact_person = ?,
                contact_number = ?,
                relationship = ?
            WHERE user_id = ?
        ");

        $updateEmergencyQuery->bind_param(
            "sssi",
            $data['contact_person'],
            $data['contact_number'],
            $data['relationship'],
            $userId
        );

        $updateEmergencyQuery->execute();
    } else {
        $insertEmergencyQuery = $conn->prepare("
            INSERT INTO emergency_contacts 
            (user_id, contact_person, contact_number, relationship)
            VALUES (?, ?, ?, ?)
        ");

        $insertEmergencyQuery->bind_param(
            "isss",
            $userId,
            $data['contact_person'],
            $data['contact_number'],
            $data['relationship']
        );

        $insertEmergencyQuery->execute();
    }

    // Check if waivers record exists for this user
    $checkWaiversQuery = $conn->prepare("SELECT COUNT(*) as count FROM waivers WHERE user_id = ?");
    $checkWaiversQuery->bind_param("i", $userId);
    $checkWaiversQuery->execute();
    $waiversResult = $checkWaiversQuery->get_result();
    $waiversExists = $waiversResult->fetch_assoc()['count'] > 0;

    // Convert boolean values to integers
    $rulesAndPolicy = $data['rules_and_policy'] ? 1 : 0;
    $liabilityWaiver = $data['liability_waiver'] ? 1 : 0;
    $cancellationPolicy = $data['cancellation_and_refund_policy'] ? 1 : 0;

    // Update or insert waivers
    if ($waiversExists) {
        $updateWaiversQuery = $conn->prepare("
            UPDATE waivers 
            SET 
                rules_and_policy = ?,
                liability_waiver = ?,
                cancellation_and_refund_policy = ?
            WHERE user_id = ?
        ");

        $updateWaiversQuery->bind_param(
            "iiii",
            $rulesAndPolicy,
            $liabilityWaiver,
            $cancellationPolicy,
            $userId
        );

        $updateWaiversQuery->execute();
    } else {
        $insertWaiversQuery = $conn->prepare("
            INSERT INTO waivers 
            (user_id, rules_and_policy, liability_waiver, cancellation_and_refund_policy)
            VALUES (?, ?, ?, ?)
        ");

        $insertWaiversQuery->bind_param(
            "iiii",
            $userId,
            $rulesAndPolicy,
            $liabilityWaiver,
            $cancellationPolicy
        );

        $insertWaiversQuery->execute();
    }

    // Commit the transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Member updated successfully']);

} catch (Exception $e) {
    // Rollback the transaction
    $conn->rollback();
    error_log("Error updating member: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating member: ' . $e->getMessage()]);
} finally {
    $conn->close();
}