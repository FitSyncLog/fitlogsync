<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db_conn.php';

// Connect to the database
$conn = connectToDatabase();

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$current_date = date("Y-m-d");

// Fetch users with their roles and subscription status
$userQuery = "
    SELECT
        u.user_id,
        u.username,
        u.firstname,
        u.middlename,
        u.lastname,
        u.email,
        u.gender,
        u.phone_number,
        u.address,
        u.date_of_birth,
        u.account_number,
        u.status,
        u.enrolled_by,
        u.status AS account_status,
        u.registration_date,
        GROUP_CONCAT(r.role SEPARATOR ', ') AS roles,
        CASE
            WHEN s.user_id IS NULL THEN 'No Subscription'
            WHEN s.expiration_date >= '$current_date' THEN 'Active'
            ELSE 'Expired'
        END AS subscription_status
    FROM users u
    LEFT JOIN user_roles r ON u.user_id = r.user_id
    LEFT JOIN (
        SELECT user_id, MAX(starting_date) AS latest_starting_date, expiration_date
        FROM subscription
        WHERE starting_date <= '$current_date'
        GROUP BY user_id
    ) s ON u.user_id = s.user_id
    GROUP BY u.user_id
    HAVING FIND_IN_SET('Member', roles) > 0
";

$userResult = $conn->query($userQuery);

if ($userResult->num_rows > 0) {
    $users = [];
    while ($userRow = $userResult->fetch_assoc()) {
        $userId = $userRow['user_id'];

        // Fetch medical background
        $medicalQuery = "SELECT * FROM medical_backgrounds WHERE user_id = ?";
        $medicalStmt = $conn->prepare($medicalQuery);
        $medicalStmt->bind_param("i", $userId);
        $medicalStmt->execute();
        $medicalResult = $medicalStmt->get_result()->fetch_assoc();

        // Fetch emergency contacts
        $emergencyQuery = "SELECT * FROM emergency_contacts WHERE user_id = ?";
        $emergencyStmt = $conn->prepare($emergencyQuery);
        $emergencyStmt->bind_param("i", $userId);
        $emergencyStmt->execute();
        $emergencyResult = $emergencyStmt->get_result()->fetch_assoc();

        // Fetch waivers
        $waiverQuery = "SELECT * FROM waivers WHERE user_id = ?";
        $waiverStmt = $conn->prepare($waiverQuery);
        $waiverStmt->bind_param("i", $userId);
        $waiverStmt->execute();
        $waiverResult = $waiverStmt->get_result()->fetch_assoc();

        // Combine all details into a single array
        $userDetails = array_merge($userRow, $medicalResult ?? [], $emergencyResult ?? [], $waiverResult ?? []);
        $users[] = $userDetails;
    }

    echo json_encode(["success" => true, "users" => $users]);
} else {
    echo json_encode(["success" => false, "message" => "No members found"]);
}

$conn->close();
?>
