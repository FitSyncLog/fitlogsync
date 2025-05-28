<?php
header('Content-Type: application/json');
require "db_con.php";

if (isset($_POST['user_id']) && isset($_POST['selected_date'])) {
    $user_id = $_POST['user_id'];
    $selected_date = $_POST['selected_date'];

    // Get the plan duration to check the full period
    $plan_id = isset($_POST['plan_id']) ? $_POST['plan_id'] : null;
    $duration = 1; // Default to 1 month if no plan_id provided

    if ($plan_id) {
        $plan_query = "SELECT duration FROM plans WHERE plan_id = ?";
        $plan_stmt = $conn->prepare($plan_query);
        $plan_stmt->bind_param("i", $plan_id);
        $plan_stmt->execute();
        $plan_result = $plan_stmt->get_result();
        if ($plan_result->num_rows > 0) {
            $plan = $plan_result->fetch_assoc();
            $duration = $plan['duration'];
        }
    }

    // Calculate the end date for the new subscription
    $end_date = date('Y-m-d', strtotime($selected_date . ' + ' . $duration . ' months - 1 day'));

    // Check for active subscriptions that overlap with the selected period
    $query = "SELECT * FROM subscriptions 
              WHERE user_id = ? 
              AND (
                  (starting_date <= ? AND expiration_date >= ?) OR  /* New subscription starts during existing one */
                  (starting_date <= ? AND expiration_date >= ?) OR  /* New subscription ends during existing one */
                  (starting_date >= ? AND expiration_date <= ?)     /* Existing subscription is within new one */
              )";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssss", 
        $user_id, 
        $selected_date, $selected_date,      /* For first condition */
        $end_date, $end_date,                /* For second condition */
        $selected_date, $end_date            /* For third condition */
    );
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $subscription = $result->fetch_assoc();
            echo json_encode([
                'status' => 'error',
                'message' => "This member already has an active subscription for the selected period.\nActive subscription period: " . 
                           date('F d, Y', strtotime($subscription['starting_date'])) . " to " . 
                           date('F d, Y', strtotime($subscription['expiration_date']))
            ]);
        } else {
            echo json_encode(['status' => 'success']);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error occurred while checking subscription'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters'
    ]);
}
?> 