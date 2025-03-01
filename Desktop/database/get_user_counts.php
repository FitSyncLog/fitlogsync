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

// Query to get the count of members and instructors
$query = "
    SELECT 
        SUM(CASE WHEN ur.role = 'Member' THEN 1 ELSE 0 END) AS member_count,
        SUM(CASE WHEN ur.role = 'Instructor' THEN 1 ELSE 0 END) AS instructor_count
    FROM users u
    LEFT JOIN user_roles ur ON u.user_id = ur.user_id
";

$result = $conn->query($query);

if ($result) {
    $data = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "members" => (int)$data["member_count"],
        "instructors" => (int)$data["instructor_count"]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Error retrieving counts"]);
}

$conn->close();
?>
