<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display_errors to prevent HTML errors from mixing with JSON
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_error.log');

// Load session configuration first
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Permission.php';
require_once '../config/Database.php';

class UserController
{
    /** @var Database */
    private $db;
    
    /** @var User */
    private $user;
    
    /** @var Permission */
    private $permission;

    /**
     * UserController constructor
     */
    public function __construct()
    {
        try {
            $this->db = new Database();
            $this->user = new User($this->db);
            $this->permission = new Permission();
        } catch (Exception $e) {
            error_log('Constructor error: ' . $e->getMessage());
            $this->sendJsonResponse(false, 'Failed to initialize user controller: ' . $e->getMessage());
        }
    }

    private function checkAccess($userId, $requiredRole) {
        if (!$this->permission->hasAccess($userId, $requiredRole)) {
            $this->sendJsonResponse(false, 'Access denied. Insufficient privileges.');
            exit();
        }
    }

    private function sendJsonResponse($success, $message, $data = null)
    {
        try {
            // Clear any previous output
            if (ob_get_length()) ob_clean();
            
            // Set headers for JSON API
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            
            $response = [
                'success' => $success,
                'message' => $message
            ];
            
            if ($data !== null) {
                // Add role information to user data
                if (isset($data['user_id'])) {
                    $roleInfo = $this->permission->getUserRole($data['user_id']);
                    if ($roleInfo) {
                        $data['role_id'] = $roleInfo['role_id'];
                        $data['role'] = $roleInfo['role'];
                    }
                }
                $response['user'] = $data;
            }
            
            error_log('Sending response: ' . json_encode($response));
            echo json_encode($response);
            exit();
        } catch (Exception $e) {
            error_log('Error in sendJsonResponse: ' . $e->getMessage());
            // Last resort error response
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
            exit();
        }
    }

