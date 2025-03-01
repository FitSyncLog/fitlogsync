<?php
session_start();

include "../../indexes/db_con.php";

if (isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin') {

    $user_id = $_GET['user_id'];
    $status = $_GET['status'];

    // address
    $sql_update_status = "UPDATE users SET status = ? WHERE user_id = ?";
    $stmt_update_status = mysqli_prepare($conn, $sql_update_status);
    mysqli_stmt_bind_param($stmt_update_status, "si", $status, $user_id);
    $result_update_status = mysqli_stmt_execute($stmt_update_status);

    if ($result_update_status) {
        header("Location: ../manage-members.php?Success=Account Status successfully updated to " . $status);
        exit();
    } else {
        header("Location: ../manage-members.php?Failed=Failed to update status");
        exit();
    }

} else {
    header("Location: ../../login.php?LoginFirst=Please login first.");
    exit();
}
?>