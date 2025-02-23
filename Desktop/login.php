<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

// Debug print to check received data
error_log(print_r($data, true));

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
$password = $data["password"]; // No need to escape, it's used with password_verify()

// Fetch user from database
$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($row = $result->fetch_assoc()) {
    // Verify hashed password
    if (password_verify($password, $row["password"])) {
        // Prepare user data to return
        $userData = [
            "success" => true,
            "username" => $row["username"],
            "email" => $row["email"],
            "firstname" => $row["firstname"],
            "middlename" => $row["middlename"],
            "lastname" => $row["lastname"],
            "date_of_birth" => $row["date_of_birth"],
            "gender" => $row["gender"],
            "phone_number" => $row["phone_number"],
            "address" => $row["address"],
            "message" => "Login successful"
        ];
        echo json_encode($userData);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}

$conn->close();
?>