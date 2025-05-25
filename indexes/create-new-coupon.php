<?php
session_start();
require "db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newCoupon'])) {

    $coupon_name = $_POST['coupon_name'] ?? '';
    $coupon_type = $_POST['coupon_type'] ?? '';
    $coupon_value = $_POST['coupon_value'] ?? '';
    $number_of_coupons = $_POST['number_of_coupons'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $created_by = $_SESSION['user_id'] ?? '';

    // Prepare data for redirection if needed
    $user_data = 'coupon_name=' . urlencode($coupon_name) .
        '&coupon_type=' . urlencode($coupon_type) .
        '&coupon_value=' . urlencode($coupon_value) .
        '&number_of_coupons=' . urlencode($number_of_coupons) .
        '&start_date=' . urlencode($start_date) .
        '&end_date=' . urlencode($end_date);

    // Validate dates
    $today = new DateTime();
    $start = DateTime::createFromFormat('Y-m-d', $start_date);
    $end = DateTime::createFromFormat('Y-m-d', $end_date);

    if (!$start || $start < $today->setTime(0, 0)) {
        header("Location: ../create-new-coupon.php?Failed=Please enter a valid start date&" . $user_data);
        exit();
    }

    if (!$end || $end < $start) {
        header("Location: ../create-new-coupon.php?Failed=Please enter a valid end date&" . $user_data);
        exit();
    }

    // Check if coupon name exists
    $stmt = $conn->prepare("SELECT coupon_name FROM coupons WHERE coupon_name = ?");
    $stmt->bind_param("s", $coupon_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: ../create-new-coupon.php?Failed=Coupon Name is already existing&" . $user_data);
        exit();
    }

    // Insert into coupons table
    $stmt = $conn->prepare("INSERT INTO coupons (coupon_name, coupon_type, coupon_value, number_of_coupons, start_date, end_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssi", $coupon_name, $coupon_type, $coupon_value, $number_of_coupons, $start_date, $end_date, $created_by);

    if ($stmt->execute()) {
        $coupon_id = $stmt->insert_id;

        // Prepare coupon code prefix (remove spaces and special characters)
        $prefix = preg_replace("/[^A-Za-z0-9]/", "", $coupon_name);

        // Prepare insert statement for coupon_codes table
        $insertCodeStmt = $conn->prepare("INSERT INTO coupon_codes (coupon_id, coupon_code, status) VALUES (?, ?, 'Unused')");

        for ($i = 1; $i <= $number_of_coupons; $i++) {
            $code_number = str_pad($i, 4, '0', STR_PAD_LEFT);
            $coupon_code = strtoupper($prefix) . '-' . $code_number;

            $insertCodeStmt->bind_param("is", $coupon_id, $coupon_code);
            $insertCodeStmt->execute();
        }

        header("Location: ../manage-coupons.php?Success=The coupon code created successfully");
        exit();
    } else {
        header("Location: ../create-new-coupon.php?Failed=Error saving coupon data&" . $user_data);
        exit();
    }
}
?>