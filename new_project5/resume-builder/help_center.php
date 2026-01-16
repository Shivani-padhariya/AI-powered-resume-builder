<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="contact-hero" style="padding: 120px 0 80px; text-align: center;">
            <h1 class="gradient-text">Help Center</h1>
            <p class="hero-subtitle">Find answers to your questions and get support</p>
        </div>

        <div class="contact-content" style="max-width: 1000px; margin: 0 auto; padding: 2rem 0;">
            <!-- Content for Help Center goes here -->
            <div class="card">
                <h2>Frequently Asked Questions</h2>
                <div style="margin-top: 2rem;">
                    <div style="margin-bottom: 1.5rem;">
                        <h3>How do I create a resume?</h3>
                        <p>Our AI resume builder guides you step-by-step. Simply fill in your information, and our AI will provide suggestions to optimize your resume.</p>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <h3>Can I customize my resume?</h3>
                        <p>Yes, you can customize every aspect of your resume, from templates and fonts to colors and sections. Our builder offers extensive customization options.</p>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <h3>What if I need help?</h3>
                        <p>You can contact our support team via email, live chat, or phone. Visit our Contact Us page for more details.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>