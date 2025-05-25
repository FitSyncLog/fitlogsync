<?php
session_start();
require "db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['coupon_id'])) {
        $coupon_id = $_POST['coupon_id'];

        // First, delete from the child table (coupon_codes) if there's a foreign key constraint
        $sql_delete_coupon_code = "DELETE FROM coupon_codes WHERE coupon_id = ?";
        $stmt_delete_coupon_code = mysqli_prepare($conn, $sql_delete_coupon_code);
        mysqli_stmt_bind_param($stmt_delete_coupon_code, "i", $coupon_id);
        $result_delete_coupon_code = mysqli_stmt_execute($stmt_delete_coupon_code);

        // Then, delete from the parent table (coupons)
        $sql_delete_coupon_id = "DELETE FROM coupons WHERE coupon_id = ?";
        $stmt_delete_coupon_id = mysqli_prepare($conn, $sql_delete_coupon_id);
        mysqli_stmt_bind_param($stmt_delete_coupon_id, "i", $coupon_id);
        $result_delete_coupon_id = mysqli_stmt_execute($stmt_delete_coupon_id);

        // Check both deletions
        if ($result_delete_coupon_code && $result_delete_coupon_id) {
            header("Location: ../manage-coupons.php?Success=Coupon deleted successfully");
            exit();
        } else {
            header("Location: ../manage-coupons.php?Failed=Failed to delete the coupon");
            exit();
        }

    } else {
        header("Location: ../manage-coupons.php?Failed=Coupon ID not found");
        exit();
    }
} else {
    header("Location: ../manage-coupons.php?Failed=Unexpected error");
    exit();
}
?>