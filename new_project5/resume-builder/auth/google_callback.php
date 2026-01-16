<?php
require_once '../includes/functions.php';

// ====== CONFIGURATION ======
$client_id = '1023204351994-2o1jt2vcm7oocp2uh2l8vp4bfmk7j9l2.apps.googleusercontent.com';
$client_secret = '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';
// Must EXACTLY match the redirect in google_login.php and the Cloud Console
$redirect_uri = 'http://localhost:90/new_project5/resume-builder/auth/google_callback.php';

// ====== Validate required params ======
if (!isset($_GET['code'])) {
    redirect('login.php', 'Google sign-in failed. Please try again.', 'error');
}

// CSRF check: verify state
if (!isset($_GET['state']) || !isset($_SESSION['oauth2_state']) || hash_equals($_SESSION['oauth2_state'], $_GET['state']) === false) {
    unset($_SESSION['oauth2_state']);
    redirect('login.php', 'Invalid or missing OAuth state. Please try again.', 'error');
}
// One-time use state
unset($_SESSION['oauth2_state']);

// Exchange code for access token
$token_url = 'https://oauth2.googleapis.com/token';
$post_fields = [
    'code' => $_GET['code'],
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code',
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields, '', '&', PHP_QUERY_RFC3986));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if ($response === false) {
    redirect('login.php', 'Google sign-in failed (network).', 'error');
}
curl_close($ch);

$token_data = json_decode($response, true);
if (!isset($token_data['access_token'])) {
    redirect('login.php', 'Google sign-in failed (token error).', 'error');
}

// Fetch user info
$userinfo_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
$headers = [
    'Authorization: Bearer ' . $token_data['access_token']
];
$ch = curl_init($userinfo_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$userinfo_response = curl_exec($ch);
curl_close($ch);

$userinfo = json_decode($userinfo_response, true);
if (!isset($userinfo['email'])) {
    redirect('login.php', 'Google sign-in failed (profile error).', 'error');
}

$email = $userinfo['email'];
$name = $userinfo['name'] ?? '';
$google_id = $userinfo['id'] ?? '';

// Check if user exists
$stmt = $pdo->prepare('SELECT id, name, email, google_id FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    // User exists, log them in
    // User exists, log them in
    $_SESSION['user_id'] = $user['id'];
    // Update google_id if it's not set for an existing user
    if (empty($user['google_id']) && !empty($google_id)) {
        $update_stmt = $pdo->prepare('UPDATE users SET google_id = ? WHERE id = ?');
        $update_stmt->execute([$google_id, $user['id']]);
    }
    redirect('../dashboard.php', 'Welcome back, ' . htmlspecialchars($user['name']) . '!');
} else {
    // Register new user
    $stmt = $pdo->prepare('INSERT INTO users (name, email, google_id) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $google_id]);
    $_SESSION['user_id'] = $pdo->lastInsertId();
    redirect('../dashboard.php', 'Welcome, ' . htmlspecialchars($name) . '! Your account has been created.');
}