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
                // Get specific user
                $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $user = $stmt->fetch();
                
                if ($user) {
                    echo json_encode([
                        'success' => true,
                        'user' => $user
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'message' => 'User not found'
                    ]);
                }
            } else {
                // Get all users
                $stmt = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
                $users = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'users' => $users
                ]);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);

            if (isset($input['_method']) && $input['_method'] === 'PUT') {
                // Update user
                if (empty($input['id']) || empty($input['name']) || empty($input['email'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'ID, name, and email are required']);
                    break;
                }

                // Check if email already exists for other users
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$input['email'], $input['id']]);
                if ($stmt->fetch()) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Email already exists']);
                    break;
                }

                if (!empty($input['password'])) {
                    $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->execute([$input['name'], $input['email'], $hashedPassword, $input['id']]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                    $stmt->execute([$input['name'], $input['email'], $input['id']]);
                }

                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } else {
                // Create new user
                if (empty($input['name']) || empty($input['email']) || empty($input['password'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Name, email, and password are required']);
                    break;
                }

                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$input['email']]);
                if ($stmt->fetch()) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Email already exists']);
                    break;
                }

                $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$input['name'], $input['email'], $hashedPassword]);

                echo json_encode(['success' => true, 'message' => 'User created successfully', 'userId' => $pdo->lastInsertId()]);
            }
            break;
            

            
        case 'DELETE':
            // Delete user
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'User ID is required'
                ]);
                break;
            }
            
            $userId = $_GET['id'];
            
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found'
                ]);
                break;
            }
            
            // Delete user (this will cascade delete resumes due to foreign key)
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
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
