<?php
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('auth/login.php', 'Please login to download resumes.');
}

$resume_id = $_GET['id'] ?? null;

if (!$resume_id) {
    redirect('dashboard.php', 'Resume ID is required.', 'error');
}

// Get resume data
$resume_data = getResume($pdo, $resume_id, $_SESSION['user_id']);

if (!$resume_data) {
    redirect('dashboard.php', 'Resume not found.', 'error');
}

$availableTemplates = ['simple', 'ats', 'executive', 'creative', 'modern'];
$template = isset($_GET['template']) && in_array($_GET['template'], $availableTemplates, true) ? $_GET['template'] : $resume_data['template'];
if (!in_array($template, $availableTemplates, true)) {
    $template = 'simple';
}

$content = $resume_data['content'];

function generateTemplatePreview($template, $data) {
    $html = '';

    // Helper function to safely get data
    $get = function($arr, $key, $default = '') {
        if (!isset($arr[$key])) {
            return $default;
        }
        $value = $arr[$key];
        if (is_scalar($value)) {
            return htmlspecialchars((string)$value);
        } elseif (is_array($value)) {
            return $value;
        } else {
            return $default;
        }
    };

    // Common sections
    $personalInfo = $get($data, 'personal_info', []);
    $summary = $get($data, 'summary');
    $experience = $get($data, 'experience', []);
    $education = $get($data, 'education', []);
    $skills = $get($data, 'skills', []);

    switch ($template) {
        case 'simple':
            $html .= '<div class="resume-template simple-template">';
            $html .= '<div class="header-section">';
            $html .= '<h1>' . $get($personalInfo, 'name') . '</h1>';
            $html .= '<p class="contact-info">' . $get($personalInfo, 'email') . ' | ' . $get($personalInfo, 'phone') . ' | ' . $get($personalInfo, 'linkedin') . '</p>';
            $html .= '</div>';




             if (!empty($summary)) {
                $html .= '<div class="section"><h2>Summary</h2><p>' . $summary . '</p></div>';
            }

            if (!empty($experience)) {
                $html .= '<div class="section"><h2>Experience</h2>';
                foreach ($experience as $exp) {
                    $description = $get($exp, 'description');
                    if (is_array($description)) {
                        $descriptionHtml = '<ul>';
                        foreach ($description as $descItem) {
                            $descriptionHtml .= '<li>' . htmlspecialchars($descItem) . '</li>';
                        }
                        $descriptionHtml .= '</ul>';
                    } else {
                        $descriptionHtml = '<p>' . htmlspecialchars($description) . '</p>';
                    }
                    $html .= '<div class="item"><h3>' . $get($exp, 'title') . '</h3><p class="sub-info">' . $get($exp, 'company') . ' | ' . $get($exp, 'years') . '</p>' . $descriptionHtml . '</div>';
                }
                $html .= '</div>';
            }

            if (!empty($education)) {
                $html .= '<div class="section"><h2>Education</h2>';
                foreach ($education as $edu) {
                    $html .= '<div class="item"><h3>' . $get($edu, 'degree') . '</h3><p class="sub-info">' . $get($edu, 'university') . ' | ' . $get($edu, 'years') . '</p></div>';
                }
                $html .= '</div>';
            }

            if (!empty($skills)) {
                $skillsHtml = is_array($skills) ? '<p>' . implode(', ', $skills) . '</p>' : '<p>' . $skills . '</p>';
                $html .= '<div class="section"><h2>Skills</h2>' . $skillsHtml . '</div>';
            }


            $html .= '</div>';
            break;
        case 'ats':
            $html .= '<div class="resume-template ats-template">';
            $html .= '<div class="header-section">';
            $html .= '<h1>' . $get($personalInfo, 'name') . '</h1>';
            $html .= '<p class="contact-info">' . $get($personalInfo, 'email') . ' | ' . $get($personalInfo, 'phone') . ' | ' . $get($personalInfo, 'linkedin') . '</p>';
            $html .= '</div>';

            if (!empty($summary)) {
                $html .= '<div class="section"><h2>Summary</h2><p>' . $summary . '</p></div>';
            }

            if (!empty($experience)) {
                $html .= '<div class="section"><h2>Experience</h2>';
                foreach ($experience as $exp) {
                    $description = $get($exp, 'description');
                    if (is_array($description)) {
                        $descriptionHtml = '<ul>';
                        foreach ($description as $descItem) {
                            $descriptionHtml .= '<li>' . htmlspecialchars($descItem) . '</li>';
                        }
                        $descriptionHtml .= '</ul>';
                    } else {
                        $descriptionHtml = '<p>' . htmlspecialchars($description) . '</p>';
                    }
                    $html .= '<div class="item"><h3>' . $get($exp, 'title') . ' at ' . $get($exp, 'company') . '</h3><p class="sub-info">' . $get($exp, 'years') . '</p>' . $descriptionHtml . '</div>';
                }
                $html .= '</div>';
            }

            if (!empty($education)) {
                $html .= '<div class="section"><h2>Education</h2>';
                foreach ($education as $edu) {
                    $html .= '<div class="item"><h3>' . $get($edu, 'degree') . ' from ' . $get($edu, 'university') . '</h3><p class="sub-info">' . $get($edu, 'years') . '</p></div>';
                }
                $html .= '</div>';
            }

            if (!empty($skills)) {
                $skillsHtml = is_array($skills) ? '<p>' . implode(', ', $skills) . '</p>' : '<p>' . $skills . '</p>';
                $html .= '<div class="section"><h2>Skills</h2>' . $skillsHtml . '</div>';
            }


            $html .= '</div>';
            break;
        case 'executive':
            $html .= '<div class="resume-template executive-template">';
            $html .= '<div class="header-section">';
            $html .= '<h1>' . $get($personalInfo, 'name') . '</h1>';
            $html .= '<p class="tagline">' . $get($personalInfo, 'tagline') . '</p>';
            $html .= '<p class="contact-info">' . $get($personalInfo, 'email') . ' | ' . $get($personalInfo, 'phone') . ' | ' . $get($personalInfo, 'linkedin') . '</p>';
            $html .= '</div>';

            if (!empty($summary)) {
                $html .= '<div class="section"><h2>Executive Summary</h2><p>' . $summary . '</p></div>';
            }

            if (!empty($experience)) {
                $html .= '<div class="section"><h2>Professional Experience</h2>';
                foreach ($experience as $exp) {
                    $description = $get($exp, 'description');
                    if (is_array($description)) {
                        $descriptionHtml = '<ul>';
                        foreach ($description as $descItem) {
                            $descriptionHtml .= '<li>' . htmlspecialchars($descItem) . '</li>';
                        }
                        $descriptionHtml .= '</ul>';
                    } else {
                        $descriptionHtml = '<p>' . htmlspecialchars($description) . '</p>';
                    }
                    $html .= '<div class="item"><h3>' . $get($exp, 'title') . '</h3><p class="sub-info">' . $get($exp, 'company') . ' | ' . $get($exp, 'years') . '</p>' . $descriptionHtml . '</div>';
                }
                $html .= '</div>';
            }

            if (!empty($education)) {
                $html .= '<div class="section"><h2>Education</h2>';
                foreach ($education as $edu) {
                    $html .= '<div class="item"><h3>' . $get($edu, 'degree') . '</h3><p class="sub-info">' . $get($edu, 'university') . ' | ' . $get($edu, 'years') . '</p></div>';
                }
                $html .= '</div>';
            }

            if (!empty($skills)) {
                $skillsHtml = is_array($skills) ? '<p>' . implode(', ', $skills) . '</p>' : '<p>' . $skills . '</p>';
                $html .= '<div class="section"><h2>Core Competencies</h2>' . $skillsHtml . '</div>';
            }

            $keyAchievements = $get($data, 'key_achievements', []);
            if (!empty($keyAchievements)) {
                $html .= '<div class="section"><h2>Key Achievements</h2><ul>';
                foreach ($keyAchievements as $achievement) {
                    $html .= '<li>' . htmlspecialchars($achievement) . '</li>';
                }
                $html .= '</ul></div>';
            }
            $html .= '</div>';
            break;
        case 'creative':
            $html .= '<div class="resume-template creative-template">';
            $html .= '<div class="header-section">';
            $html .= '<h1>' . $get($personalInfo, 'name') . '</h1>';
            $html .= '<p class="tagline">' . $get($personalInfo, 'tagline') . '</p>';
            $html .= '<p class="contact-info">' . $get($personalInfo, 'email') . ' | ' . $get($personalInfo, 'phone') . ' | ' . $get($personalInfo, 'linkedin') . '</p>';
            $html .= '</div>';

            if (!empty($summary)) {
                $html .= '<div class="section"><h2>About Me</h2><p>' . $summary . '</p></div>';
            }

            if (!empty($experience)) {
                $html .= '<div class="section"><h2>Work History</h2>';
                foreach ($experience as $exp) {
                    $description = $get($exp, 'description');
                    if (is_array($description)) {
                        $descriptionHtml = '<ul>';
                        foreach ($description as $descItem) {
                            $descriptionHtml .= '<li>' . htmlspecialchars($descItem) . '</li>';
                        }
                        $descriptionHtml .= '</ul>';
                    } else {
                        $descriptionHtml = '<p>' . htmlspecialchars($description) . '</p>';
                    }
                    $html .= '<div class="item"><h3>' . $get($exp, 'title') . ' at ' . $get($exp, 'company') . '</h3><p class="sub-info">' . $get($exp, 'years') . '</p>' . $descriptionHtml . '</div>';
                }
                $html .= '</div>';
            }

            if (!empty($education)) {
                $html .= '<div class="section"><h2>Education</h2>';
                foreach ($education as $edu) {
                    $html .= '<div class="item"><h3>' . $get($edu, 'degree') . ' from ' . $get($edu, 'university') . '</h3><p class="sub-info">' . $get($edu, 'years') . '</p></div>';
                }
                $html .= '</div>';
            }

            if (!empty($skills)) {
                $skillsHtml = is_array($skills) ? '<p>' . implode(', ', $skills) . '</p>' : '<p>' . $skills . '</p>';
                $html .= '<div class="section"><h2>Skills</h2>' . $skillsHtml . '</div>';
            }

            $keyAchievements = $get($data, 'key_achievements', []);
            if (!empty($keyAchievements)) {
                $html .= '<div class="section"><h2>Key Achievements</h2><ul>';
                foreach ($keyAchievements as $achievement) {
                    $html .= '<li>' . htmlspecialchars($achievement) . '</li>';
                }
                $html .= '</ul></div>';
            }
            $html .= '</div>';
            break;
        case 'modern':
            $html .= '<div class="resume-template modern-template">';
            $html .= '<div class="header-section">';
            $html .= '<h1>' . $get($personalInfo, 'name') . '</h1>';
            $html .= '<p class="title">' . $get($personalInfo, 'title') . '</p>';
            $html .= '<p class="contact-info">' . $get($personalInfo, 'email') . ' | ' . $get($personalInfo, 'phone') . ' | ' . $get($personalInfo, 'linkedin') . '</p>';
            $html .= '</div>';

            if (!empty($summary)) {
                $html .= '<div class="section"><h2>Profile</h2><p>' . $summary . '</p></div>';
            }

            if (!empty($experience)) {
                $html .= '<div class="section"><h2>Experience</h2>';
                foreach ($experience as $exp) {
                    $description = $get($exp, 'description');
                    if (is_array($description)) {
                        $descriptionHtml = '<ul>';
                        foreach ($description as $descItem) {
                            $descriptionHtml .= '<li>' . htmlspecialchars($descItem) . '</li>';
                        }
                        $descriptionHtml .= '</ul>';
                    } else {
                        $descriptionHtml = '<p>' . htmlspecialchars($description) . '</p>';
                    }
                    $html .= '<div class="item"><h3>' . $get($exp, 'title') . '</h3><p class="sub-info">' . $get($exp, 'company') . ' | ' . $get($exp, 'years') . '</p>' . $descriptionHtml . '</div>';
                }
                $html .= '</div>';
            }

            if (!empty($education)) {
                $html .= '<div class="section"><h2>Education</h2>';
                foreach ($education as $edu) {
                    $html .= '<div class="item"><h3>' . $get($edu, 'degree') . '</h3><p class="sub-info">' . $get($edu, 'university') . ' | ' . $get($edu, 'years') . '</p></div>';
                }
                $html .= '</div>';
            }

            if (!empty($skills)) {
                $skillsHtml = is_array($skills) ? '<p>' . implode(', ', $skills) . '</p>' : '<p>' . $skills . '</p>';
                $html .= '<div class="section"><h2>Skills</h2>' . $skillsHtml . '</div>';
            }

            $keyAchievements = $get($data, 'key_achievements', []);
            if (!empty($keyAchievements)) {
                $html .= '<div class="section"><h2>Key Achievements</h2><ul>';
                foreach ($keyAchievements as $achievement) {
                    $html .= '<li>' . htmlspecialchars($achievement) . '</li>';
                }
                $html .= '</ul></div>';
            }
            $html .= '</div>';
            break;
        default:
            $html .= '<p>Template not found.</p>';
            break;
    }
    return $html;
}

