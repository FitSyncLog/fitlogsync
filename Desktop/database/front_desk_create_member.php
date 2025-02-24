<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db_conn.php';

// Function to generate a unique account number
function generateAccountNumber($conn) {
    $year = date('Y');
    $month = str_pad(date('m'), 4, '0', STR_PAD_LEFT);
    $random = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

    $sql = "SELECT MAX(SUBSTRING(account_number, 13, 4)) AS last_increment
            FROM users
            WHERE SUBSTRING(account_number, 1, 4) = ?
            AND SUBSTRING(account_number, 5, 4) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $lastIncrement = $row['last_increment'] ?? 0;
    $increment = str_pad($lastIncrement + 1, 4, '0', STR_PAD_LEFT);

    $accountNumber = $year . $month . $random . $increment;

    return $accountNumber;
}

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check for required fields
$requiredFields = [
    "username", "firstname", "lastname", "dateofbirth",
    "gender", "address", "phonenumber", "email",
    "password", "confirm_password", "contact_person",
    "contact_number", "relationship", "waiver_rules",
    "waiver_liability", "waiver_cancel", "q1", "q2", "q3", "q4", "q5", "q6", "q7", "q8", "q9", "q10"
];

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "message" => "All fields are required except middle name."]);
        exit;
    }
}

// Validate email and phone number
if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email address."]);
    exit;
}

if (!preg_match('/^09\d{9}$/', $data["phonenumber"])) {
    echo json_encode(["success" => false, "message" => "Phone number must be 11 digits and start with '09'."]);
    exit;
}

// Validate password match
if ($data["password"] !== $data["confirm_password"]) {
    echo json_encode(["success" => false, "message" => "Passwords do not match."]);
    exit;
}

// Connect to the database
$conn = connectToDatabase();

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Sanitize input data
$username = $conn->real_escape_string($data["username"]);
$firstname = $conn->real_escape_string($data["firstname"]);
$middlename = $conn->real_escape_string($data["middlename"] ?? '');
$lastname = $conn->real_escape_string($data["lastname"]);
$dateofbirth = $conn->real_escape_string($data["dateofbirth"]);
$gender = $conn->real_escape_string($data["gender"]);
$address = $conn->real_escape_string($data["address"]);
$phonenumber = $conn->real_escape_string($data["phonenumber"]);
$email = $conn->real_escape_string($data["email"]);
$password = password_hash($data["password"], PASSWORD_DEFAULT);
$contact_person = $conn->real_escape_string($data["contact_person"]);
$contact_number = $conn->real_escape_string($data["contact_number"]);
$relationship = $conn->real_escape_string($data["relationship"]);
$waiver_rules = $data["waiver_rules"] ? 1 : 0;
$waiver_liability = $data["waiver_liability"] ? 1 : 0;
$waiver_cancel = $data["waiver_cancel"] ? 1 : 0;
$q1 = $conn->real_escape_string($data["q1"]);
$q2 = $conn->real_escape_string($data["q2"]);
$q3 = $conn->real_escape_string($data["q3"]);
$q4 = $conn->real_escape_string($data["q4"]);
$q5 = $conn->real_escape_string($data["q5"]);
$q6 = $conn->real_escape_string($data["q6"]);
$q7 = $conn->real_escape_string($data["q7"]);
$q8 = $conn->real_escape_string($data["q8"]);
$q9 = $conn->real_escape_string($data["q9"]);
$q10 = $conn->real_escape_string($data["q10"]);

// Generate account number
$accountNumber = generateAccountNumber($conn);

// Set additional fields
$status = "Not Verified";
$role = "Member";
$registration_date = date('Y-m-d');
$verification_code = "123"; // Placeholder value
$v_code_expiration = date('Y-m-d H:i:s', strtotime('+10 minutes'));

// Check if username already exists
$query = $conn->prepare("SELECT * FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Username already exists."]);
    exit;
}

// Check if email already exists
$query = $conn->prepare("SELECT * FROM users WHERE email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email is already taken."]);
    exit;
}

// Insert user data into the database
$query = $conn->prepare("INSERT INTO users (username, firstname, middlename, lastname, date_of_birth, password, gender, phone_number, email, address, account_number, status, registration_date, verification_code, v_code_expiration) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$query->bind_param("sssssssssssssss", $username, $firstname, $middlename, $lastname, $dateofbirth, $password, $gender, $phonenumber, $email, $address, $accountNumber, $status, $registration_date, $verification_code, $v_code_expiration);

if ($query->execute()) {
    $user_id = $query->insert_id;

    // Insert additional data (medical background, waivers, emergency contacts, roles)
    // Example for medical background:
    $medicalQuery = $conn->prepare("INSERT INTO medical_backgrounds (user_id, medical_conditions, current_medications, previous_injuries, par_q_1, par_q_2, par_q_3, par_q_4, par_q_5, par_q_6, par_q_7, par_q_8, par_q_9, par_q_10) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $medicalQuery->bind_param("isssssssssssss", $user_id, $data["medical_conditions"], $data["current_medications"], $data["previous_injuries"], $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10);
    $medicalQuery->execute();

    // Insert into emergency_contacts table
    $emergencyQuery = $conn->prepare("INSERT INTO emergency_contacts (user_id, contact_person, contact_number, relationship) VALUES (?, ?, ?, ?)");
    $emergencyQuery->bind_param("isss", $user_id, $contact_person, $contact_number, $relationship);
    $emergencyQuery->execute();

    // Insert into user_roles table with default role "Member"
    $roleQuery = $conn->prepare("INSERT INTO user_roles (user_id, role) VALUES (?, ?)");
    $roleQuery->bind_param("is", $user_id, $role);
    $roleQuery->execute();

    // Insert into waivers table
    $waiverQuery = $conn->prepare("INSERT INTO waivers (user_id, rules_and_policy, liability_waiver, cancellation_and_refund_policy) VALUES (?, ?, ?, ?)");
    $waiverQuery->bind_param("iiii", $user_id, $waiver_rules, $waiver_liability, $waiver_cancel);
    $waiverQuery->execute();

    echo json_encode(["success" => true, "message" => "Member created successfully", "account_number" => $accountNumber]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create member: " . $conn->error]);
}

$conn->close();
?>
