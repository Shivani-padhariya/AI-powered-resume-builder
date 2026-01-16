<?php 
require_once 'includes/functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Store in database
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = 'Thank you for your message! We will get back to you soon.';
            // Clear form data
            $_POST = [];
        } catch (Exception $e) {
            $error = 'Failed to submit your message. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="image/2.jpg" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="contact-hero" style="padding: 120px 0 80px; text-align: center;">
            <h1 class="gradient-text">Contact Us</h1>
            <p class="hero-subtitle">Get in touch with our team for support, feedback, or questions</p>
        </div>

        <div class="contact-content" style="max-width: 1000px; margin: 0 auto; padding: 2rem 0;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">
                <!-- Contact Form -->
                <div class="card">
                    <h2>Send us a Message</h2>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" class="contact-form">
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" class="form-input" 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-input" 
                                   value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message" class="form-label">Message</label>
                            <textarea id="message" name="message" class="form-input form-textarea" rows="6" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>
                
                <!-- Contact Information -->
                <div class="card">
                    <h2>Get in Touch</h2>
                    <p>We're here to help! Reach out to us through any of these channels:</p>
                    
                    <div style="margin-top: 2rem;">
                        <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                <i class="fas fa-envelope" style="color: white;"></i>
                            </div>
                            <div>
                                <h3>Email</h3>
                                <p>support@resumeai.com</p>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">We typically respond within 24 hours</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                <i class="fas fa-comments" style="color: white;"></i>
                            </div>
                            <div>
                                <h3>Live Chat</h3>
                                <p>Available 9 AM - 6 PM EST</p>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">Get instant help from our support team</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                <i class="fas fa-phone" style="color: white;"></i>
                            </div>
                            <div>
                                <h3>Phone</h3>
                                <p>+1 (555) 123-4567</p>
                                <p style="color: var(--text-secondary); font-size: 0.875rem;">Monday to Friday, 9 AM - 6 PM EST</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center;">
                            <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                <i class="fas fa-map-marker-alt" style="color: white;"></i>
                            </div>
                            <div>
                                <h3>Address</h3>
                                <p>123 Resume Street<br>Tech City, TC 12345<br>United States</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Section -->
            <div class="card">
                <h2>Frequently Asked Questions</h2>
                <div style="margin-top: 2rem;">
                    <div style="margin-bottom: 1.5rem;">
                        <h3>How does the AI resume builder work?</h3>
                        <p>Our AI analyzes your input and provides intelligent suggestions for content, keywords, and formatting based on your industry and experience level. It helps you create a resume that stands out to both human recruiters and ATS systems.</p>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <h3>Are the templates ATS-friendly?</h3>
                        <p>Yes! All our templates are designed to be ATS-friendly with clean formatting, standard fonts, and proper structure that Applicant Tracking Systems can easily parse and understand.</p>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <h3>Can I download my resume as a PDF?</h3>
                        <p>Absolutely! You can download your resume as a high-quality PDF file that maintains perfect formatting across all devices and platforms.</p>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <h3>Is my data secure?</h3>
                        <p>Yes, we take data security seriously. All your information is encrypted and stored securely. We never share your personal data with third parties.</p>
                    </div>
                    
                    <div>
                        <h3>Do you offer customer support?</h3>
                        <p>Yes! We provide comprehensive customer support through email, live chat, and phone. Our support team is available to help you with any questions or issues.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html> 