// Start output buffering to capture HTML
ob_start();

// Generate the HTML content using the PHP function
$resumeHtmlContent = generateTemplatePreview($template, $content);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($resume_data['title']); ?> - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Print-friendly styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
                font-size: 12pt;
                line-height: 1.4;
            }
            

            
            .preview-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .resume-content {
                padding: 0.5in;
            }
            
            .resume-header {
                margin-bottom: 1.5rem;
            }
            
            .resume-section {
                margin-bottom: 1.5rem;
                page-break-inside: avoid;
            }
            
            .experience-item, .education-item {
                margin-bottom: 1rem;
                page-break-inside: avoid;
            }
            
            h1 {
                font-size: 24pt;
                margin-bottom: 0.5rem;
            }
            
            h2 {
                font-size: 16pt;
                margin-bottom: 0.75rem;
                border-bottom: 2px solid #333;
                padding-bottom: 0.25rem;
            }
            
            h3 {
                font-size: 14pt;
                margin-bottom: 0.5rem;
            }
            
            p {
                margin-bottom: 0.5rem;
            }
            
            .company, .school {
                font-weight: 600;
                color: #333;
            }
            
            .dates {
                color: #666;
                font-style: italic;
            }
            
            /* Hide any navigation or unnecessary elements */
            nav, .header, .footer, .sidebar {
                display: none !important;
            }
        }
        
        body {
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            font-family: 'Inter', sans-serif;
        }
        /* Screen styles */
        
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .download-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .download-header h1 {
            margin: 0;
            font-size: 1.25rem;
        }
        
        .download-actions {
            display: flex;
            gap: 1rem;
        }
        
        .download-actions .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: white;
            color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: #f8f9fa;
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 1px solid white;
        }
        
        .btn-outline:hover {
            background: white;
            color: var(--primary-color);
        }
        
        .resume-content {
            padding: 2rem;
            min-height: 800px;
        }
        
        .resume-content h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        
        .resume-content h2 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.25rem;
