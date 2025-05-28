<?php
require_once __DIR__ . '/../config/Database.php';

class User
{
    private $conn;
    private $table = 'users';

    /**
     * User constructor
     * @param Database|null $db Database connection
     */
    public function __construct($db = null)
    {
        try {
            if ($db) {
                $this->conn = $db->getConnection();
            } else {
                $database = new Database();
                $this->conn = $database->getConnection();
            }

            if (!$this->conn) {
                error_log("Failed to establish database connection in User constructor");
                throw new Exception("Database connection failed");
            }
            
            error_log("Database connection established successfully in User constructor");
        } catch (Exception $e) {
            error_log("User Model Constructor Error: " . $e->getMessage());
            throw new Exception("Failed to initialize User model: " . $e->getMessage());
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, r.role, ur.role_id 
                FROM users u
                LEFT JOIN user_roles ur ON u.user_id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.role_id
                WHERE u.email = :email
            ");
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getUserByEmail: " . $e->getMessage());
            return false;
        }
    }

    public function getUserWithRole($userId)
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT u.*, r.role, ur.role_id 
            FROM users u
            LEFT JOIN user_roles ur ON u.user_id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.role_id
            WHERE u.user_id = :user_id
        ");
            $stmt->execute(['user_id' => $userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userData) {
                error_log("No user found for user_id: $userId");
                return false;
            }

            // Ensure role_id and role are set, default to Member (role_id: 5) if not found
            if (!isset($userData['role_id']) || !isset($userData['role'])) {
                $userData['role_id'] = 5; // Default to Member
                $userData['role'] = 'Member';
            }

            return $userData;
        } catch (PDOException $e) {
            error_log("Database error in getUserWithRole: " . $e->getMessage());
            return false;
        }
    }

    public function findById($userId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in findById: " . $e->getMessage());
            return false;
        }
    }

    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($userId, $data)
    {
        try {
            $updateFields = [];
            $params = [];

            foreach (['username', 'email', 'firstname', 'lastname'] as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }

            if (empty($updateFields)) {
                return false;
            }

            $params[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE user_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            throw new Exception("Failed to update user");
        }
    }

    public function updatePassword($userId, $newPassword)
    {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            return $stmt->execute([$hashedPassword, $userId]);
        } catch (PDOException $e) {
            error_log("Update password error: " . $e->getMessage());
            throw new Exception("Failed to update password");
        }
    }

    public function verifyPassword($plainPassword, $hashedPassword)
    {
        return password_verify($plainPassword, $hashedPassword);
    }

    /**
     * Get the unified dashboard for all roles
     * Since you're using a single dashboard that adapts based on user role,
     * all users go to the same dashboard file
     */
    public function getDashboard($roleId = null)
    {
        // Return the unified dashboard for all roles
        // The JavaScript in the dashboard handles role-based access control
        return 'dashboard.html';
    }

    /**
     * Alternative method name for backward compatibility
     */
    public function getRoleDashboard($roleId)
    {
        return $this->getDashboard($roleId);
    }

    /**
     * Get role permissions for frontend use
     * Returns what sections/features the user can access
     */
    public function getRolePermissions($roleId)
    {
        $permissions = [
            1 => [ // Super Admin - full access
                'member_management' => true,
                'class_management' => true,
                'financial_reports' => true,
                'system_admin' => true,
                'profile' => true,
                'settings' => true
            ],
            2 => [ // Admin - no system admin
                'member_management' => true,
                'class_management' => true,
                'financial_reports' => true,
                'system_admin' => false,
                'profile' => true,
                'settings' => true
            ],
            3 => [ // Front Desk - member management only
                'member_management' => true,
                'class_management' => false,
                'financial_reports' => false,
                'system_admin' => false,
                'profile' => true,
                'settings' => true
            ],
            4 => [ // Instructor - class management only
                'member_management' => false,
                'class_management' => true,
                'financial_reports' => false,
                'system_admin' => false,
                'profile' => true,
                'settings' => true
            ],
            5 => [ // Member - basic access only
                'member_management' => false,
                'class_management' => false,
                'financial_reports' => false,
                'system_admin' => false,
                'profile' => true,
                'settings' => true
            ]
        ];

        return $permissions[$roleId] ?? $permissions[5]; // Default to Member permissions
    }

    /**
     * Check if user has permission for a specific feature
     */
    public function hasPermission($roleId, $feature)
    {
        $permissions = $this->getRolePermissions($roleId);
        return $permissions[$feature] ?? false;
    }

    /**
     * Get role hierarchy level (lower number = higher privilege)
     */
    public function getRoleLevel($roleId)
    {
        return intval($roleId);
    }

    /**
     * Check if user can access a feature based on required role level
     */
    public function canAccess($userRoleId, $requiredRoleId)
    {
        // Lower role_id numbers have higher privileges
        return intval($userRoleId) <= intval($requiredRoleId);
    }

    public function authenticate($username, $password) {
        try {
            $query = "SELECT u.*, ur.role_id 
                     FROM users u 
                     JOIN user_roles ur ON u.user_id = ur.user_id 
                     WHERE u.username = :username";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Remove sensitive data
                unset($user['password']);
                unset($user['otp_code']);
                unset($user['otp_code_expiration']);
                
                return [
                    'success' => true,
                    'user' => $user
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Invalid username or password'
            ];
        } catch (PDOException $e) {
            error_log("Authentication error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Authentication failed'
            ];
        }
    }

    public function getUserPermissions($roleId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT page_name, permission 
                FROM permissions 
                WHERE role_id = :role_id
            ");
            $stmt->execute(['role_id' => $roleId]);
            
            $permissions = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $permissions[$row['page_name']] = (bool)$row['permission'];
            }
            
            return $permissions;
        } catch (Exception $e) {
            error_log("Error getting permissions: " . $e->getMessage());
            return [];
        }
    }

    public function getUserById($userId) {
        try {
            $query = "SELECT u.*, ur.role_id 
                     FROM users u 
                     JOIN user_roles ur ON u.user_id = ur.user_id 
                     WHERE u.user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Remove sensitive data
                unset($user['password']);
                unset($user['otp_code']);
                unset($user['otp_code_expiration']);
            }
            
            return $user;
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return null;
        }
    }

    public function createUser($data) {
        try {
            $this->conn->beginTransaction();

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert into users table
            $query = "INSERT INTO users (username, firstname, lastname, email, password, status, registration_date) 
                     VALUES (:username, :firstname, :lastname, :email, :password, 'Pending', CURDATE())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':firstname', $data['firstname']);
            $stmt->bindParam(':lastname', $data['lastname']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();
            
            $userId = $this->conn->lastInsertId();
            
            // Insert into user_roles table
            $roleQuery = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
            $roleStmt = $this->conn->prepare($roleQuery);
            $roleStmt->bindParam(':user_id', $userId);
            $roleStmt->bindParam(':role_id', $data['role_id']);
            $roleStmt->execute();
            
            $this->conn->commit();
            
            // Get the created user
            $user = $this->getUserById($userId);
            
            return [
                'success' => true,
                'user' => $user
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Create user error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create user'
            ];
        }
    }

    public function updateUser($userId, $data) {
        try {
            $this->conn->beginTransaction();
            
            $updateFields = [];
            $params = [':user_id' => $userId];
            
            // Build update query dynamically
            if (!empty($data['username'])) {
                $updateFields[] = "username = :username";
                $params[':username'] = $data['username'];
            }
            if (!empty($data['firstname'])) {
                $updateFields[] = "firstname = :firstname";
                $params[':firstname'] = $data['firstname'];
            }
            if (!empty($data['lastname'])) {
                $updateFields[] = "lastname = :lastname";
                $params[':lastname'] = $data['lastname'];
            }
            if (!empty($data['email'])) {
                $updateFields[] = "email = :email";
                $params[':email'] = $data['email'];
            }
            if (!empty($data['password'])) {
                $updateFields[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            if (!empty($data['status'])) {
                $updateFields[] = "status = :status";
                $params[':status'] = $data['status'];
            }
            
            if (!empty($updateFields)) {
                $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE user_id = :user_id";
                $stmt = $this->conn->prepare($query);
                $stmt->execute($params);
            }
            
            // Update role if provided
            if (!empty($data['role_id'])) {
                $roleQuery = "UPDATE user_roles SET role_id = :role_id WHERE user_id = :user_id";
                $roleStmt = $this->conn->prepare($roleQuery);
                $roleStmt->bindParam(':user_id', $userId);
                $roleStmt->bindParam(':role_id', $data['role_id']);
                $roleStmt->execute();
            }
            
            $this->conn->commit();
            
            // Get the updated user
            $user = $this->getUserById($userId);
            
            return [
                'success' => true,
                'user' => $user
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Update user error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update user'
            ];
        }
    }

    public function deleteUser($userId) {
        try {
            $this->conn->beginTransaction();
            
            // Delete from user_roles first (due to foreign key constraint)
            $roleQuery = "DELETE FROM user_roles WHERE user_id = :user_id";
            $roleStmt = $this->conn->prepare($roleQuery);
            $roleStmt->bindParam(':user_id', $userId);
            $roleStmt->execute();
            
            // Delete from users table
            $query = "DELETE FROM users WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'User deleted successfully'
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Delete user error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete user'
            ];
        }
    }

    /**
     * Get all members from the database
     * @return array Array of members
     * @throws Exception if there's an error retrieving members
     */
    public function getMembers() {
        try {
            if (!$this->conn) {
                error_log("Database connection is not established in getMembers");
                throw new Exception("Database connection error");
            }

            $query = "SELECT 
                        u.user_id,
                        u.account_number,
                        u.username,
                        u.firstname,
                        u.lastname,
                        u.email,
                        u.phone_number,
                        u.status,
                        u.registration_date,
                        r.role
                    FROM users u
                    LEFT JOIN user_roles ur ON u.user_id = ur.user_id
                    LEFT JOIN roles r ON ur.role_id = r.role_id
                    WHERE ur.role_id >= 3
                    ORDER BY u.registration_date DESC";
            
            error_log("Executing query in getMembers: " . $query);
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                error_log("Failed to prepare statement in getMembers: " . print_r($this->conn->errorInfo(), true));
                throw new Exception("Failed to prepare statement");
            }

            $stmt->execute();
            if ($stmt->errorCode() !== '00000') {
                error_log("Error executing statement in getMembers: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Error executing statement");
            }
            
            $members = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $member = [
                    'id' => $row['user_id'],
                    'accountNumber' => $row['account_number'],
                    'name' => $row['firstname'] . ' ' . $row['lastname'],
                    'email' => $row['email'],
                    'phone' => $row['phone_number'],
                    'status' => $row['status'] ?: 'Pending',
                    'joinDate' => date('Y-m-d', strtotime($row['registration_date'])),
                    'role' => $row['role']
                ];
                $members[] = $member;
            }
            
            error_log("Successfully retrieved " . count($members) . " members");
            return $members;
            
        } catch(PDOException $e) {
            error_log("PDO Error in getMembers: " . $e->getMessage());
            throw new Exception("Database error while retrieving members: " . $e->getMessage());
        } catch(Exception $e) {
            error_log("General Error in getMembers: " . $e->getMessage());
            throw $e;
        }
    }
}
