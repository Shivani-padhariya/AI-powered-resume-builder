<?php
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$resume_id = $input['id'] ?? null;

if (!$resume_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Resume ID is required']);
    exit();
}

// Delete the resume
if (deleteResume($pdo, $resume_id, $_SESSION['user_id'])) {
    echo json_encode(['success' => true, 'message' => 'Resume deleted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete resume']);
}
?> 