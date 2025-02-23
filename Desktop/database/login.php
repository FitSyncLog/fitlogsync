<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db_conn.php';

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

        // Retrieve user roles
        $userRolesQuery = $conn->prepare("SELECT role FROM user_roles WHERE user_id = ?");
        $userRolesQuery->bind_param("i", $userId);
        $userRolesQuery->execute();
        $userRolesResult = $userRolesQuery->get_result();

        $roles = [];
        while ($roleRow = $userRolesResult->fetch_assoc()) {
            $roles[] = $roleRow['role'];
        }

        // ✅ Ensure roles is a proper array, not an object
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
            "roles" => array_values($roles), // 🔹 Fix roles array format
            "message" => "Login successful"
        ];

        // Debugging Log
        error_log("Response JSON: " . json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        echo json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}

$conn->close();
?>