    private function login()
    {
        try {
            error_log('Starting login process...');
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
                $this->sendJsonResponse(false, 'Method not allowed');
                return;
            }

            // Get input
            $input = file_get_contents('php://input');
            error_log('Raw input: ' . $input);
            
            $data = json_decode($input, true);
            if (!$data) {
                error_log('JSON decode error: ' . json_last_error_msg());
                $this->sendJsonResponse(false, 'Invalid JSON data: ' . json_last_error_msg());
                return;
            }

            // Validate required fields
            if (empty($data['username']) || empty($data['password'])) {
                error_log('Missing required fields');
                $this->sendJsonResponse(false, 'Username and password are required');
                return;
            }

            // Attempt authentication
            error_log('Attempting authentication for user: ' . $data['username']);
            $result = $this->user->authenticate($data['username'], $data['password']);
            error_log('Authentication result: ' . print_r($result, true));

            if ($result['success']) {
                $user = $result['user'];
                error_log('User authenticated successfully: ' . print_r($user, true));
                
                // Get user's role
                $roleInfo = $this->permission->getUserRole($user['user_id']);
                if ($roleInfo) {
                    $user['role_id'] = $roleInfo['role_id'];
                    $user['role'] = $roleInfo['role'];
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $user
                ];

                error_log('Sending successful login response');
                echo json_encode($response);
                exit();
            } else {
                error_log('Authentication failed: ' . $result['message']);
                $this->sendJsonResponse(false, $result['message']);
            }
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->sendJsonResponse(false, 'Login failed: ' . $e->getMessage());
        }
    }

    private function getCurrentUser()
    {
        try {
            // Get token from Authorization header
            $headers = getallheaders();
            $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

            error_log('Received token: ' . $token);

            if (!$token) {
                error_log('No token provided');
                $this->sendJsonResponse(false, 'No token provided');
                return;
            }

            // Validate token
            $userId = $this->validateToken($token);
            if (!$userId) {
                error_log('Invalid token');
                $this->sendJsonResponse(false, 'Invalid token');
                return;
            }

            error_log('Token validated for user ID: ' . $userId);

            // Get user data
            $user = $this->user->getUserById($userId);
            if (!$user) {
                error_log('User not found for ID: ' . $userId);
                $this->sendJsonResponse(false, 'User not found');
                return;
            }

            // Get role info
            $roleInfo = $this->permission->getUserRole($user['user_id']);
            if ($roleInfo) {
                $user['role_id'] = $roleInfo['role_id'];
                $user['role'] = $roleInfo['role'];
            }

            error_log('Sending user data: ' . json_encode($user));
            $this->sendJsonResponse(true, 'User data retrieved successfully', $user);
        } catch (Exception $e) {
            error_log('Get current user error: ' . $e->getMessage());
            $this->sendJsonResponse(false, 'Error getting user data: ' . $e->getMessage());
        }
    }

    private function logout()
    {
        $this->sendJsonResponse(true, 'Logged out successfully');
    }

    private function generateToken($userId)
    {
        try {
            $key = 'your_secret_key_here'; // In production, use a secure key from environment variables
            $issuedAt = time();
            $expire = $issuedAt + 3600; // 1 hour expiration

            $token = [
                'user_id' => $userId,
                'iat' => $issuedAt,
                'exp' => $expire
            ];

            return base64_encode(json_encode($token));
        } catch (Exception $e) {
            error_log('Token generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function validateToken($token)
    {
        try {
            $decoded = json_decode(base64_decode($token), true);
            if (!$decoded) {
                return false;
            }

            if (!isset($decoded['exp']) || !isset($decoded['user_id'])) {
                return false;
            }

            if ($decoded['exp'] < time()) {
                return false;
            }

            return $decoded['user_id'];
        } catch (Exception $e) {
            error_log('Token validation error: ' . $e->getMessage());
            return false;
        }
    }

    public function handleRequest()
    {
        try {
            // Set headers for JSON API
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');

            // Handle preflight requests
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                http_response_code(200);
                exit();
            }

            $action = isset($_GET['action']) ? $_GET['action'] : '';
            error_log('Handling action: ' . $action);
            error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

            switch ($action) {
                case 'login':
                    $this->login();
                    break;
                case 'logout':
                    $this->logout();
                    break;
                case 'getCurrentUser':
                    $this->getCurrentUser();
                    break;
                case 'createMember':
                    // Check if user has permission to create members (Front Desk or higher)
                    if (isset($_SESSION['user_id'])) {
                        $this->checkAccess($_SESSION['user_id'], 3);
                    }
                    $this->createMember();
                    break;
                case 'updateMember':
                    // Check if user has permission to update members (Front Desk or higher)
                    if (isset($_SESSION['user_id'])) {
                        $this->checkAccess($_SESSION['user_id'], 3);
                    }
                    $this->updateMember();
                    break;
                case 'deleteMember':
                    // Check if user has permission to delete members (Admin or higher)
                    if (isset($_SESSION['user_id'])) {
                        $this->checkAccess($_SESSION['user_id'], 2);
                    }
                    $this->deleteMember();
                    break;
                case 'getMembers':
                    $this->handleGetMembers();
                    break;
                default:
                    $this->sendJsonResponse(false, 'Invalid action');
            }
        } catch (Exception $e) {
            error_log('HandleRequest error: ' . $e->getMessage());
            $this->sendJsonResponse(false, $e->getMessage());
        }
    }

    private function createMember()
    {
        try {
            // Get input data
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $requiredFields = [
                'firstname', 'lastname', 'email', 'username', 'password',
                'phone', 'dob', 'membership_type', 'emergency_contact', 'address'
            ];
            
            foreach ($requiredFields as $field) {
                if (empty($input[$field])) {
                    $this->sendJsonResponse(false, "Missing required field: {$field}");
                    return;
                }
            }
            
            // Validate email format
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $this->sendJsonResponse(false, "Invalid email format");
                return;
            }
            
            // Validate username length
            if (strlen($input['username']) < 4) {
                $this->sendJsonResponse(false, "Username must be at least 4 characters long");
                return;
            }
            
            // Validate password length
            if (strlen($input['password']) < 6) {
                $this->sendJsonResponse(false, "Password must be at least 6 characters long");
                return;
            }
            
            // Validate phone number format
            if (!preg_match("/^[0-9]{10}$/", $input['phone'])) {
                $this->sendJsonResponse(false, "Invalid phone number format");
                return;
            }
            
            // Validate date of birth
            $dob = strtotime($input['dob']);
            if (!$dob) {
                $this->sendJsonResponse(false, "Invalid date of birth");
                return;
            }
            
            // Validate membership type
            $validMembershipTypes = ['basic', 'premium', 'vip'];
            if (!in_array($input['membership_type'], $validMembershipTypes)) {
                $this->sendJsonResponse(false, "Invalid membership type");
                return;
            }
            
            // Create member with role_id 5 (Member)
            $memberData = [
                'username' => $input['username'],
                'password' => password_hash($input['password'], PASSWORD_DEFAULT),
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'dob' => date('Y-m-d', $dob),
                'membership_type' => $input['membership_type'],
                'emergency_contact' => $input['emergency_contact'],
                'address' => $input['address'],
                'role_id' => 5 // Member role
            ];
            
            $result = $this->user->createUser($memberData);
            
            if ($result['success']) {
                $this->sendJsonResponse(true, 'Member registered successfully', $result['user']);
            } else {
                $this->sendJsonResponse(false, $result['message']);
            }
            
        } catch (Exception $e) {
            error_log('Create member error: ' . $e->getMessage());
            $this->sendJsonResponse(false, 'Failed to register member: ' . $e->getMessage());
        }
    }

    private function updateMember() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['user_id'])) {
                $this->sendJsonResponse(false, 'User ID is required');
                return;
            }
            
            // Update the member
            $result = $this->user->updateUser($input['user_id'], $input);
            
            if ($result['success']) {
                $this->sendJsonResponse(true, 'Member updated successfully', $result['user']);
            } else {
                $this->sendJsonResponse(false, $result['message']);
            }
        } catch (Exception $e) {
            error_log('Update member error: ' . $e->getMessage());
            $this->sendJsonResponse(false, 'Failed to update member: ' . $e->getMessage());
        }
    }

    private function deleteMember() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['user_id'])) {
                $this->sendJsonResponse(false, 'User ID is required');
                return;
            }
            
            // Delete the member
            $result = $this->user->deleteUser($input['user_id']);
            
            if ($result['success']) {
                $this->sendJsonResponse(true, 'Member deleted successfully');
            } else {
                $this->sendJsonResponse(false, $result['message']);
            }
        } catch (Exception $e) {
            error_log('Delete member error: ' . $e->getMessage());
            $this->sendJsonResponse(false, 'Failed to delete member: ' . $e->getMessage());
        }
    }

    /**
     * Handle get members request
     * @return void
     */
    private function handleGetMembers() {
        try {
            // Check if user has permission to view members (Front Desk or higher)
            if (isset($_SESSION['user_id'])) {
                $this->checkAccess($_SESSION['user_id'], 3);
            }

            error_log("Starting to fetch members in handleGetMembers");
            
            if (!$this->user) {
                error_log("User model not initialized in handleGetMembers");
                throw new Exception("Internal server error");
            }

            $result = $this->user->getMembers();
            
            error_log("Successfully fetched members. Count: " . count($result));
            
            // Clear any previous output
            if (ob_get_length()) ob_clean();
            
            // Set proper headers
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
            
        } catch (Exception $e) {
            error_log("Error in handleGetMembers: " . $e->getMessage());
            
            // Clear any previous output
            if (ob_get_length()) ob_clean();
            
            // Set proper headers
            header('Content-Type: application/json; charset=utf-8');
            header('HTTP/1.1 500 Internal Server Error');
            
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch members: ' . $e->getMessage()
            ]);
        }
    }
}

// Create controller instance and handle request
try {
    $controller = new UserController();
    $controller->handleRequest();
} catch (Exception $e) {
    error_log('Fatal error: ' . $e->getMessage());
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
