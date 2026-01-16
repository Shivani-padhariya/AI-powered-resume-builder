<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../resume-builder/includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get specific message
                $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $message = $stmt->fetch();
                
                if ($message) {
                    echo json_encode([
                        'success' => true,
                        'message' => $message
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Message not found'
                    ]);
                }
            } else {
                // Get all messages
                $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                $messages = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'messages' => $messages
                ]);
            }
            break;
            
        case 'DELETE':
            // Delete message
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Message ID is required'
                ]);
                break;
            }
            
            $messageId = $_GET['id'];
            
            // Check if message exists
            $stmt = $pdo->prepare("SELECT id FROM contact_messages WHERE id = ?");
            $stmt->execute([$messageId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Message not found'
                ]);
                break;
            }
            
            // Delete message
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$messageId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Message deleted successfully'
            ]);
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
