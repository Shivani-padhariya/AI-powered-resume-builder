<?php
/**
 * Email Test Script for ResumeAI
 * This script tests the email functionality
 */

require_once 'includes/functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = $_POST['test_email'] ?? '';
    
    if (empty($testEmail)) {
        $error = 'Please enter a test email address.';
    } elseif (!validateEmail($testEmail)) {
        $error = 'Please enter a valid email address.';
    } else {
        $subject = "Email Test - ResumeAI";
        $messageBody = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #2563eb;'>Email Test Successful!</h2>
                <p>Hello,</p>
                <p>This is a test email from your ResumeAI application.</p>
                <p>If you received this email, your email configuration is working correctly!</p>
                <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
                <p style='font-size: 12px; color: #666;'>Sent at: " . date('Y-m-d H:i:s') . "</p>
            </div>
        </body>
        </html>";
        
        if (sendEmail($testEmail, $subject, $messageBody)) {
            $message = 'Test email sent successfully! Please check your inbox.';
        } else {
            $error = 'Failed to send test email. Please check your configuration and error logs.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="index.php" class="logo">ResumeAI</a>
                <h1>Email Test</h1>
                <p>Test your email configuration</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="test_email" class="form-label">Test Email Address</label>
                    <input type="email" id="test_email" name="test_email" class="form-input" 
                           value="<?php echo htmlspecialchars($_POST['test_email'] ?? ''); ?>" required>
                    <small class="form-help">Enter an email address to send a test email</small>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-paper-plane"></i>
                    Send Test Email
                </button>
            </form>
            
            <div class="auth-footer">
                <p><a href="setup_email.php">← Email Setup</a></p>
                <p><a href="index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html> 