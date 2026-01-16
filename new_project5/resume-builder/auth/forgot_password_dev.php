<?php
require_once '../includes/functions.php';

$message = '';
$error = '';
$step = 'request';
$emailPrefill = '';
$devOtp = ''; // For development: store OTP to display

// Clean expired tokens proactively
cleanExpiredPasswordResets($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'send_otp';
    $email = sanitizeInput($_POST['email'] ?? '');
    $emailPrefill = $email;
    
    if ($action === 'send_otp') {
        if (empty($email)) {
            $error = 'Please enter your email address.';
        } elseif (!validateEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            // Rate limit
            if (!checkPasswordResetRateLimit($pdo, $email)) {
                $error = 'Too many attempts. Please wait 1 hour before trying again.';
            } else {
                // Check if user exists (do not disclose result to client)
                $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                // Always proceed but only send email if user exists
                if ($user) {
                    // Create a 6-digit numeric OTP
                    $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    // Set a short expiry for OTP (10 minutes)
                    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                    
                    // Delete existing tokens for this email
                    $stmt = $pdo->prepare('DELETE FROM password_resets WHERE email = ?');
                    $stmt->execute([$email]);
                    
                    // Insert OTP into password_resets table (token column stores OTP)
                    $stmt = $pdo->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)');
                    $stmt->execute([$email, $otp, $expires]);
                    
                    // For development: store OTP to display
                    $devOtp = $otp;
                    
                    // Try to send email (will fail if not configured)
                    $subject = 'Your ResumeAI Password Reset OTP';
                    $messageBody = "
                    <html>
                    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                            <h2 style='color: #2563eb;'>One-Time Password (OTP)</h2>
                            <p>Hello " . htmlspecialchars($user['name']) . ",</p>
                            <p>Use the one-time password below to reset your ResumeAI account password:</p>
                            <div style='text-align: center; margin: 24px 0;'>
                                <div style='display:inline-block; font-size: 28px; letter-spacing: 6px; font-weight: 700; padding: 12px 18px; border: 2px dashed #2563eb; border-radius: 8px; color:#111;'>$otp</div>
                            </div>
                            <p>This OTP will expire in <strong>10 minutes</strong> and can be used <strong>only once</strong>.</p>
                            <p>If you did not request this, you can safely ignore this email.</p>
                            <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
                            <p style='font-size: 12px; color: #666;'>This is an automated message from ResumeAI. Please do not reply to this email.</p>
                        </div>
                    </body>
                    </html>";
                    
                    $emailSent = sendEmail($email, $subject, $messageBody);
                    
                    if (!$emailSent) {
                        $message = 'Email sending failed, but OTP is generated. Use the OTP below for testing.';
                    } else {
                        $message = 'OTP sent to your email successfully!';
                    }
                } else {
                    $message = 'Email not found in our system, but OTP generated for testing.';
                }
                
                $step = 'verify';
            }
        }
    } elseif ($action === 'verify_otp') {
        $otp = trim($_POST['otp'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (empty($email) || empty($otp)) {
            $error = 'Please enter your email and OTP.';
            $step = 'verify';
        } elseif (!preg_match('/^\d{6}$/', $otp)) {
            $error = 'Invalid OTP format. Please enter the 6-digit code.';
            $step = 'verify';
        } elseif (empty($password)) {
            $error = 'Please enter a new password.';
            $step = 'verify';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
            $step = 'verify';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
            $step = 'verify';
        } else {
            // Validate OTP against password_resets
            $stmt = $pdo->prepare('SELECT id FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW()');
            $stmt->execute([$email, $otp]);
            $row = $stmt->fetch();
            
            if (!$row) {
                $error = 'Invalid or expired OTP. Please request a new one.';
                $step = 'verify';
            } else {
                // Update password
                $hashedPassword = hashPassword($password);
                $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
                $stmt->execute([$hashedPassword, $email]);
                
                // One-time use: delete the OTP
                $stmt = $pdo->prepare('DELETE FROM password_resets WHERE id = ?');
                $stmt->execute([$row['id']]);
                
                $message = 'Your password has been reset successfully! You can now sign in with your new password.';
                $step = 'done';
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
    <title>Forgot Password (Dev Mode) - ResumeAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .dev-otp {
            background: #f0f9ff;
            border: 2px dashed #2563eb;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: center;
        }
        .dev-otp h3 {
            color: #2563eb;
            margin: 0 0 0.5rem 0;
        }
        .dev-otp .otp-code {
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 0.5rem;
            color: #1e40af;
            font-family: monospace;
        }
        .dev-note {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 0.75rem;
            margin: 1rem 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="../index.php" class="logo">ResumeAI</a>
                <h1>Forgot Password (Development Mode)</h1>
                <p><?php echo $step === 'request' ? 'Enter your email to receive a one-time OTP.' : ($step === 'verify' ? 'Enter the OTP and set a new password.' : ''); ?></p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($step === 'request'): ?>
                <form method="POST" class="auth-form">
                    <input type="hidden" name="action" value="send_otp">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($emailPrefill); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fas fa-paper-plane"></i>
                        Generate OTP
                    </button>
                </form>
                
                <div class="dev-note">
                    <strong>Development Note:</strong> This is a development version that will show the OTP on screen instead of sending emails.
                </div>
                
            <?php elseif ($step === 'verify'): ?>
                <?php if ($devOtp): ?>
                    <div class="dev-otp">
                        <h3>🔐 Development OTP</h3>
                        <div class="otp-code"><?php echo $devOtp; ?></div>
                        <p><small>Use this OTP to test the password reset functionality</small></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="auth-form">
                    <input type="hidden" name="action" value="verify_otp">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($emailPrefill); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="otp" class="form-label">6-digit OTP</label>
                        <input type="text" id="otp" name="otp" class="form-input" placeholder="Enter the code" maxlength="6" pattern="\d{6}" required>
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
                        Verify & Reset Password
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
                <p><a href="forgot_password.php">Use Production Version</a></p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>