</style>
</head>
<body>
    <div class="preview-container">

        
        <div class="download-header">
            <h1>
                <i class="fas fa-file-alt"></i>
                Download: <?php echo htmlspecialchars($resume_data['title']); ?>
            </h1>
            <div class="download-actions">
                <a href="dashboard.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
                <a href="resume_builder.php?id=<?php echo $resume_id; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit Resume
                </a>
                <button onclick="window.print()" class="btn btn-outline">
                    <i class="fas fa-download"></i>
                    Download PDF
                </button>
            </div>
        </div>
        
        <div class="resume-content" id="resume-content">
            <?php echo $resumeHtmlContent; ?>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Generate the resume content
        document.addEventListener('DOMContentLoaded', function() {
            const template = '<?php echo $template; ?>';
            const data = <?php echo json_encode($content); ?>;
            console.log('Resume Data:', data);

            if (typeof window.ResumeAI !== 'undefined' && window.ResumeAI.generateTemplatePreview) {
                const resumeContentDiv = document.querySelector('.resume-content');
                if (resumeContentDiv) {
                    resumeContentDiv.innerHTML = window.ResumeAI.generateTemplatePreview(template, data);
                }
            }
        });
    </script>
</body>
</html>
<?php
$html = ob_get_clean();

require_once 'vendor/autoload.php';
use Dompdf\Dompdf;

