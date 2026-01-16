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
                // Get specific resume
                $stmt = $pdo->prepare("
                    SELECT r.*, u.name as user_name 
                    FROM resumes r 
                    LEFT JOIN users u ON r.user_id = u.id 
                    WHERE r.id = ?
                ");
                $stmt->execute([$_GET['id']]);
                $resume = $stmt->fetch();
                
                if ($resume) {
                    echo json_encode([
                        'success' => true,
                        'resume' => $resume
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Resume not found'
                    ]);
                }
            } else {
                // Get all resumes with user information
                $stmt = $pdo->query("
                    SELECT r.*, u.name as user_name 
                    FROM resumes r 
                    LEFT JOIN users u ON r.user_id = u.id 
                    ORDER BY r.created_at DESC
                ");
                $resumes = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'resumes' => $resumes
                ]);
            }
            break;
            
        case 'DELETE':
            // Delete resume
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Resume ID is required'
                ]);
                break;
            }
            
            $resumeId = $_GET['id'];
            
            // Check if resume exists
            $stmt = $pdo->prepare("SELECT id FROM resumes WHERE id = ?");
            $stmt->execute([$resumeId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Resume not found'
                ]);
                break;
            }
            
            // Delete resume
            $stmt = $pdo->prepare("DELETE FROM resumes WHERE id = ?");
            $stmt->execute([$resumeId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Resume deleted successfully'
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
