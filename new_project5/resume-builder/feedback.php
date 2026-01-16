<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="image/2.jpg" type="image/x-icon">
</head>
<body>
<?php
require_once 'includes/header.php';

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    $category = trim($_POST['category'] ?? 'general');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif ($rating < 1 || $rating > 5) {
        $error_message = 'Please select a valid rating.';
    } else {
        try {
            // Get user_id if logged in
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            
            $stmt = $pdo->prepare("
                INSERT INTO feedback (user_id, name, email, rating, category, subject, message) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$user_id, $name, $email, $rating, $category, $subject, $message]);
            
            $success_message = 'Thank you for your feedback! We appreciate your input.';
            
            // Clear form data
            $name = $email = $subject = $message = '';
            $rating = 5;
            $category = 'general';
            
        } catch (PDOException $e) {
            $error_message = 'Sorry, there was an error submitting your feedback. Please try again.';
        }
    }
}

// Get existing feedback for display (optional)
$recent_feedback = [];
try {
    $stmt = $pdo->query("
        SELECT name, rating, category, subject, message, created_at 
        FROM feedback 
        WHERE status != 'closed' 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recent_feedback = $stmt->fetchAll();
} catch (PDOException $e) {
    // Silently fail for display
}
?>

<main class="main-content">
    <div class="container">
        <!-- Hero Section -->
        <section class="hero-section text-center">
            <div class="hero-content">
                <h1 class="gradient-text">Share Your Feedback</h1>
                <p class="hero-subtitle">Help us improve ResumeAI by sharing your thoughts, suggestions, and experiences</p>
            </div>
        </section>

        <!-- Feedback Form Section -->
        <section class="feedback-section">
            <div class="feedback-container">
                <div class="feedback-form-container">
                    <h2><i class="fas fa-comment-dots"></i> Submit Your Feedback</h2>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="feedback-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">
                                    <i class="fas fa-user"></i> Full Name *
                                </label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i> Email Address *
                                </label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="Enter your email address" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rating">
                                    <i class="fas fa-star"></i> Rate Your Experience *
                                </label>
                                <div class="rating-input">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo ($rating ?? 5) == $i ? 'checked' : ''; ?>>
                                        <label for="star<?php echo $i; ?>" class="star-label" title="<?php echo $i == 1 ? 'Excellent' : ($i == 2 ? 'very good' : ($i == 3 ? 'Good' : ($i == 4 ? 'Fair' : 'poor'))); ?>">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                                <small class="rating-hint">Click on a star to rate your experience</small>
                            </div>
                            <div class="form-group">
                                <label for="category">
                                    <i class="fas fa-tag"></i> Feedback Category *
                                </label>
                                <select id="category" name="category" required>
                                    <option value="general" <?php echo ($category ?? 'general') == 'general' ? 'selected' : ''; ?>>General Feedback</option>
                                    <option value="bug_report" <?php echo ($category ?? 'general') == 'bug_report' ? 'selected' : ''; ?>>Bug Report</option>
                                    <option value="feature_request" <?php echo ($category ?? 'general') == 'feature_request' ? 'selected' : ''; ?>>Feature Request</option>
                                    <option value="improvement" <?php echo ($category ?? 'general') == 'improvement' ? 'selected' : ''; ?>>Improvement Suggestion</option>
                                    <option value="other" <?php echo ($category ?? 'general') == 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">
                                <i class="fas fa-heading"></i> Subject *
                            </label>
                            <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" placeholder="Brief summary of your feedback" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">
                                <i class="fas fa-comment"></i> Detailed Feedback *
                            </label>
                            <textarea id="message" name="message" rows="6" placeholder="Please provide detailed feedback, suggestions, or describe any issues you've encountered..." required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="fas fa-paper-plane"></i>
                            Submit Feedback
                        </button>
                    </form>
                </div>
                
                <!-- Recent Feedback Display -->
                <?php if (!empty($recent_feedback)): ?>
                <div class="recent-feedback">
                    <h3><i class="fas fa-users"></i> Recent Community Feedback</h3>
                    <div class="feedback-list">
                        <?php foreach ($recent_feedback as $feedback): ?>
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <div class="feedback-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $feedback['rating'] ? 'filled' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="feedback-category"><?php echo ucfirst(str_replace('_', ' ', $feedback['category'])); ?></span>
                                </div>
                                <h4 class="feedback-subject"><?php echo htmlspecialchars($feedback['subject']); ?></h4>
                                <p class="feedback-message"><?php echo htmlspecialchars(substr($feedback['message'], 0, 150)) . (strlen($feedback['message']) > 150 ? '...' : ''); ?></p>
                                <div class="feedback-meta">
                                    <span class="feedback-author">
                                        <i class="fas fa-user-circle"></i>
                                        <?php echo htmlspecialchars($feedback['name']); ?>
                                    </span>
                                    <span class="feedback-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo date('M j, Y', strtotime($feedback['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- Feedback Stats Section -->
        <section class="stats-section">
            <div class="stats-container">
                <h2>Your Feedback Matters</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Community Driven</h3>
                            <p>Your suggestions help shape the future of ResumeAI</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Innovation</h3>
                            <p>We implement the best ideas from our community</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-content">
                            <h3>User Experience</h3>
                            <p>Your feedback helps us create better tools for everyone</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
