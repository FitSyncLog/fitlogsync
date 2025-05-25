<?php
session_start();
include "db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_id = $_POST['plan_id'];
    $price = $_POST['price'];
    $user_id = $_SESSION['user_id'];
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? 1 : 0;

    $query = "UPDATE plans SET price = ?, status = ? WHERE plan_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dii", $price, $status, $plan_id);

    if ($stmt->execute()) {

        $query = "INSERT INTO plan_history (plan_id, user_id, price, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iidi", $plan_id, $user_id, $price, $status);
        $stmt->execute();

        header("Location: ../manage-plan.php?Success=Plan price updated successfully");
    } else {
        header("Location: ../manage-plan.php?Failed=Failed to update plan price");
    }
    exit();
}
?>