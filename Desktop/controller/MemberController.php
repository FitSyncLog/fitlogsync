<?php
require_once __DIR__ . '/../models/MemberModel.php';

class MemberController {
    private $memberModel;

    public function __construct() {
        try {
            $this->memberModel = new MemberModel();
        } catch (Exception $e) {
            error_log("Error initializing MemberController: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to initialize member controller'
            ]);
            exit;
        }
    }

    public function getAllMembers() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Content-Type');

        try {
            $result = $this->memberModel->getAllMembers();
            echo json_encode($result);
        } catch (Exception $e) {
            error_log("Error in getAllMembers: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch members: ' . $e->getMessage()
            ]);
        }
    }

    public function getMemberById() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Content-Type');

        if (!isset($_GET['id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Member ID is required'
            ]);
            return;
        }

        $id = $_GET['id'];
        echo json_encode($this->memberModel->getMemberById($id));
    }

    public function updateMember() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request data'
            ]);
            return;
        }

        echo json_encode($this->memberModel->updateMember($data));
    }

    public function deleteMember() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['accountNumber'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Account number is required'
            ]);
            return;
        }

        echo json_encode($this->memberModel->deleteMember($data['accountNumber']));
    }
}

// Handle the request
$controller = new MemberController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getAll':
        $controller->getAllMembers();
        break;
    case 'getById':
        $controller->getMemberById();
        break;
    case 'update':
        $controller->updateMember();
        break;
    case 'delete':
        $controller->deleteMember();
        break;
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
}
?>