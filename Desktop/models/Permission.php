<?php
require_once __DIR__ . '/../config/Database.php';

class Permission {
    private $conn;
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function checkPermission($userId, $pageName) {
        try {
            // Get user's role
            $roleQuery = "SELECT r.role_id, r.role 
                         FROM roles r 
                         JOIN user_roles ur ON r.role_id = ur.role_id 
                         WHERE ur.user_id = :user_id";
            
            $roleStmt = $this->conn->prepare($roleQuery);
            $roleStmt->bindParam(':user_id', $userId);
            $roleStmt->execute();
            
            $userRole = $roleStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$userRole) {
                return false;
            }

            // Check permission for the page
            $permQuery = "SELECT permission 
                         FROM permissions 
                         WHERE role_id = :role_id 
                         AND page_name = :page_name";
            
            $permStmt = $this->conn->prepare($permQuery);
            $permStmt->bindParam(':role_id', $userRole['role_id']);
            $permStmt->bindParam(':page_name', $pageName);
            $permStmt->execute();
            
            $permission = $permStmt->fetch(PDO::FETCH_ASSOC);
            
            return $permission && $permission['permission'] == 1;
        } catch (PDOException $e) {
            error_log("Permission check error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserRole($userId) {
        try {
            $query = "SELECT r.role_id, r.role 
                      FROM roles r 
                      JOIN user_roles ur ON r.role_id = ur.role_id 
                      WHERE ur.user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user role error: " . $e->getMessage());
            return null;
        }
    }

    public function hasAccess($userId, $requiredRole) {
        try {
            $userRole = $this->getUserRole($userId);
            if (!$userRole) {
                return false;
            }
            
            // Lower role_id means higher privilege
            return $userRole['role_id'] <= $requiredRole;
        } catch (PDOException $e) {
            error_log("Access check error: " . $e->getMessage());
            return false;
        }
    }
} 