<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Resume Builder - Create Professional Resumes</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="image/2.jpg" type="image/x-icon">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="gradient-text">AI-Powered</span> Resume Builder
                </h1>
                <p class="hero-subtitle">
                    Create stunning, professional resumes in minutes with our AI assistant. 
                    Choose from multiple templates designed for every career stage.
                </p>
                <div class="hero-features">
                    <div class="feature">
                        <i class="fas fa-robot"></i>
                        <span>AI-Powered</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-palette"></i>
                        <span>Multiple Templates</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-download"></i>
                        <span>PDF Export</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>ATS Friendly</span>
                    </div>
                </div>
                <div class="hero-buttons">
                    <a href="auth/register.php" class="btn btn-primary">
                        <i class="fas fa-rocket"></i>
                        Get Started Free
                    </a>
                    <a href="resume_builder.php" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        View Templates
                    </a>
                </div>
                <br><br>
            </div>
            <div class="hero-image">
                <div class="resume-preview">
                    <div class="preview-header">
                        <div class="preview-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="preview-content">
                        <div class="preview-line"></div>
                        <div class="preview-line short"></div>
                        <div class="preview-line"></div>
                        <div class="preview-line short"></div>
                        <div class="preview-line"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <section class="templates-section">
        <div class="container">
            <h2 class="section-title">Choose Your Perfect Template</h2>
            <div class="templates-grid">
                <div class="template-card">
                    <div class="template-preview simple"></div>
                    <h3>Simple</h3>
                    <p>Clean and minimal design for any industry</p>
                    <a href="resume_builder.php?template=simple" class="btn btn-outline">Use Template</a>
                </div>
                <div class="template-card">
                    <div class="template-preview ats"></div>
                    <h3>ATS Friendly</h3>
                    <p>Optimized for Applicant Tracking Systems</p>
                    <a href="resume_builder.php?template=ats" class="btn btn-outline">Use Template</a>
                </div>
                
                <div class="template-card">
                    <div class="template-preview executive"></div>
                    <h3>Executive</h3>
                    <p>Sophisticated design for senior positions</p>
                    <a href="resume_builder.php?template=executive" class="btn btn-outline">Use Template</a>
                </div>
                <div class="template-card">
                    <div class="template-preview creative"></div>
                    <h3>Creative</h3>
                    <p>Stand out with unique design elements</p>
                    <a href="resume_builder.php?template=creative" class="btn btn-outline">Use Template</a>
                </div>
                <div class="template-card">
                    <div class="template-preview modern"></div>
                    <h3>Modern</h3>
                    <p>Contemporary layout with visual appeal</p>
                    <a href="resume_builder.php?template=modern" class="btn btn-outline">Use Template</a>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose Our Resume Builder?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-magic"></i>
                    </div>
                    <h3>AI-Powered Suggestions</h3>
                    <p>Get intelligent recommendations for content, keywords, and formatting based on your industry and experience level.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Responsive</h3>
                    <p>Create and edit your resume on any device. Our responsive design works perfectly on desktop, tablet, and mobile.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h3>PDF Export</h3>
                    <p>Download your resume as a high-quality PDF file that maintains perfect formatting across all devices and platforms.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-save"></i>
                    </div>
                    <h3>Save & Edit</h3>
                    <p>Save multiple versions of your resume and edit them anytime. Never lose your work with our auto-save feature.</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html> 