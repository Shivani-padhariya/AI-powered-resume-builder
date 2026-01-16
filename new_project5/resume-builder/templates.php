<?php
require_once 'includes/functions.php';

// Get all available templates
$templates = [
    'simple' => [
        'name' => 'Simple',
        'description' => 'Clean and minimal design for any industry. Perfect for traditional companies and conservative fields.',
        'features' => ['Clean typography', 'Professional layout', 'ATS friendly', 'Easy to read'],
        'best_for' => 'Traditional industries, conservative companies, entry-level positions',
        'color' => '#f3f4f6',
        'icon' => 'fas fa-file-alt',
        'preview_html' => '<div class="template-preview-content simple-preview">
            <div class="preview-header">
                <h2>Ethan Carter</h2>
                <p>Software Developer</p>
                <p>ethan.carter@outlook.com | (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>Professional Summary</h3>
                <p>Experienced software developer with expertise in full-stack development...</p>
            </div>
            <div class="preview-section">
                <h3>Work Experience</h3>
                <div class="preview-item">
                    <h4>Senior Developer</h4>
                    <p>Tech Company Inc. | 2020-2023</p>
                </div>
            </div>
        </div>'
    ],
    'ats' => [
        'name' => 'ATS Friendly',
        'description' => 'Optimized for Applicant Tracking Systems. Uses standard formatting and keywords that ATS software can easily parse.',
        'features' => ['ATS optimized', 'Standard formatting', 'Keyword friendly', 'High parsing rate'],
        'best_for' => 'Large corporations, online applications, ATS-heavy industries',
        'color' => '#dbeafe',
        'icon' => 'fas fa-search',
        'preview_html' => '<div class="template-preview-content ats-preview">
            <div class="preview-header">
                <h2>Olivia Bennett</h2>
                <p>Software Developer</p>
                <p>olivia.bennett@gmail.com| (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>SUMMARY</h3>
                <p>Experienced software developer with expertise in full-stack development...</p>
            </div>
            <div class="preview-section">
                <h3>PROFESSIONAL EXPERIENCE</h3>
                <div class="preview-item">
                    <h4>Senior Developer</h4>
                    <p>Tech Company Inc. | 2020-2023</p>
                </div>
            </div>
        </div>'
    ],
    'executive' => [
        'name' => 'Executive',
        'description' => 'Sophisticated design for senior positions. Emphasizes leadership, achievements, and strategic thinking.',
        'features' => ['Executive layout', 'Leadership focus', 'Achievement oriented', 'Strategic design'],
        'best_for' => 'Senior positions, C-level roles, management positions',
        'color' => '#e0e7ff',
        'icon' => 'fas fa-crown',
        'preview_html' => '<div class="template-preview-content executive-preview">
            <div class="preview-header">
                <h2>James Whitman</h2>
                <p class="executive-title">Executive Professional</p>
                <p>james.whitman@gmail.com | (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>EXECUTIVE SUMMARY</h3>
                <p>Accomplished executive with proven track record of delivering complex projects...</p>
            </div>
        </div>'
    ],
    'creative' => [
        'name' => 'Creative',
        'description' => 'Stand out with unique design elements. Perfect for creative professionals who want to showcase their artistic side.',
        'features' => ['Creative design', 'Unique elements', 'Visual impact', 'Artistic layout'],
        'best_for' => 'Creative industries, design roles, marketing positions',
        'color' => '#fce7f3',
        'icon' => 'fas fa-palette',
        'preview_html' => '<div class="template-preview-content creative-preview">
            <div class="preview-header">
                <h2>Sophia Clarke</h2>
                <p>Creative Professional</p>
                <p>sophia.clarke@gmail.com| (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>About Me</h3>
                <p>Creative professional with passion for innovative design solutions...</p>
            </div>
        </div>'
    ],
    'modern' => [
        'name' => 'Modern',
        'description' => 'Contemporary layout with visual appeal. Clean lines and modern typography for the tech-savvy professional.',
        'features' => ['Modern design', 'Clean lines', 'Contemporary typography', 'Tech-friendly'],
        'best_for' => 'Technology companies, startups, modern organizations',
        'color' => '#d1fae5',
        'icon' => 'fas fa-rocket',
        'preview_html' => '<div class="template-preview-content modern-preview">
            <div class="preview-header">
                <h2>Nathan Brooks</h2>
                <p>Software Developer</p>
                <p>nathanbrooks@gmail.com | (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>Summary</h3>
                <p>Modern software developer with expertise in cutting-edge technologies...</p>
            </div>
        </div>'
    ],
    'minimalist' => [
        'name' => 'Minimalist',
        'description' => 'Ultra-clean design with maximum focus on content. Perfect for professionals who prefer simplicity.',
        'features' => ['Ultra-clean', 'Content-focused', 'Minimal distractions', 'Professional'],
        'best_for' => 'Design professionals, consultants, minimalist preferences',
        'color' => '#f3f4f6',
        'icon' => 'fas fa-minus',
        'preview_html' => '<div class="template-preview-content minimalist-preview">
            <div class="preview-header">
                <h2>Emily Foster</h2>
                <p>UX Designer</p>
                <p>emilyfoster@gmail.com | (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>About</h3>
                <p>Passionate UX designer focused on creating intuitive user experiences...</p>
            </div>
        </div>'
    ],
    'corporate' => [
        'name' => 'Corporate',
        'description' => 'Traditional corporate style with professional formatting. Ideal for established companies and conservative industries.',
        'features' => ['Corporate style', 'Traditional layout', 'Professional', 'Conservative'],
        'best_for' => 'Corporate environments, finance, legal, healthcare',
        'color' => '#e5e7eb',
        'icon' => 'fas fa-building',
        'preview_html' => '<div class="template-preview-content corporate-preview">
            <div class="preview-header">
                <h2>Liam Turner</h2>
                <p>Financial Analyst</p>
                <p>liamturner@gmail.com | (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>Professional Summary</h3>
                <p>Experienced financial analyst with strong analytical skills...</p>
            </div>
        </div>'
    ],
    'creative-pro' => [
        'name' => 'Creative Pro',
        'description' => 'Advanced creative design with unique visual elements. Stand out in creative industries.',
        'features' => ['Advanced design', 'Visual elements', 'Creative layout', 'Unique style'],
        'best_for' => 'Creative directors, artists, marketing professionals',
        'color' => '#fce7f3',
        'icon' => 'fas fa-paint-brush',
        'preview_html' => '<div class="template-preview-content creative-pro-preview">
            <div class="preview-header">
                <h2>Ryan Cooper</h2>
                <p>Creative Director</p>
                <p>ryancooper@gmail.com | (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>Creative Vision</h3>
                <p>Creative director with passion for innovative design solutions...</p>
            </div>
        </div>'
    ],
    'tech-savvy' => [
        'name' => 'Tech Savvy',
        'description' => 'Technology-focused design with modern elements. Perfect for IT professionals and developers.',
        'features' => ['Tech-focused', 'Modern elements', 'Developer-friendly', 'Innovative'],
        'best_for' => 'Software developers, IT professionals, tech startups',
        'color' => '#dbeafe',
        'icon' => 'fas fa-code',
        'preview_html' => '<div class="template-preview-content tech-savvy-preview">
            <div class="preview-header">
                <h2>Benjamin Hayes</h2>
                <p>Full Stack Developer</p>
                <p>benjaminhayes@gmail.com | (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>Technical Summary</h3>
                <p>Full stack developer with expertise in modern web technologies...</p>
            </div>
        </div>'
    ],
    'elegant' => [
        'name' => 'Elegant',
        'description' => 'Sophisticated design with refined typography and spacing. For professionals who appreciate elegance.',
        'features' => ['Sophisticated', 'Refined typography', 'Elegant spacing', 'Premium look'],
        'best_for' => 'Senior professionals, luxury brands, premium services',
        'color' => '#fef3c7',
        'icon' => 'fas fa-gem',
        'preview_html' => '<div class="template-preview-content elegant-preview">
            <div class="preview-header">
                <h2>Charlotte Evans</h2>
                <p>Senior Consultant</p>
                <p>charlotteevans@gmail.com| (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>Professional Profile</h3>
                <p>Senior consultant with proven track record of delivering results...</p>
            </div>
        </div>'
    ],
    'startup' => [
        'name' => 'Startup',
        'description' => 'Dynamic and energetic design perfect for startup culture. Bold and innovative approach.',
        'features' => ['Dynamic', 'Energetic', 'Bold design', 'Innovative'],
        'best_for' => 'Startup founders, entrepreneurs, innovative companies',
        'color' => '#ecfdf5',
        'icon' => 'fas fa-lightbulb',
        'preview_html' => '<div class="template-preview-content startup-preview">
            <div class="preview-header">
                <h2>Henry Collins</h2>
                <p>Startup Founder</p>
                <p>henrycollins@gmail.com | (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>Vision</h3>
                <p>Entrepreneurial leader with passion for innovation and growth...</p>
            </div>
        </div>'
    ],
    'academic' => [
        'name' => 'Academic',
        'description' => 'Scholarly design suitable for academic and research positions. Clean and professional.',
        'features' => ['Academic style', 'Research-focused', 'Professional', 'Scholarly'],
        'best_for' => 'Researchers, academics, educational institutions',
        'color' => '#f1f5f9',
        'icon' => 'fas fa-graduation-cap',
        'preview_html' => '<div class="template-preview-content academic-preview">
            <div class="preview-header">
                <h2>Isla Thompson</h2>
                <p>Research Scientist</p>
                <p>islathompson@gmail.com| (555) 123-4567</p>
            </div>
            <div class="preview-section">
                <h3>Research Focus</h3>
                <p>Dedicated researcher with expertise in scientific methodology...</p>
            </div>
        </div>'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Templates - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="image/2.jpg" type="image/x-icon">
    <style>
        body {
            background: #18181b;
            color: #f3f4f6;
        }
        .templates-container {
            background: linear-gradient(135deg, var(--bg-tertiary) 0%, #1f2937 100%);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(99, 102, 241, 0.2);
            margin-top: 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        .templates-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4);
            border-radius: 20px 20px 0 0;
        }
        .templates-title-section {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            padding-top: 1rem;
        }
        .templates-title-section .page-title {
            margin-top: 0;
            margin-bottom: 1rem;
            color: #fff;
            font-size: 2.2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .section-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .section-subtitle {
            font-size: 1.1rem;
            color: var(--text-secondary);
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
        }
        /* Remove custom nav and container spacing styles to match About page */
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff; /* Use white for dark backgrounds */
            margin-bottom: 2rem;
            margin-top: 2rem;
            text-align: center;
            letter-spacing: 0.5px;
            z-index: 2;
            position: relative;
            width: 100%;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .templates-hero .page-title {
            color: #fff;
        }
        .templates-hero {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%) !important;
            border-radius: 20px !important;
            padding: 3rem 2rem !important;
            margin-bottom: 3rem !important;
            text-align: center !important;
            border: 1px solid rgba(99, 102, 241, 0.3) !important;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2) !important;
            max-width: 900px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            min-height: 160px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            position: relative !important;
            backdrop-filter: blur(10px) !important;
            transition: all 0.3s ease !important;
        }
        .templates-hero:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.3) !important;
        }
        .page-title {
            font-size: 2.3rem !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            margin-bottom: 1.5rem !important;
            margin-top: 0.5rem !important;
            text-align: center !important;
            letter-spacing: 0.5px !important;
            z-index: 2 !important;
            position: relative !important;
            width: 100% !important;
            line-height: 1.2 !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        .templates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.2rem;
            margin-bottom: 3rem;
        }
        .template-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            border: 1.5px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            backdrop-filter: blur(10px);
        }
        .template-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .template-card:hover {
            box-shadow: 0 20px 40px rgba(99,102,241,0.2);
            border-color: #6366f1;
            transform: translateY(-8px) scale(1.02);
        }
        .template-card:hover::before {
            opacity: 1;
        }
        .template-preview-container {
            height: 210px;
            border-radius: 16px;
            margin-bottom: 1.3rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1.5px solid rgba(99, 102, 241, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .template-preview-container:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.15);
        }
        .template-preview-content {
            width: 100%;
            height: 100%;
            padding: 1.1rem 1rem 1rem 1rem;
            font-size: 0.85rem;
            line-height: 1.4;
            color: #22223b;
            background: none;
        }
        .template-preview-content h2 {
            font-size: 1.08rem;
            margin-bottom: 0.2rem;
            color: #18181b;
        }
        .template-preview-content h3 {
            font-size: 0.93rem;
            margin-bottom: 0.2rem;
            color: #3730a3;
        }
        .template-preview-content h4 {
            font-size: 0.8rem;
            margin-bottom: 0.1rem;
            color: #52525b;
        }
        .template-preview-content p {
            font-size: 0.75rem;
            margin-bottom: 0.2rem;
            color: #52525b;
        }
        .template-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.1rem;
        }
        .template-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.6rem;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .template-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .template-card:hover .template-icon {
            transform: scale(1.1) rotate(5deg);
        }
        .template-card:hover .template-icon::before {
            opacity: 1;
        }
        .template-info h3 {
            margin-bottom: 0.3rem;
            color: #18181b;
            font-size: 1.08rem;
        }
        .template-description {
            color: #64748b;
            margin-bottom: 1.1rem;
            line-height: 1.5;
            font-size: 0.95rem;
        }
        .template-features {
            margin-bottom: 1.1rem;
        }
        .template-features h4 {
            color: #6366f1;
            margin-bottom: 0.3rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .features-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .feature-tag {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #3730a3;
            padding: 0.4rem 0.8rem;
            border-radius: 25px;
            font-size: 0.75rem;
            border: 1px solid #c7d2fe;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .feature-tag:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        }
        .template-best-for {
            background: linear-gradient(135deg, #f1f5f9 0%, #e0e7ff 100%);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 12px;
            padding: 1rem 1.2rem;
            margin-bottom: 1.1rem;
            box-shadow: 0 2px 10px rgba(99, 102, 241, 0.1);
            transition: all 0.3s ease;
        }
        .template-card:hover .template-best-for {
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
        }
        .template-best-for h4 {
            color: #6366f1;
            margin-bottom: 0.3rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .template-best-for p {
            color: #64748b;
            font-size: 0.85rem;
            margin: 0;
        }
        .template-actions {
            display: flex;
            gap: 1rem;
            margin-top: auto;
        }
        .template-actions .btn {
            flex: 1 1 0;
            font-size: 0.97rem;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .template-actions .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .template-actions .btn:hover::before {
            left: 100%;
        }
        .template-actions .btn-primary {
            background: #6366f1;
            color: #fff;
            border: none;
        }
        .template-actions .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }
        .template-actions .btn-outline {
            background: transparent;
            color: #6366f1;
            border: 1.5px solid #6366f1;
        }
        .template-actions .btn-outline:hover {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }
        .template-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 900px;
            margin: 3rem auto 3rem auto;
            padding: 0 1.5rem;
        }
        .stat-item {
            text-align: center;
            padding: 2.5rem 1rem 1.5rem 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            border: 1.5px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 0;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .stat-item:hover {
            box-shadow: 0 15px 35px rgba(99,102,241,0.2);
            border-color: #6366f1;
            transform: translateY(-5px) scale(1.02);
        }
        .stat-item:hover::before {
            opacity: 1;
        }
        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #6366f1;
            margin-bottom: 0.3rem;
            letter-spacing: 1px;
            word-break: break-word;
        }
        .stat-label {
            font-size: 1.08rem;
            color: #64748b;
            font-weight: 500;
            word-break: break-word;
        }
        @media (max-width: 900px) {
            .templates-grid {
                grid-template-columns: 1fr;
            }
            .template-stats {
                grid-template-columns: 1fr;
                max-width: 500px;
            }
        }
        @media (max-width: 600px) {
            .template-card {
                padding: 1.2rem 0.5rem 1rem 0.5rem;
            }
            .template-preview-container {
                height: 140px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="templates-container">
            <div class="templates-title-section">
            </div>
            <div class="templates-hero">
            <h1 class="page-title">CHOOSE YOUR PERFECT RESUME TEMPLATE</h1>
                <p>Select from our collection of professionally designed templates, each optimized for different industries and career stages.</p>
                
                <div class="template-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count($templates); ?></div>
                        <div class="stat-label">Templates</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">12</div>
                        <div class="stat-label">Industries</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">ATS Ready</div>
                    </div>
                </div>
            </div>
            
            <div class="templates-grid">
            <?php foreach ($templates as $key => $template): ?>
                <div class="template-card" data-template="<?php echo $key; ?>">
                    <div class="template-preview-container">
                        <?php echo $template['preview_html']; ?>
                    </div>
                    
                    <div class="template-header">
                        <div class="template-icon" style="background: <?php echo $template['color']; ?>">
                            <i class="<?php echo $template['icon']; ?>"></i>
                        </div>
                        <div class="template-info">
                            <h3><?php echo $template['name']; ?></h3>
                            <p style="margin: 0; color: var(--text-secondary);"><?php echo $template['description']; ?></p>
                        </div>
                    </div>
                    
                    <div class="template-features">
                        <h4>Key Features</h4>
                        <div class="features-list">
                            <?php foreach ($template['features'] as $feature): ?>
                                <span class="feature-tag"><?php echo $feature; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="template-best-for">
                        <h4>Best For</h4>
                        <p><?php echo $template['best_for']; ?></p>
                    </div>
                    
                    <div class="template-actions">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="resume_builder.php?template=<?php echo $key; ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                                Use This Template
                            </a>
                        <?php else: ?>
                            <a href="auth/register.php" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i>
                                Sign Up to Use
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        
        <div class="card" style="text-align: center; margin-top: 3rem;">
            <h2>Ready to Create Your Professional Resume?</h2>
            <p>Join thousands of professionals who have already created amazing resumes with our AI-powered builder.</p>
            <div style="margin-top: 2rem;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="resume_builder.php" class="btn btn-primary">
                        <i class="fas fa-rocket"></i>
                        Start Building
                    </a>
                <?php else: ?>
                    <a href="auth/register.php" class="btn btn-primary">
                        <i class="fas fa-rocket"></i>
                        Get Started Free
                    </a>
                    <a href="auth/login.php" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html> 