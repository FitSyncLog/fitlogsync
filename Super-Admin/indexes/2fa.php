<?php
session_start();
include "../../indexes/db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];

    // Handle password verification
    if (isset($_POST['verifyPassword'])) {
        $password = $_POST['password'];
        $twoFactorSwitch = $_POST['twoFactorSwitch'];

        // Fetch the hashed password from the database
        $sql = "SELECT password FROM users WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $hashedPassword);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            // Password is correct, update 2FA settings
            $sql__two_factor_auth = "UPDATE users SET two_factor_authentication = ? WHERE user_id = ?";
            $stmt__two_factor_auth = mysqli_prepare($conn, $sql__two_factor_auth);
            mysqli_stmt_bind_param($stmt__two_factor_auth, "ii", $twoFactorSwitch, $userId);
            $result__two_factor_auth = mysqli_stmt_execute($stmt__two_factor_auth);

            if ($result__two_factor_auth) {
                header("Location: ../settings.php?Success=Successfully updated Two Factor Authentication");
                exit();
            } else {
                header("Location: ../settings.php?Failed=Failed to update Two Factor Authentication");
                exit();
            }
        } else {
            // Password is incorrect
            header("Location: ../settings.php?Failed=Incorrect password");
            exit();
        }
    }
}
?>