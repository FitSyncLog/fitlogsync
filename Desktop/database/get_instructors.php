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

// Fetch users and their roles from the database
$query = "
    SELECT
        u.user_id,
        u.username,
        u.firstname,
        u.middlename,
        u.lastname,
        u.date_of_birth,
        u.gender,
        u.phone_number,
        u.email,
        u.address,
        u.account_number,
        u.status,
        u.registration_date,
        GROUP_CONCAT(r.role SEPARATOR ', ') AS roles
    FROM
        users u
    LEFT JOIN
        user_roles r ON u.user_id = r.user_id
    GROUP BY
        u.user_id
    HAVING
        FIND_IN_SET('Instructor', roles) > 0
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(["success" => true, "users" => $users]);
} else {
    echo json_encode(["success" => false, "message" => "No users found"]);
}

$conn->close();
?>
