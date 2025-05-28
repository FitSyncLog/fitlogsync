<?php
require_once 'db_con.php';

if (!isset($_GET['user_id']) || !isset($_GET['status'])) {
    header("Location: ../manage-members.php?Failed=Invalid request");
    exit();
}

$user_id = $_GET['user_id'];
$status = $_GET['status'];
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'members';

// Validate status
$valid_statuses = ['Active', 'Inactive', 'Suspended', 'Banned'];
if (!in_array($status, $valid_statuses)) {
    header("Location: ../manage-members.php?Failed=Invalid status");
    exit();
}

// Update user status
$query = "UPDATE users SET status = ? WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status, $user_id);

if ($stmt->execute()) {
    $success_message = "Member status updated successfully";
    if ($redirect === 'profile') {
        header("Location: ../member-profile.php?user_id=$user_id&Success=$success_message");
    } else {
        header("Location: ../manage-members.php?Success=$success_message");
    }
} else {
    $error_message = "Failed to update member status";
    if ($redirect === 'profile') {
        header("Location: ../member-profile.php?user_id=$user_id&Failed=$error_message");
    } else {
        header("Location: ../manage-members.php?Failed=$error_message");
    }
}
exit();
?> 