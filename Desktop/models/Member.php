<?php
// models/Member.php

class Member {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        try {
            $this->conn->beginTransaction();

            // Generate account number
            $accountNumber = $this->generateAccountNumber();

            // Insert into users table
            $userSql = "INSERT INTO users (
                account_number, username, firstname, middlename, lastname, date_of_birth, 
                password, gender, phone_number, email, address, otp_code, otp_code_expiration,
                two_factor_authentication, enrolled_by, status, registration_date, profile_image
            ) VALUES (
                :account_number, :username, :firstname, :middlename, :lastname, :date_of_birth,
                :password, :gender, :phone_number, :email, :address, :otp_code, :otp_code_expiration,
                :two_factor_authentication, :enrolled_by, :status, :registration_date, :profile_image
            )";

            $userStmt = $this->conn->prepare($userSql);
            
            // Bind user parameters with proper null handling
            $userStmt->bindParam(':account_number', $accountNumber);
            $userStmt->bindParam(':username', $data['username']);
            $userStmt->bindParam(':firstname', $data['firstname']);
            
            $middlename = isset($data['middlename']) ? $data['middlename'] : '';
            $userStmt->bindParam(':middlename', $middlename);
            
            $userStmt->bindParam(':lastname', $data['lastname']);
            $userStmt->bindParam(':date_of_birth', $data['date_of_birth']);
            $userStmt->bindParam(':password', $data['password']);
            $userStmt->bindParam(':gender', $data['gender']);
            $userStmt->bindParam(':phone_number', $data['phone_number']);
            $userStmt->bindParam(':email', $data['email']);
            $userStmt->bindParam(':address', $data['address']);
            
            // Generate OTP code and expiration
            $otpCode = sprintf('%06d', mt_rand(100000, 999999)); // Generate proper 6-digit OTP
            $otpExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $twoFactor = 1;
            $enrolledBy = 'Online Registration';
            $status = 'Pending';
            $registrationDate = date('Y-m-d');
            $profileImage = 'default.png';
            
            $userStmt->bindParam(':otp_code', $otpCode);
            $userStmt->bindParam(':otp_code_expiration', $otpExpiration);
            $userStmt->bindParam(':two_factor_authentication', $twoFactor);
            $userStmt->bindParam(':enrolled_by', $enrolledBy);
            $userStmt->bindParam(':status', $status);
            $userStmt->bindParam(':registration_date', $registrationDate);
            $userStmt->bindParam(':profile_image', $profileImage);

            if (!$userStmt->execute()) {
                throw new Exception('Failed to insert user: ' . implode(', ', $userStmt->errorInfo()));
            }

            $userId = $this->conn->lastInsertId();

            // Insert into emergency_contacts table
            $emergencySql = "INSERT INTO emergency_contacts (user_id, contact_person, contact_number, relationship) 
                           VALUES (:user_id, :contact_person, :contact_number, :relationship)";
            
            $emergencyStmt = $this->conn->prepare($emergencySql);
            $emergencyStmt->bindParam(':user_id', $userId);
            $emergencyStmt->bindParam(':contact_person', $data['contact_person']);
            $emergencyStmt->bindParam(':contact_number', $data['contact_number']);
            $emergencyStmt->bindParam(':relationship', $data['relationship']);
            
            if (!$emergencyStmt->execute()) {
                throw new Exception('Failed to insert emergency contact: ' . implode(', ', $emergencyStmt->errorInfo()));
            }

