<?php
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('auth/login.php', 'Please login to preview resumes.');
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

$template = $resume_data['template'];
$availableTemplates = ['simple', 'ats', 'executive', 'creative', 'modern'];
if (!in_array($template, $availableTemplates, true)) {
    $template = 'simple';
}
$content = $resume_data['content'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview - <?php echo htmlspecialchars($resume_data['title']); ?> - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            font-family: 'Inter', sans-serif;
        }
        
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .preview-header {
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .preview-header h1 {
            margin: 0;
            font-size: 1.25rem;
        }
        
        .preview-actions {
            display: flex;
            gap: 1rem;
        }
        
        .preview-actions .btn {
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
        
        @media print {
            .preview-header {
                display: none;
            }
            
            body {
                padding: 0;
                background: white;
            }
            
            .preview-container {
                box-shadow: none;
                border-radius: 0;
            }
        }
        
        @media (max-width: 768px) {
            .preview-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .preview-actions {
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
        <div class="preview-header">
            <h1>
                <i class="fas fa-download"></i>
                Download: <?php echo htmlspecialchars($resume_data['title']); ?>
            </h1>
            <div class="preview-actions">
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
        
        <div class="resume-content" id="resume-preview">
            <!-- Resume content will be generated here -->
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Generate the resume preview
        document.addEventListener('DOMContentLoaded', function() {
            const template = '<?php echo $template; ?>';
            const data = <?php echo json_encode($content); ?>;
            
            if (typeof window.ResumeAI !== 'undefined' && window.ResumeAI.generateTemplatePreview) {
                window.ResumeAI.generateTemplatePreview(template, data);
            } else {
                // Fallback preview
                const preview = document.getElementById('resume-preview');
                preview.innerHTML = `
                    <div class="resume-preview-fallback">
                        <h2>${data.personal?.name || 'Your Name'}</h2>
                        <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                        <p>${data.personal?.location || 'Location'}</p>
                        <hr>
                        <h3>Summary</h3>
                        <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
                        <h3>Experience</h3>
                        <p>Work experience will appear here...</p>
                        <h3>Education</h3>
                        <p>Education details will appear here...</p>
                        <h3>Skills</h3>
                        <p>Skills will appear here...</p>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>