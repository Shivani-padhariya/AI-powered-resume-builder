<?php
require_once '../includes/functions.php';

// ====== CONFIGURATION ======
$client_id = '1023204351994-2o1jt2vcm7oocp2uh2l8vp4bfmk7j9l2.apps.googleusercontent.com';
// Must EXACTLY match the authorized redirect URI in Google Console
$redirect_uri = 'http://localhost:90/new_project5/resume-builder/auth/google_callback.php';
$scope = 'email profile';

// ====== CSRF PROTECTION (state) ======
if (empty($_SESSION['oauth2_state'])) {
    $_SESSION['oauth2_state'] = bin2hex(random_bytes(16));
}

// ====== REDIRECT TO GOOGLE OAUTH ======
$params = [
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => $scope,
    'state' => $_SESSION['oauth2_state'],
    'access_type' => 'online',
    'prompt' => 'select_account',
];

$url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
header('Location: ' . $url);
exit;