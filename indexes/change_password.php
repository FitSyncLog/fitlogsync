<?php
session_start();
include 'db_con.php';

if (isset($_POST['changePassword'])) {

    function validate($data)
    {
        $data = trim($data ?? '');
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $user_id = validate($_SESSION['user_id']);
    $current_password = validate($_POST['current_password']);
    $new_password = validate($_POST['new_password']);
    $confirm_password = validate($_POST['confirm_password']);

    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        header("Location: ../settings.php?error=New password and confirm password do not match.");
        exit();
    }

    // Fetch the current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify the current password
    if (!password_verify($current_password, $user['password'])) {
        header("Location: ../settings.php?error=Current password is incorrect.");
        exit();
    }

    // Check if new password is the same as the current password
    if (password_verify($new_password, $user['password'])) {
        header("Location: ../settings.php?error=New password cannot be the same as the current password.");
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $update_stmt->bind_param("si", $hashed_password, $user_id);

    if ($update_stmt->execute()) {
        header("Location: ../settings.php?success=Password updated successfully.");
    } else {
        header("Location: ../settings.php?error=Failed to update password.");
    }

    $update_stmt->close();
    $conn->close();

} else {
    header("Location: ../settings.php");
    exit();
}
?>
