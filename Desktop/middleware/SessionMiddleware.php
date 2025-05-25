<?php
// middleware/SessionMiddleware.php
class SessionMiddleware {
    
    public static function requireLogin($redirectUrl = 'login.html') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            // If it's an AJAX request, return JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Session expired', 'redirect' => $redirectUrl]);
                exit();
            }
            
            // Regular request, redirect to login
            header("Location: $redirectUrl");
            exit();
        }
    }
    
    public static function requireRole($allowedRoles = [], $redirectUrl = 'unauthorized.html') {
        self::requireLogin();
        
        if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowedRoles)) {
            // If it's an AJAX request, return JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied', 'redirect' => $redirectUrl]);
                exit();
            }
            
            // Regular request, redirect to unauthorized page
            header("Location: $redirectUrl");
            exit();
        }
    }
    
    public static function getCurrentUser() {
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
    
    public static function checkUserStatus($userId) {
        require_once __DIR__ . '/../models/User.php';
        
        $userModel = new User();
        $user = $userModel->findById($userId);
        
        if (!$user || $user['status'] !== 'Active') {
            self::logout();
            return false;
        }
        
        return true;
    }
    
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        
        // Clear session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }
}