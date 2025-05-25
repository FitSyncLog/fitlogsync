<?php
session_start();
require "db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editCoupon'])) {

    $coupon_id = $_POST['coupon_id'];
    $coupon_name = $_POST['coupon_name'] ?? '';
    $coupon_type = $_POST['coupon_type'] ?? '';
    $coupon_value = $_POST['coupon_value'] ?? '';
    $number_of_coupons = $_POST['number_of_coupons'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    // Validate dates
    $today = new DateTime();
    $start = DateTime::createFromFormat('Y-m-d', $start_date);
    $end = DateTime::createFromFormat('Y-m-d', $end_date);

    if (!$start || $start < $today->setTime(0, 0)) {
        header("Location: ../edit-coupon.php?coupon_id=$coupon_id&Failed=Please enter a valid start date");
        exit();
    }

    if (!$end || $end < $start) {
        header("Location: ../edit-coupon.php?coupon_id=$coupon_id&Failed=Please enter a valid end date");
        exit();
    }

    // Update main coupon info
    $sql = "UPDATE coupons SET coupon_type = ?, coupon_value = ?, number_of_coupons = ?, start_date = ?, end_date = ? WHERE coupon_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sdissi", $coupon_type, $coupon_value, $number_of_coupons, $start_date, $end_date, $coupon_id);
    $update_success = mysqli_stmt_execute($stmt);

    if (!$update_success) {
        header("Location: ../manage-coupons.php?Failed=Failed to update coupon");
        exit();
    }

    // Delete old coupon codes
    $stmt = $conn->prepare("DELETE FROM coupon_codes WHERE coupon_id = ?");
    $stmt->bind_param("i", $coupon_id);
    $delete_success = $stmt->execute();

    if ($delete_success) {
        // Generate prefix - ensure it's not empty
        $prefix = preg_replace("/[^A-Za-z0-9]/", "", $coupon_name);
        
        // If prefix is empty after sanitization, use a default
        if (empty($prefix)) {
            $prefix = 'CPN'; // Default prefix if coupon name results in empty string
        }
        
        // Truncate prefix if too long (let's say max 8 chars)
        $prefix = substr($prefix, 0, 8);

        $insertCodeStmt = $conn->prepare("INSERT INTO coupon_codes (coupon_id, coupon_code, status) VALUES (?, ?, 'Unused')");

        for ($i = 1; $i <= $number_of_coupons; $i++) {
            $code_number = str_pad($i, 4, '0', STR_PAD_LEFT);
            $coupon_code = strtoupper($prefix) . '-' . $code_number; // Added strtoupper for consistency

            $insertCodeStmt->bind_param("is", $coupon_id, $coupon_code);
            if (!$insertCodeStmt->execute()) {
                // Log error or handle failed insert
                error_log("Failed to insert coupon code: " . $coupon_code);
            }
        }

        header("Location: ../manage-coupons.php?Success=The coupon code created successfully");
        exit();
    } else {
        header("Location: ../edit-coupon.php?coupon_id=$coupon_id&Failed=Error saving coupon data");
        exit();
    }

} else {
    header("Location: ../manage-coupons.php?Failed=Unexpected error");
    exit();
}