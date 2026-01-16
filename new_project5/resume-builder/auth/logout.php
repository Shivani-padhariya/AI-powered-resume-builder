<?php
// Start the session to access and destroy it
session_start();

// Include functions file
require_once '../includes/functions.php';

// Call logoutUser function to handle database cleanup
logoutUser($pdo);

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Clear any other cookies
setcookie('PHPSESSID', '', time() - 3600, '/');

// Set cache control headers to prevent back button access
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 01 Jan 1990 00:00:00 GMT");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logging Out</title>
    <script>
        // Clear browser history and cache
        window.history.pushState(null, "", "login.php");
        window.onpopstate = function() {
            window.history.pushState(null, "", "login.php");
        };
        
        // Clear localStorage and sessionStorage
        localStorage.clear();
        sessionStorage.clear();
        
        // Redirect to login page
        window.location.replace("login.php");
    </script>
</head>
<body>
    <p>Logging out...</p>
</body>
</html>