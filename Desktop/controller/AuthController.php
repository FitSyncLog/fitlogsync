<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    public function login($email = null, $password = null) {
        try {
            // If parameters are not provided, get from POST data
            if ($email === null || $password === null) {
                $data = json_decode(file_get_contents("php://input"), true);
                $email = $data['email'] ?? null;
                $password = $data['password'] ?? null;
            }

            // Validate input
            if (!$email || !$password) {
                return [
                    'success' => false, 
                    'message' => 'Email and password are required'
                ];
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false, 
                    'message' => 'Invalid email format'
                ];
            }

            $userModel = new User();
            $user = $userModel->getUserByEmail($email);

            if (!$user) {
                return [
                    'success' => false, 
                    'message' => 'Invalid credentials'
                ];
            }

            // Check if user account is active
            if ($user['status'] !== 'Active') {
                $statusMessages = [
                    'Pending' => 'Your account is pending approval. Please contact the administrator.',
                    'Suspended' => 'Your account has been suspended. Please contact the administrator.',
                    'Banned' => 'Your account has been banned. Please contact the administrator.',
                    'Deleted' => 'Your account is no longer active. Please contact the administrator.'
                ];
                
                return [
                    'success' => false, 
                    'message' => $statusMessages[$user['status']] ?? 'Your account is not active.'
                ];
            }

            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false, 
                    'message' => 'Invalid credentials'
                ];
            }

            // Start session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Store user info in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'] ?? 5; // Default to Member if no role
            $_SESSION['role'] = $user['role'] ?? 'Member';
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];

            // Get appropriate dashboard based on role
            $dashboard = $userModel->getRoleDashboard($user['role_id'] ?? 5);

            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['user_id'],
                    'email' => $user['email'],
                    'username' => $user['username'],
                    'firstname' => $user['firstname'] ?? '',
                    'lastname' => $user['lastname'] ?? '',
                    'role_id' => $user['role_id'] ?? 5,
                    'role' => $user['role'] ?? 'Member'
                ],
                'redirect_url' => $dashboard
            ];

        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'An error occurred during login'
            ];
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    public function requireLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit();
        }
    }

    public function checkRole($requiredRoles = []) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $requiredRoles)) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit();
        }
    }

    public function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'],
            'username' => $_SESSION['username'],
            'firstname' => $_SESSION['firstname'] ?? '',
            'lastname' => $_SESSION['lastname'] ?? '',
            'role_id' => $_SESSION['role_id'],
            'role' => $_SESSION['role']
        ];
    }
}
?>