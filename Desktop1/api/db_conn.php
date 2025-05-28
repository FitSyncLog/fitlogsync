<?php
header("Content-Type: application/json");

function connectToDatabase() {
    $conn = new mysqli("localhost:3307", "root", "", "fitlogsync");

    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    return $conn;
}
?>
