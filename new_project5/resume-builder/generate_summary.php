<?php
require_once 'includes/functions.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
error_log('Session check - isLoggedIn(): ' . (isLoggedIn() ? 'true' : 'false'));
error_log('Session data: ' . print_r($_SESSION, true));

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if it's a POST request
error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Debug: Log the input
error_log('AI Summary Request - Input: ' . print_r($input, true));
error_log('AI Summary Request - Raw input: ' . file_get_contents('php://input'));

$jobRole = sanitizeInput($input['job_role'] ?? '');
$experience = sanitizeInput($input['experience'] ?? '');
$industry = sanitizeInput($input['industry'] ?? '');

// Validation
if (empty($jobRole)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Job role is required']);
    exit();
}

if (empty($experience)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Experience level is required']);
    exit();
}

try {
    // Debug: Log the parameters
    error_log('AI Summary Generation - Job Role: ' . $jobRole . ', Experience: ' . $experience . ', Industry: ' . $industry);
    
    // Generate AI summary
    $summary = generateAISummary($jobRole, $experience, '', $industry);
    
    // Get enhanced AI suggestions for this job role
    $suggestions = getEnhancedAISuggestions($jobRole, $experience, $industry);
    
    // Debug: Log the results
    error_log('AI Summary Generated: ' . substr($summary, 0, 100) . '...');
    error_log('AI Suggestions: ' . print_r($suggestions, true));
    
    // Return success response
    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'suggestions' => $suggestions
    ]);
    
} catch (Exception $e) {
    error_log('AI Summary Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to generate summary: ' . $e->getMessage()
    ]);
}
?> 