<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers first
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../controller/AuthController.php';

try {
    $authController = new AuthController();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Check if JSON decode was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid JSON data'
            ]);
            exit();
        }
        
        // Validate required fields
        if (empty($input['email']) || empty($input['password'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Email and password are required'
            ]);
            exit();
        }
        
        // Call login method
        $result = $authController->login($input['email'], $input['password']);
        echo json_encode($result);
        
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false, 
            'message' => 'Method not allowed'
        ]);
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Server error occurred'
    ]);
}
?>