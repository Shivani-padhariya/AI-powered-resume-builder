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
                // Get specific feedback
                $stmt = $pdo->prepare("
                    SELECT f.*, u.name as user_name 
                    FROM feedback f 
                    LEFT JOIN users u ON f.user_id = u.id 
                    WHERE f.id = ?
                ");
                $stmt->execute([$_GET['id']]);
                $feedback = $stmt->fetch();
                
                if ($feedback) {
                    echo json_encode([
                        'success' => true,
                        'feedback' => $feedback
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Feedback not found'
                    ]);
                }
            } else {
                // Get all feedback with user information
                $stmt = $pdo->query("
                    SELECT f.*, u.name as user_name 
                    FROM feedback f 
                    LEFT JOIN users u ON f.user_id = u.id 
                    ORDER BY f.created_at DESC
                ");
                $feedbackList = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'feedback' => $feedbackList
                ]);
            }
            break;
            
        case 'PUT':
            // Update feedback status and admin notes
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Feedback ID is required'
                ]);
                break;
            }
            
            $feedbackId = $input['id'];
            $status = $input['status'] ?? 'pending';
            $adminNotes = $input['admin_notes'] ?? '';
            
            // Check if feedback exists
            $stmt = $pdo->prepare("SELECT id FROM feedback WHERE id = ?");
            $stmt->execute([$feedbackId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Feedback not found'
                ]);
                break;
            }
            
            // Update feedback
            $stmt = $pdo->prepare("UPDATE feedback SET status = ?, admin_notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$status, $adminNotes, $feedbackId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Feedback updated successfully'
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
