<?php
require_once '../includes/functions.php';

$error = '';
$success = '';
$password_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $password_error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Email address already registered.';
        } else {
            // Create user
            $hashed_password = hashPassword($password);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                redirect('../dashboard.php', 'Registration successful! Welcome to ResumeAI.');
            } else {
                $error = 'Registration failed. Please try again.';
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
    <title>Register - ResumeAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="../index.php" class="logo">ResumeAI</a>
                <h1>Create Account</h1>
                <p>Join thousands of professionals creating amazing resumes</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($password_error): ?>
                <div class="alert alert-error"><?php echo $password_error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="registerForm" class="auth-form">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                    <small id="passwordHelp" style="color:#888;">Password must be at least 6 characters long.</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                </div>
                

                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>
            <div style="text-align:center; margin: 1.5em 0;">
                <a href="google_login.php" class="btn btn-google" style="background:#fff; color:#444; border:1px solid #ddd; display:inline-block; padding:0.7em 1.5em; border-radius:6px; font-weight:600; font-size:1em; box-shadow:0 2px 6px rgba(0,0,0,0.04);">
                    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" style="width:20px; vertical-align:middle; margin-right:8px;"> Sign in with Google
                </a>
            </div>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign In</a></p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
    <script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    var password = document.getElementById('password').value;
    var confirm = document.getElementById('confirm_password').value;
    
    if (password.length < 6) {
        alert('Password must be at least 6 characters long.');
        e.preventDefault();
        return false;
    }
    if (password !== confirm) {
        alert('Passwords do not match.');
        e.preventDefault();
        return false;
    }
});
</script>
</body>
</html> 