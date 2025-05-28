<?php
session_start();
include "indexes/db_con.php";

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Check if user is logged in and has appropriate role
if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 2, 3])) {
    // Get the last 6 months
    $labels = [];
    $values = [];
    
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $month_start = $month . '-01';
        $month_end = date('Y-m-t', strtotime($month_start));
        
        // Add month label
        $labels[] = date('M Y', strtotime($month_start));
        
        // Get new members count for this month
        $sql = "SELECT COUNT(*) as new_members 
                FROM users u 
                JOIN user_roles ur ON u.user_id = ur.user_id 
                WHERE ur.role_id = 5 
                AND DATE(u.registration_date) BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $month_start, $month_end);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $values[] = intval($row['new_members']);
    }
    
    echo json_encode([
        'labels' => $labels,
        'values' => $values
    ]);
} else {
    echo json_encode(['error' => 'Unauthorized access']);
}
?> 