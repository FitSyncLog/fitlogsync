<?php
require "db_con.php";

header('Content-Type: application/json');

if (isset($_POST['coupon_code'])) {
    $coupon_code = $_POST['coupon_code'];
    
    // Query to validate coupon
    $query = "SELECT c.*, cc.coupon_code, cc.status as code_status 
              FROM coupons c 
              INNER JOIN coupon_codes cc ON c.coupon_id = cc.coupon_id 
              WHERE cc.coupon_code = ? 
              AND c.status = 'Active'
              AND cc.status = 'Unused'
              AND CURDATE() BETWEEN c.start_date AND c.end_date";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $coupon_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $coupon = $result->fetch_assoc();
        echo json_encode([
            'valid' => true,
            'message' => 'Valid coupon code for event: ' . $coupon['coupon_name'],
            'coupon_name' => $coupon['coupon_name'],
            'coupon_type' => $coupon['coupon_type'],
            'coupon_value' => $coupon['coupon_value'],
            'coupon_id' => $coupon['coupon_id']
        ]);
    } else {
        // Check if coupon exists but is invalid
        $query = "SELECT c.*, cc.coupon_code, cc.status as code_status 
                  FROM coupons c 
                  INNER JOIN coupon_codes cc ON c.coupon_id = cc.coupon_id 
                  WHERE cc.coupon_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $coupon_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $coupon = $result->fetch_assoc();
            if ($coupon['code_status'] === 'Used') {
                echo json_encode(['valid' => false, 'message' => 'This coupon code has already been used.']);
            } else if ($coupon['status'] !== 'Active') {
                echo json_encode(['valid' => false, 'message' => 'This coupon is no longer active.']);
            } else if (strtotime($coupon['start_date']) > time()) {
                echo json_encode(['valid' => false, 'message' => 'This coupon is not yet valid.']);
            } else if (strtotime($coupon['end_date']) < time()) {
                echo json_encode(['valid' => false, 'message' => 'This coupon has expired.']);
            } else {
                echo json_encode(['valid' => false, 'message' => 'Invalid coupon code.']);
            }
        } else {
            echo json_encode(['valid' => false, 'message' => 'Invalid coupon code.']);
        }
    }
} else {
    echo json_encode(['valid' => false, 'message' => 'No coupon code provided.']);
} 