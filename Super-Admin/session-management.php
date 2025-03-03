<?php
// Secure session settings before session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

session_start();
include "../indexes/db_con.php";

// Check if user is logged in and is a Super Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: ../login.php?error=Unauthorized access");
    exit();
}
?>