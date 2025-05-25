<?php
class MemberModel {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/Database.php';
        $this->db = new Database();
    }

    public function getAllMembers() {
        try {
            $query = "SELECT 
                        u.id,
                        u.account_number,
                        u.firstname,
                        u.lastname,
                        u.email,
                        u.phone_number,
                        u.status,
                        DATE_FORMAT(u.registration_date, '%Y-%m-%d') as registration_date,
                        u.gender,
                        u.date_of_birth,
                        u.address,
                        u.membership_type,
                        u.emergency_contact_name,
                        u.emergency_contact_phone,
                        u.health_conditions
                    FROM users u
                    ORDER BY u.registration_date DESC";

            $stmt = $this->db->executeQuery($query);
            $result = $stmt->get_result();

            $members = [];
            while ($row = $result->fetch_assoc()) {
                $members[] = $row;
            }

            return [
                'success' => true,
                'data' => $members
            ];
        } catch (Exception $e) {
            error_log("Error in getAllMembers: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch members: ' . $e->getMessage()
            ];
        }
    }

    public function getMemberById($id) {
        try {
            $query = "SELECT 
                        u.id,
                        u.account_number,
                        u.firstname,
                        u.lastname,
                        u.email,
                        u.phone_number,
                        u.status,
                        DATE_FORMAT(u.registration_date, '%Y-%m-%d') as registration_date,
                        u.gender,
                        u.date_of_birth,
                        u.address,
                        u.membership_type,
                        u.emergency_contact_name,
                        u.emergency_contact_phone,
                        u.health_conditions
                    FROM users u
                    WHERE u.id = ?";

            $stmt = $this->db->executeQuery($query, [$id], "i");
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return [
                    'success' => false,
                    'message' => 'Member not found'
                ];
            }

            return [
                'success' => true,
                'member' => $result->fetch_assoc()
            ];
        } catch (Exception $e) {
            error_log("Error in getMemberById: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch member: ' . $e->getMessage()
            ];
        }
    }

    public function updateMember($data) {
        try {
            $query = "UPDATE users SET 
                        firstname = ?,
                        lastname = ?,
                        email = ?,
                        phone_number = ?,
                        gender = ?,
                        date_of_birth = ?,
                        address = ?,
                        membership_type = ?,
                        status = ?,
                        emergency_contact_name = ?,
                        emergency_contact_phone = ?,
                        health_conditions = ?
                    WHERE account_number = ?";

            $params = [
                $data['firstname'],
                $data['lastname'],
                $data['email'],
                $data['phone'],
                $data['gender'],
                $data['date_of_birth'],
                $data['address'],
                $data['membership_type'],
                $data['status'],
                $data['emergency_contact_name'],
                $data['emergency_contact_phone'],
                $data['health_conditions'],
                $data['accountNumber']
            ];

            $stmt = $this->db->executeQuery($query, $params, "sssssssssssss");

            if ($stmt->affected_rows === 0) {
                return [
                    'success' => false,
                    'message' => 'No member found with the provided account number'
                ];
            }

            return [
                'success' => true,
                'message' => 'Member updated successfully'
            ];
        } catch (Exception $e) {
            error_log("Error in updateMember: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update member: ' . $e->getMessage()
            ];
        }
    }

    public function deleteMember($accountNumber) {
        try {
            $query = "UPDATE users SET status = 'Deleted' WHERE account_number = ?";
            
            $stmt = $this->db->executeQuery($query, [$accountNumber], "s");

            if ($stmt->affected_rows === 0) {
                return [
                    'success' => false,
                    'message' => 'No member found with the provided account number'
                ];
            }

            return [
                'success' => true,
                'message' => 'Member deleted successfully'
            ];
        } catch (Exception $e) {
            error_log("Error in deleteMember: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete member: ' . $e->getMessage()
            ];
        }
    }
}
?> 