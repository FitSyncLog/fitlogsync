<?php
// Include the database connection file
include 'db_con.php';

// Get the type (username or email) and value from the request
$type = $_GET['type']; // 'username' or 'email'
$value = $_GET['value'];

// Validate the type
if (!in_array($type, ['username', 'email'])) {
    echo json_encode(['exists' => false]);
    exit();
}

// Prepare and execute the query
$stmt = $conn->prepare("SELECT * FROM users WHERE $type = ?");
$stmt->bind_param("s", $value);
$stmt->execute();
$result = $stmt->get_result();

// Check if the value exists
$exists = $result->num_rows > 0;

// Return the result as JSON
echo json_encode(['exists' => $exists]);

// Close the connection
$stmt->close();
$conn->close();
?>