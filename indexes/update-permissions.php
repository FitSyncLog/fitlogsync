<?php
include '../session-management.php';
include 'db_con.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $entered_password = $_POST['confirm_password'] ?? '';

    // Step 1: Get the hashed password from DB
    $query = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        header("Location: ../permission-settings.php?Failed=User not found");
        exit();
    }

    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    // Step 2: Verify password
    if (!password_verify($entered_password, $hashed_password)) {
        header("Location: ../permission-settings.php?Failed=Incorrect password");
        exit();
    }

    // Step 3: Continue updating permissions
    $submitted_permissions = $_POST['permissions'];

    $roles_query = "SELECT role_id FROM roles";
    $roles_result = $conn->query($roles_query);
    $roles = [];
    while ($row = $roles_result->fetch_assoc()) {
        $roles[] = $row['role_id'];
    }

    $pages_query = "SELECT DISTINCT page_name FROM permissions";
    $pages_result = $conn->query($pages_query);
    $pages = [];
    while ($row = $pages_result->fetch_assoc()) {
        $pages[] = $row['page_name'];
    }

    foreach ($roles as $role_id) {
        foreach ($pages as $page_name) {
            $is_checked = isset($submitted_permissions[$role_id][$page_name]) ? 1 : 0;

            $query = "UPDATE permissions SET permission = ? WHERE role_id = ? AND page_name = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iis", $is_checked, $role_id, $page_name);
            $stmt->execute();
        }
    }

    header("Location: ../permission-settings.php?Success=Permissions updated successfully");
    exit();
} else {
    header("Location: ../permission-settings.php?Error=Invalid request");
    exit();
}
?>
