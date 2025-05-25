<?php
session_start();
include "db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $discount_id = $_POST['discount_id'];
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? 1 : 0;

    $query = "UPDATE discounts SET status = ? WHERE discount_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $status, $discount_id); // Corrected variable name from $price to $status

    if ($stmt->execute()) {
        header("Location: ../manage-discount.php?Success=Successfully updated the status");
    } else {
        header("Location: ../manage-discount.php?Failed=Failed to update the status");
    }
    exit();
}
?>
