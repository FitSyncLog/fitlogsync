<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit;
}

// Database connection
$conn = new mysqli("localhost:3307", "root", "", "fitlogsync");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$email = $conn->real_escape_string($data["email"]);
$password = $conn->real_escape_string($data["password"]);

$query = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
$query->bind_param("ss", $email, $password);
$query->execute();
$result = $query->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "username" => $row["username"], "email" => $row["email"], "message" => "Login successful"]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}

$conn->close();
?>
