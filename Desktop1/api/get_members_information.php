    <?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Content-Type: application/json");

    include 'db_conn.php';

    // Function to fetch member details
    function getMemberDetails($conn, $memberId) {
        // Fetch user details
        $userQuery = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $userQuery->bind_param("i", $memberId);
        $userQuery->execute();
        $userResult = $userQuery->get_result();
        $user = $userResult->fetch_assoc();

        if (!$user) {
            return null;
        }

        // Fetch medical background
        $medicalQuery = $conn->prepare("SELECT * FROM medical_backgrounds WHERE user_id = ?");
        $medicalQuery->bind_param("i", $memberId);
        $medicalQuery->execute();
        $medicalResult = $medicalQuery->get_result();
        $medicalBackground = $medicalResult->fetch_assoc();

        // Fetch emergency contacts
        $emergencyQuery = $conn->prepare("SELECT * FROM emergency_contacts WHERE user_id = ?");
        $emergencyQuery->bind_param("i", $memberId);
        $emergencyQuery->execute();
        $emergencyResult = $emergencyQuery->get_result();
        $emergencyContact = $emergencyResult->fetch_assoc();

        // Fetch waivers
        $waiverQuery = $conn->prepare("SELECT * FROM waivers WHERE user_id = ?");
        $waiverQuery->bind_param("i", $memberId);
        $waiverQuery->execute();
        $waiverResult = $waiverQuery->get_result();
        $waivers = $waiverResult->fetch_assoc();

        // Fetch roles
        $roleQuery = $conn->prepare("SELECT role FROM user_roles WHERE user_id = ?");
        $roleQuery->bind_param("i", $memberId);
        $roleQuery->execute();
        $roleResult = $roleQuery->get_result();
        $roles = [];
        while ($role = $roleResult->fetch_assoc()) {
            $roles[] = $role['role'];
        }

        // Combine all details into a single array
        $memberDetails = array_merge($user, $medicalBackground, $emergencyContact, $waivers, ['roles' => $roles]);

        return $memberDetails;
    }

    // Connect to the database
    $conn = connectToDatabase();

    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        echo json_encode(["success" => false, "message" => "Database connection failed"]);
        exit;
    }

    // Get member ID from query parameter
    $memberId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

    if ($memberId <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid member ID"]);
        exit;
    }

    // Fetch member details
    $memberDetails = getMemberDetails($conn, $memberId);

    if ($memberDetails) {
        echo json_encode(["success" => true, "data" => $memberDetails]);
    } else {
        echo json_encode(["success" => false, "message" => "Member not found"]);
    }

    $conn->close();
    ?>
