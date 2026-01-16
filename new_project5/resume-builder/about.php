<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="image/2.jpg" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="about-hero" style="padding: 120px 0 80px; text-align: center;">
            <h1 class="gradient-text">About ResumeAI</h1>
            <p class="hero-subtitle">Empowering professionals to create stunning resumes with AI assistance</p>
        </div>

        <div class="about-content" style="max-width: 800px; margin: 0 auto; padding: 2rem 0;">
            <div class="card" style="margin-bottom: 2rem;">
                <h2>Our Mission</h2>
                <p>At ResumeAI, we believe that everyone deserves to have a professional, compelling resume that showcases their skills and experience effectively. Our AI-powered platform combines cutting-edge technology with beautiful design to help job seekers stand out in today's competitive market.</p>
            </div>

            <div class="card" style="margin-bottom: 2rem;">
                <h2>Why Choose ResumeAI?</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                    <div>
                        <h3><i class="fas fa-robot" style="color: var(--primary-color);"></i> AI-Powered</h3>
                        <p>Get intelligent suggestions for content, keywords, and formatting based on your industry and experience level.</p>
                    </div>
                    <div>
                        <h3><i class="fas fa-palette" style="color: var(--primary-color);"></i> Multiple Templates</h3>
                        <p>Choose from a variety of professionally designed templates that suit different industries and career stages.</p>
                    </div>
                    <div>
                        <h3><i class="fas fa-mobile-alt" style="color: var(--primary-color);"></i> Mobile Responsive</h3>
                        <p>Create and edit your resume on any device with our fully responsive design.</p>
                    </div>
                    <div>
                        <h3><i class="fas fa-shield-alt" style="color: var(--primary-color);"></i> ATS Optimized</h3>
                        <p>Our templates are designed to pass through Applicant Tracking Systems effectively.</p>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 2rem;">
                <h2>Our Story</h2>
                <p>ResumeAI was born from the frustration of job seekers struggling to create professional resumes that truly represent their skills and experience. We recognized that while there were many resume builders available, none combined the power of AI with beautiful, customizable templates.</p>
                <p>Our team of designers, developers, and HR professionals came together to create a platform that not only looks great but also helps users create content that resonates with hiring managers and ATS systems.</p>
            </div>

            <div class="card">
                <h2>Get Started Today</h2>
                <p>Join thousands of professionals who have already created amazing resumes with ResumeAI. Start building your professional future today!</p>
                <div style="margin-top: 2rem;">
                    <a href="auth/register.php" class="btn btn-primary">
                        <i class="fas fa-rocket"></i>
                        Get Started Free
                    </a>
                    <a href="resume_builder.php" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        View Templates
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html> 