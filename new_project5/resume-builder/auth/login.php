<?php
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, email, password, google_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Check for password-based login
            if (!empty($user['password']) && verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                redirect('../dashboard.php', 'Welcome back, ' . $user['name'] . '!');
            // Check for Google-authenticated login (if password field is empty)
            } elseif (empty($user['password']) && !empty($user['google_id'])) {
                // If a user tries to log in with a password but has a Google account,
                // they should be redirected to use Google Sign-In.
                $error = 'This account is registered with Google. Please sign in with Google.';
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ResumeAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="../index.php" class="logo">ResumeAI</a>
                <h1>Welcome Back</h1>
                <p>Sign in to continue building your professional resume</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>
            <div style="text-align:center; margin: 1.5em 0;">
                <a href="google_login.php" class="btn btn-google" style="background:#fff; color:#444; border:1px solid #ddd; display:inline-block; padding:0.7em 1.5em; border-radius:6px; font-weight:600; font-size:1em; box-shadow:0 2px 6px rgba(0,0,0,0.04);">
                    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" style="width:20px; vertical-align:middle; margin-right:8px;"> Sign in with Google
                </a>
            </div>
            
            <div class="auth-footer">
                <p><a href="forgot_password_simple.php">Forgot your password?</a></p>
                <p>Don't have an account? <a href="register.php">Sign Up</a></p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>