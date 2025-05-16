<?php
session_start();

include "db_con.php";

if (isset($_SESSION['login']) ) {
    $user_id = $_GET['user_id'];
    $status = $_GET['status'];

    // Update status query
    $sql_update_status = "UPDATE users SET status = ? WHERE user_id = ?";
    $stmt_update_status = mysqli_prepare($conn, $sql_update_status);
    mysqli_stmt_bind_param($stmt_update_status, "si", $status, $user_id);
    $result_update_status = mysqli_stmt_execute($stmt_update_status);

    // Get base URL without previous query parameters
    $base_url = strtok($_SERVER['HTTP_REFERER'], '?');

    if ($result_update_status) {
        header("Location: $base_url?Success=Account Status successfully updated to " . urlencode($status));
        exit();
    } else {
        header("Location: $base_url?Failed=Failed to update status");
        exit();
    }

} else {
    header("Location: ../login.php?LoginFirst=Please login first.");
    exit();
}