// Create a Dompdf instance
$html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Resume</title><link rel="stylesheet" href="assets/css/style.css"></head><body>' . $html . '</body></html>';

$dompdf = new Dompdf();

// Load HTML to Dompdf
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream(htmlspecialchars($resume_data['title']) . '.pdf', array("Attachment" => false));

exit();
?>                color: #333;
            }
            
            .dates {
                color: #666;
                font-style: italic;
            }
            
            /* Hide any navigation or unnecessary elements */
            nav, .header, .footer, .sidebar {
                display: none !important;
            }
        }
        
        body {
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            font-family: 'Inter', sans-serif;
        }
        /* Screen styles */
        
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .download-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .download-header h1 {
            margin: 0;
            font-size: 1.25rem;
        }
        
        .download-actions {
            display: flex;
            gap: 1rem;
        }
        
        .download-actions .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: white;
            color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: #f8f9fa;
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 1px solid white;
        }
        
        .btn-outline:hover {
            background: white;
            color: var(--primary-color);
        }
        
        .resume-content {
            padding: 2rem;
            min-height: 800px;
        }
        
        .resume-content h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        
        .resume-content h2 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.25rem;
        }
        
        .resume-content h3 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        
        .resume-content p {
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }
        
        .resume-content .company,
        .resume-content .school {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .resume-content .dates {
            color: var(--text-secondary);
            font-style: italic;
        }
        
        @media (max-width: 768px) {
            .download-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .download-actions {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .resume-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="preview-container">

        
        <div class="download-header">
            <h1>
                <i class="fas fa-file-alt"></i>
                Download: <?php echo htmlspecialchars($resume_data['title']); ?>
            </h1>
            <div class="download-actions">
                <a href="dashboard.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
                <a href="resume_builder.php?id=<?php echo $resume_id; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit Resume
                </a>
                <button onclick="window.print()" class="btn btn-outline">
                    <i class="fas fa-download"></i>
                    Download PDF
                </button>
            </div>
        </div>
        
        <div class="resume-content" id="resume-content">
            <!-- Resume content will be generated here -->
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Generate the resume content
        document.addEventListener('DOMContentLoaded', function() {
            const template = '<?php echo $template; ?>';
            const data = <?php echo json_encode($content); ?>;
            console.log('Resume Data:', data);

            
            if (typeof window.ResumeAI !== 'undefined' && window.ResumeAI.generateTemplatePreview) {
                window.ResumeAI.generateTemplatePreview(template, data);
            }
        });
    </script>
</body>
</html>