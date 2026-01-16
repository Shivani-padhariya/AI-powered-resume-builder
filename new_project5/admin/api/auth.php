<?php
session_start();

// Check if admin is logged in
function requireAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized access. Please login as admin.'
        ]);
        exit;
    }
}

// Include this at the top of each API file to require authentication
// require_once 'auth.php';
// requireAdminAuth();
?>