            // Insert into medical_backgrounds table
            $medicalSql = "INSERT INTO medical_backgrounds (
                user_id, medical_conditions, current_medications, previous_injuries,
                par_q_1, par_q_2, par_q_3, par_q_4, par_q_5, par_q_6, par_q_7, par_q_8, par_q_9, par_q_10
            ) VALUES (
                :user_id, :medical_conditions, :current_medications, :previous_injuries,
                :par_q_1, :par_q_2, :par_q_3, :par_q_4, :par_q_5, :par_q_6, :par_q_7, :par_q_8, :par_q_9, :par_q_10
            )";
            
            $medicalStmt = $this->conn->prepare($medicalSql);
            $medicalStmt->bindParam(':user_id', $userId);
            
            // Handle optional medical fields
            $medicalConditions = isset($data['medical_conditions']) && !empty($data['medical_conditions']) ? $data['medical_conditions'] : 'None';
            $currentMedications = isset($data['current_medications']) && !empty($data['current_medications']) ? $data['current_medications'] : 'None';
            $previousInjuries = isset($data['previous_injuries']) && !empty($data['previous_injuries']) ? $data['previous_injuries'] : 'None';
            
            $medicalStmt->bindParam(':medical_conditions', $medicalConditions);
            $medicalStmt->bindParam(':current_medications', $currentMedications);
            $medicalStmt->bindParam(':previous_injuries', $previousInjuries);
            
            // FIXED: Bind PAR-Q questions using the correct keys from data array
            for ($i = 1; $i <= 10; $i++) {
                $questionKey = "q{$i}"; // This matches what's in the data array
                $paramName = ":par_q_{$i}"; // This matches the SQL parameter
                
                // Make sure the question exists in the data array
                if (isset($data[$questionKey])) {
                    $medicalStmt->bindParam($paramName, $data[$questionKey]);
                } else {
                    // Provide a default value if missing
                    $defaultValue = 'No';
                    $medicalStmt->bindParam($paramName, $defaultValue);
                }
            }
            
            if (!$medicalStmt->execute()) {
                throw new Exception('Failed to insert medical background: ' . implode(', ', $medicalStmt->errorInfo()));
            }

            // Insert into security_questions table
            $securitySql = "INSERT INTO security_questions (
                user_id, sq1, sq1_res, sq2, sq2_res, sq3, sq3_res
            ) VALUES (
                :user_id, :sq1, :sq1_res, :sq2, :sq2_res, :sq3, :sq3_res
            )";
            
            $securityStmt = $this->conn->prepare($securitySql);
            $securityStmt->bindParam(':user_id', $userId);
            $securityStmt->bindParam(':sq1', $data['security_question1']);
            $securityStmt->bindParam(':sq1_res', $data['security_answer1']);
            $securityStmt->bindParam(':sq2', $data['security_question2']);
            $securityStmt->bindParam(':sq2_res', $data['security_answer2']);
            $securityStmt->bindParam(':sq3', $data['security_question3']);
            $securityStmt->bindParam(':sq3_res', $data['security_answer3']);
            
            if (!$securityStmt->execute()) {
                throw new Exception('Failed to insert security questions: ' . implode(', ', $securityStmt->errorInfo()));
            }

            // Insert into waivers table - fix the boolean conversion
            $waiverSql = "INSERT INTO waivers (user_id, rules_and_policy, liability_waiver, cancellation_and_refund_policy) 
                         VALUES (:user_id, :rules_and_policy, :liability_waiver, :cancellation_and_refund_policy)";
            
            $waiverStmt = $this->conn->prepare($waiverSql);
            $waiverStmt->bindParam(':user_id', $userId);
            $waiverStmt->bindParam(':rules_and_policy', $data['waiver_rules']);
            $waiverStmt->bindParam(':liability_waiver', $data['waiver_liability']);
            $waiverStmt->bindParam(':cancellation_and_refund_policy', $data['waiver_cancel']);
            
            if (!$waiverStmt->execute()) {
                throw new Exception('Failed to insert waivers: ' . implode(', ', $waiverStmt->errorInfo()));
            }

            // Insert default role (Member = role_id 5)
            $roleSql = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, 5)";
            $roleStmt = $this->conn->prepare($roleSql);
            $roleStmt->bindParam(':user_id', $userId);
            
            if (!$roleStmt->execute()) {
                throw new Exception('Failed to assign user role: ' . implode(', ', $roleStmt->errorInfo()));
            }

            $this->conn->commit();
            return $userId;

        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Database error in Member::create: " . $e->getMessage());
            throw $e; // Re-throw to get more specific error messages
        }
    }

    public function getAll() {
        try {
            $sql = "SELECT 
                u.user_id, u.account_number, u.username, u.firstname, u.middlename, u.lastname, 
                u.date_of_birth, u.gender, u.phone_number, u.email, u.address, u.status, 
                u.registration_date, u.profile_image,
                ec.contact_person, ec.contact_number, ec.relationship,
                mb.medical_conditions, mb.current_medications, mb.previous_injuries
            FROM users u
            LEFT JOIN emergency_contacts ec ON u.user_id = ec.user_id
            LEFT JOIN medical_backgrounds mb ON u.user_id = mb.user_id
            LEFT JOIN user_roles ur ON u.user_id = ur.user_id
            WHERE ur.role_id = 5
            ORDER BY u.registration_date DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Member::getAll: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT 
                u.*, ec.*, mb.*, sq.sq1, sq.sq2, sq.sq3, w.*
            FROM users u
            LEFT JOIN emergency_contacts ec ON u.user_id = ec.user_id
            LEFT JOIN medical_backgrounds mb ON u.user_id = mb.user_id
            LEFT JOIN security_questions sq ON u.user_id = sq.user_id
            LEFT JOIN waivers w ON u.user_id = w.user_id
            WHERE u.user_id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Member::getById: " . $e->getMessage());
            return false;
        }
    }

    public function getByUsername($username) {
        try {
            $sql = "SELECT u.*, sq.sq1_res, sq.sq2_res, sq.sq3_res 
                   FROM users u 
                   LEFT JOIN security_questions sq ON u.user_id = sq.user_id 
                   WHERE u.username = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Member::getByUsername: " . $e->getMessage());
            return false;
        }
    }

    public function getByEmail($email) {
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Member::getByEmail: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $this->conn->beginTransaction();
            
            // Update users table
            $userFields = [];
            $userParams = [':user_id' => $id];
            
            $allowedUserFields = [
                'username', 'firstname', 'middlename', 'lastname', 'date_of_birth', 
                'gender', 'phone_number', 'email', 'address', 'password', 'status'
            ];
            
            foreach ($allowedUserFields as $field) {
                if (isset($data[$field]) && $data[$field] !== '') {
                    $userFields[] = "{$field} = :{$field}";
                    $userParams[":{$field}"] = $data[$field];
                }
            }
            
            if (!empty($userFields)) {
                $userSql = "UPDATE users SET " . implode(', ', $userFields) . " WHERE user_id = :user_id";
                $userStmt = $this->conn->prepare($userSql);
                $userStmt->execute($userParams);
            }
            
            // Update emergency contacts if provided
            if (isset($data['contact_person']) || isset($data['contact_number']) || isset($data['relationship'])) {
                $emergencyFields = [];
                $emergencyParams = [':user_id' => $id];
                
                $allowedEmergencyFields = ['contact_person', 'contact_number', 'relationship'];
                
                foreach ($allowedEmergencyFields as $field) {
                    if (isset($data[$field]) && $data[$field] !== '') {
                        $emergencyFields[] = "{$field} = :{$field}";
                        $emergencyParams[":{$field}"] = $data[$field];
                    }
                }
                
                if (!empty($emergencyFields)) {
                    $emergencySql = "UPDATE emergency_contacts SET " . implode(', ', $emergencyFields) . " WHERE user_id = :user_id";
                    $emergencyStmt = $this->conn->prepare($emergencySql);
                    $emergencyStmt->execute($emergencyParams);
                }
            }
            
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conn->rollback();
            error_log("Database error in Member::update: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            // Instead of hard delete, update status to 'Deleted'
            $sql = "UPDATE users SET status = 'Deleted' WHERE user_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in Member::delete: " . $e->getMessage());
            return false;
        }
    }

    public function usernameExists($username) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database error in Member::usernameExists: " . $e->getMessage());
            return false;
        }
    }

    public function emailExists($email) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database error in Member::emailExists: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($id, $status) {
        try {
            $sql = "UPDATE users SET status = :status WHERE user_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', $status);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in Member::updateStatus: " . $e->getMessage());
            return false;
        }
    }

    public function getSecurityQuestions($username) {
        try {
            $sql = "SELECT sq.sq1, sq.sq2, sq.sq3 
                   FROM security_questions sq
                   JOIN users u ON sq.user_id = u.user_id 
                   WHERE u.username = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Member::getSecurityQuestions: " . $e->getMessage());
            return false;
        }
    }

    public function searchMembers($searchTerm) {
        try {
            $sql = "SELECT 
                u.user_id, u.account_number, u.username, u.firstname, u.middlename, u.lastname, 
                u.date_of_birth, u.gender, u.phone_number, u.email, u.address, u.status, 
                u.registration_date, ec.contact_person, ec.contact_number, ec.relationship
            FROM users u
            LEFT JOIN emergency_contacts ec ON u.user_id = ec.user_id
            LEFT JOIN user_roles ur ON u.user_id = ur.user_id
            WHERE ur.role_id = 5 AND (
                u.username LIKE :search 
                OR u.lastname LIKE :search 
                OR u.firstname LIKE :search 
                OR u.email LIKE :search
            )
            ORDER BY u.registration_date DESC";
            
            $stmt = $this->conn->prepare($sql);
            $searchParam = "%{$searchTerm}%";
            $stmt->bindParam(':search', $searchParam);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Member::searchMembers: " . $e->getMessage());
            return [];
        }
    }

    public function getMemberStats() {
        try {
            $sql = "SELECT 
                COUNT(*) as total_members,
                COUNT(CASE WHEN u.status = 'Active' THEN 1 END) as active_members,
                COUNT(CASE WHEN u.status = 'Suspended' THEN 1 END) as suspended_members,
                COUNT(CASE WHEN u.status = 'Pending' THEN 1 END) as pending_members,
                COUNT(CASE WHEN DATE(u.registration_date) = CURDATE() THEN 1 END) as new_today
            FROM users u
            JOIN user_roles ur ON u.user_id = ur.user_id
            WHERE ur.role_id = 5";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Member::getMemberStats: " . $e->getMessage());
            return false;
        }
    }

    private function generateAccountNumber() {
        $year = date('Y');
        $dayOfYear = str_pad(date('z') + 1, 3, '0', STR_PAD_LEFT);
        $timestamp = str_pad(substr(time(), -5), 5, '0', STR_PAD_LEFT);
        
        // Get next sequence number
        $sql = "SELECT COUNT(*) + 1 as next_seq FROM users WHERE account_number LIKE :pattern";
        $stmt = $this->conn->prepare($sql);
        $pattern = $year . $dayOfYear . $timestamp . '%';
        $stmt->bindParam(':pattern', $pattern);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $sequence = str_pad($result['next_seq'], 4, '0', STR_PAD_LEFT);
        
        return $year . $dayOfYear . $timestamp . $sequence;
    }
}
?>