<?php
/**
 * Email Setup Script for ResumeAI
 * This script helps you configure Gmail SMTP settings for password reset functionality
 */

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gmail = $_POST['gmail'] ?? '';
    $appPassword = $_POST['app_password'] ?? '';
    $fromName = $_POST['from_name'] ?? 'ResumeAI';
    
    if (empty($gmail) || empty($appPassword)) {
        $error = 'Please provide both Gmail address and App Password.';
    } else {
        // Read the current config file
        $configContent = file_get_contents('includes/email_config.php');
        
        // Replace the placeholder values
        $configContent = str_replace(
            "'your-email@gmail.com'",
            "'" . addslashes($gmail) . "'",
            $configContent
        );
        $configContent = str_replace(
            "'your-app-password'",
            "'" . addslashes($appPassword) . "'",
            $configContent
        );
        $configContent = str_replace(
            "'ResumeAI'",
            "'" . addslashes($fromName) . "'",
            $configContent
        );
        
        // Write the updated config
        if (file_put_contents('includes/email_config.php', $configContent)) {
            $success = 'Email configuration updated successfully!';
        } else {
            $error = 'Failed to update configuration file. Please check file permissions.';
        }
    }
}

// Read current config to show existing values
$currentConfig = include 'includes/email_config.php';
$currentGmail = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
$currentFromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Setup - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .setup-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .setup-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .steps {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .step {
            margin: 15px 0;
            padding: 10px;
            border-left: 3px solid #2563eb;
            background: white;
        }
        .step-number {
            font-weight: bold;
            color: #2563eb;
        }
        .code-block {
            background: #1f2937;
            color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="auth-header">
                <a href="index.php" class="logo">ResumeAI</a>
                <h1>Email Setup</h1>
                <p>Configure Gmail SMTP for password reset functionality</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="steps">
                <h3><i class="fas fa-info-circle"></i> Setup Instructions</h3>
                
                <div class="step">
                    <div class="step-number">Step 1: Enable 2-Factor Authentication</div>
                    <p>Go to your Google Account settings and enable 2-Factor Authentication if not already enabled.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">Step 2: Generate App Password</div>
                    <p>1. Go to <a href="https://myaccount.google.com/apppasswords" target="_blank">Google App Passwords</a></p>
                    <p>2. Select "Mail" as the app and "Other" as the device</p>
                    <p>3. Enter "ResumeAI" as the device name</p>
                    <p>4. Click "Generate" and copy the 16-character password</p>
                </div>
                
                <div class="step">
                    <div class="step-number">Step 3: Install PHPMailer</div>
                    <p>Run this command in your project directory:</p>
                    <div class="code-block">composer install</div>
                    <p>Or if you don't have Composer, download PHPMailer manually and include it in your project.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">Step 4: Configure Settings</div>
                    <p>Enter your Gmail address and app password below:</p>
                </div>
            </div>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="gmail" class="form-label">Gmail Address</label>
                    <input type="email" id="gmail" name="gmail" class="form-input" 
                           value="<?php echo htmlspecialchars($currentGmail); ?>" required>
                    <small class="form-help">The Gmail address you want to send emails from</small>
                </div>
                
                <div class="form-group">
                    <label for="app_password" class="form-label">App Password</label>
                    <input type="password" id="app_password" name="app_password" class="form-input" required>
                    <small class="form-help">The 16-character app password generated from Google</small>
                </div>
                
                <div class="form-group">
                    <label for="from_name" class="form-label">From Name</label>
                    <input type="text" id="from_name" name="from_name" class="form-input" 
                           value="<?php echo htmlspecialchars($currentFromName); ?>" required>
                    <small class="form-help">The name that will appear as the sender</small>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-save"></i>
                    Save Configuration
                </button>
            </form>
            
            <div class="auth-footer">
                <p><a href="index.php">← Back to Home</a></p>
                <p><a href="auth/login.php">Go to Login</a></p>
            </div>
        </div>
    </div>
</body>
</html> 