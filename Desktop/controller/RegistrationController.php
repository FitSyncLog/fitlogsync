<?php
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../models/Database.php';

class RegistrationController
{
    private $memberModel;
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->memberModel = new Member($this->db);
    }

    public function register()
    {
        header('Content-Type: application/json');

        try {
            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON input: ' . json_last_error_msg());
            }

            // Log received data for debugging
            error_log('Received data: ' . print_r($data, true));

            // Validate required fields (this now modifies $data by reference)
            $this->validateRequiredFields($data);

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            // Validate password strength
            if (strlen($data['password']) < 6) {
                throw new Exception('Password must be at least 6 characters long');
            }

            // Check if passwords match
            if ($data['password'] !== $data['confirm_password']) {
                throw new Exception('Passwords do not match');
            }

            // Check if username already exists
            if ($this->memberModel->usernameExists($data['username'])) {
                throw new Exception('Username already exists');
            }

            // Check if email already exists
            if ($this->memberModel->emailExists($data['email'])) {
                throw new Exception('Email already exists');
            }

            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            // Remove confirm_password from data
            unset($data['confirm_password']);

            // Map field names to database columns - FIXED: Only map fields that need mapping
            $fieldMappings = [
                'dateofbirth' => 'date_of_birth',
                'phonenumber' => 'phone_number'
            ];

            foreach ($fieldMappings as $frontendName => $dbName) {
                if (isset($data[$frontendName])) {
                    $data[$dbName] = $data[$frontendName];
                    unset($data[$frontendName]);
                }
            }

            // Handle checkboxes - they come as '1' or '0' from frontend
            $data['waiver_rules'] = isset($data['waiver_rules']) ? (int)$data['waiver_rules'] : 0;
            $data['waiver_liability'] = isset($data['waiver_liability']) ? (int)$data['waiver_liability'] : 0;
            $data['waiver_cancel'] = isset($data['waiver_cancel']) ? (int)$data['waiver_cancel'] : 0;

            // Additional validation to ensure emergency contact fields are present
            if (empty($data['contact_person']) || empty($data['contact_number']) || empty($data['relationship'])) {
                throw new Exception('Emergency contact information is incomplete');
            }

            // Log final data before database insertion
            error_log('Final data for database: ' . print_r($data, true));

            // Create member
            $memberId = $this->memberModel->create($data);

            if ($memberId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Member registered successfully',
                    'member_id' => $memberId
                ]);
            } else {
                $errorInfo = $this->db->errorInfo();
                throw new Exception('Failed to register member: ' . ($errorInfo[2] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function validateRequiredFields(&$data)
    {
        $required = [
            'username',
            'lastname',
            'firstname',
            'dateofbirth',
            'gender',
            'address',
            'phonenumber',
            'email',
            'password',
            'confirm_password',
            'contact_person',
            'contact_number',
            'relationship',
            'security_question1',
            'security_answer1',
            'security_question2',
            'security_answer2',
            'security_question3',
            'security_answer3'
        ];

        // Validate PAR-Q questions
        for ($i = 1; $i <= 10; $i++) {
            if (!isset($data["q{$i}"]) || empty($data["q{$i}"])) {
                throw new Exception("PAR-Q question {$i} is required");
            }
        }

        // Single validation loop that both validates and trims
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new Exception("Field '{$field}' is required");
            }
            
            if (is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
                if ($data[$field] === '') {
                    throw new Exception("Field '{$field}' cannot be empty");
                }
            }
        }

        // Validate waivers - convert string '1'/'0' to boolean for validation
        $waiverFields = ['waiver_rules', 'waiver_liability', 'waiver_cancel'];
        $waiverMessages = [
            'waiver_rules' => 'You must agree to the Rules and Policy',
            'waiver_liability' => 'You must agree to the Liability Waiver',
            'waiver_cancel' => 'You must agree to the Cancellation and Refund Policy'
        ];

        foreach ($waiverFields as $field) {
            if (!isset($data[$field]) || $data[$field] !== '1') {
                throw new Exception($waiverMessages[$field]);
            }
        }
    }
}

// Handle request
if (isset($_GET['action']) && $_GET['action'] === 'register') {
    $controller = new RegistrationController();
    $controller->register();
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
}