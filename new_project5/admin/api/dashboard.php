<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../resume-builder/includes/db.php';

try {
    // Get dashboard statistics
    $stats = [];
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $stats['totalUsers'] = $stmt->fetch()['count'];
    
    // Total resumes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM resumes");
    $stats['totalResumes'] = $stmt->fetch()['count'];
    
    // Pending feedback
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM feedback WHERE status = 'pending'");
    $stats['pendingFeedback'] = $stmt->fetch()['count'];
    
    // Total contact messages
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages");
    $stats['totalMessages'] = $stmt->fetch()['count'];
    
    // Recent activity (combine recent actions from different tables)
    $recentActivity = [];
    
    // Recent user registrations
    $stmt = $pdo->query("SELECT 'User Registration' as action, name as user, 'New user registered' as details, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        $recentActivity[] = $row;
    }
    
    // Recent resume creations
    $stmt = $pdo->query("SELECT 'Resume Created' as action, u.name as user, CONCAT('Created resume: ', r.title) as details, r.created_at FROM resumes r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        $recentActivity[] = $row;
    }
    
    // Recent feedback submissions
    $stmt = $pdo->query("SELECT 'Feedback Submitted' as action, COALESCE(u.name, f.name) as user, CONCAT('Feedback: ', f.subject) as details, f.created_at FROM feedback f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        $recentActivity[] = $row;
    }
    
    // Recent contact messages
    $stmt = $pdo->query("SELECT 'Contact Message' as action, name as user, CONCAT('Message: ', subject) as details, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5");
    while ($row = $stmt->fetch()) {
        $recentActivity[] = $row;
    }
    
    // Sort all activities by date and take top 10
    usort($recentActivity, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    $recentActivity = array_slice($recentActivity, 0, 10);
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recentActivity' => $recentActivity
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
