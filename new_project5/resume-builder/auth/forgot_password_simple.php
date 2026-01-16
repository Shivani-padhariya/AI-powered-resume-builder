<?php
require_once '../includes/functions.php';

$message = '';
$error = '';
$step = 'request';
$emailPrefill = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'verify_email';
    $email = sanitizeInput($_POST['email'] ?? '');
    $emailPrefill = $email;
    
    if ($action === 'verify_email') {
        if (empty($email)) {
            $error = 'Please enter your email address.';
        } elseif (!validateEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                $step = 'change_password';
                $message = 'Email verified! You can now change your password.';
            } else {
                $error = 'Email address not found in our system.';
            }
        }
    } elseif ($action === 'change_password') {
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($password)) {
            $error = 'Please enter a new password.';
            $step = 'change_password';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
            $step = 'change_password';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
            $step = 'change_password';
        } else {
            // Update password
            $hashed_password = hashPassword($password);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            
            if ($stmt->execute([$hashed_password, $email])) {
                $message = 'Your password has been changed successfully! You can now sign in with your new password.';
                $step = 'done';
            } else {
                $error = 'Failed to update password. Please try again.';
                $step = 'change_password';
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
    <title>Forgot Password - ResumeAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="../index.php" class="logo">ResumeAI</a>
                <h1>Forgot Password</h1>
                <p><?php echo $step === 'request' ? 'Enter your registered email to change your password.' : ($step === 'change_password' ? 'Set your new password.' : ''); ?></p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($step === 'request'): ?>
                <form method="POST" class="auth-form">
                    <input type="hidden" name="action" value="verify_email">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($emailPrefill); ?>" required>
                        <small class="form-help">Enter the email address you used for registration</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fas fa-check"></i>
                        Verify Email
                    </button>
                </form>
                
            <?php elseif ($step === 'change_password'): ?>
                <form method="POST" class="auth-form">
                    <input type="hidden" name="action" value="change_password">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($emailPrefill); ?>" readonly>
                        <small class="form-help">Verified email address</small>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" id="password" name="password" class="form-input" required minlength="6">
                        <small class="form-help">Password must be at least 6 characters long</small>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fas fa-key"></i>
                        Change Password
                    </button>
                </form>
                
            <?php else: ?>
                <div style="text-align:center; margin-top:1rem;">
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                </div>
            <?php endif; ?>
            
            <div class="auth-footer">
                <p>Remember your password? <a href="login.php">Sign In</a></p>
                <p>Don't have an account? <a href="register.php">Sign Up</a></p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    <script>
        // Password confirmation validation
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            
            if (password && confirmPassword) {
                function validatePassword() {
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Passwords do not match');
                    } else {
                        confirmPassword.setCustomValidity('');
                    }
                }
                
                password.addEventListener('change', validatePassword);
                confirmPassword.addEventListener('keyup', validatePassword);
            }
        });
    </script>
</body>
</html>
