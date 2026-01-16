<?php
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('auth/login.php', 'Please login to access your dashboard.');
}

$user = getCurrentUser($pdo);
$resumes = getUserResumes($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="image/2.jpg" type="image/x-icon">
    <style>
        .page-title {
            font-size: 2.3rem;
            font-weight: 700;
            color: #18181b;
            margin-bottom: 1.5rem;
            margin-top: 0.5rem;
            text-align: center;
        }
        .dashboard-header {
            background: var(--bg-tertiary);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid var(--border-color);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .resumes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .resume-card {
            background: var(--bg-tertiary);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .resume-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .resume-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .resume-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .resume-template {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        
        .resume-date {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
        
        .resume-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.2rem;
            justify-content: flex-start;
        }
        .btn-small {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            padding: 0.55rem 1.1rem;
            font-size: 0.97rem;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.2s, color 0.2s, border 0.2s;
            gap: 0.5rem;
            height: 40px;
            box-sizing: border-box;
        }
        .btn-small i {
            font-size: 1.05em;
        }
        @media (max-width: 600px) {
            .resume-actions {
                flex-direction: column;
                gap: 0.5rem;
                align-items: stretch;
            }
            .btn-small {
                width: 100%;
                min-width: 0;
            }
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <h1 class="page-title">Dashboard</h1>
    <div class="container">
        <?php echo displayMessage(); ?>
        
        <div class="dashboard-header">
            <h1>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h1>
            <p>Manage your resumes and create new ones with AI assistance.</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($resumes); ?></div>
                    <div>Total Resumes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_filter($resumes, fn($r) => $r['is_public'])); ?></div>
                    <div>Public Resumes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_unique(array_column($resumes, 'template'))); ?></div>
                    <div>Templates Used</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo date('M'); ?></div>
                    <div>This Month</div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="resume_builder.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Create New Resume
                </a>
            </div>
        </div>
        
        <div class="resumes-section">
            <h2>Your Resumes</h2>
            
            <?php if (empty($resumes)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h3>No resumes yet</h3>
                    <p>Create your first professional resume to get started.</p>
                    <a href="resume_builder.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Create Resume
                    </a>
                </div>
            <?php else: ?>
                <div class="resumes-grid">
                    <?php foreach ($resumes as $resume): ?>
                        <div class="resume-card">
                            <div class="resume-header">
                                <div>
                                    <div class="resume-title"><?php echo htmlspecialchars($resume['title']); ?></div>
                                    <div class="resume-date">Updated <?php echo formatDate($resume['updated_at']); ?></div>
                                </div>
                                <div class="resume-template"><?php echo ucfirst($resume['template']); ?></div>
                            </div>
                            
                            <div class="resume-actions">
                                <a href="resume_builder.php?id=<?php echo $resume['id']; ?>" class="btn btn-secondary btn-small">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a href="preview.php?id=<?php echo $resume['id']; ?>" class="btn btn-outline btn-small" target="_blank">
                                    <i class="fas fa-eye"></i>
                                    Preview
                                </a>
<a href="preview.php?id=<?php echo htmlspecialchars($resume['id']); ?>&template=<?php echo htmlspecialchars($resume['template']); ?>" class="btn btn-primary btn-small">
                                      <i class="fas fa-download"></i> Download
                                  </a>
                                <button onclick="deleteResume(<?php echo $resume['id']; ?>)" class="btn btn-outline btn-small" style="color: var(--error-color); border-color: var(--error-color);">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions" style="margin-top: 3rem;">
            <h2>Quick Actions</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <a href="resume_builder.php?template=simple" class="card" style="text-decoration: none; color: inherit;">
                    <h3><i class="fas fa-file-alt"></i> Simple Resume</h3>
                    <p>Create a clean, professional resume</p>
                </a>
                <a href="resume_builder.php?template=ats" class="card" style="text-decoration: none; color: inherit;">
                    <h3><i class="fas fa-search"></i> ATS Optimized</h3>
                    <p>Resume optimized for Applicant Tracking Systems</p>
                </a>
                <a href="resume_builder.php?template=creative" class="card" style="text-decoration: none; color: inherit;">
                    <h3><i class="fas fa-palette"></i> Creative Design</h3>
                    <p>Stand out with a unique creative template</p>
                </a>
                <a href="templates.php" class="card" style="text-decoration: none; color: inherit;">
                    <h3><i class="fas fa-th-large"></i> Browse Templates</h3>
                    <p>Explore all available resume templates</p>
                </a>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
    <script>
        function deleteResume(resumeId) {
            if (confirm('Are you sure you want to delete this resume? This action cannot be undone.')) {
                fetch('delete_resume.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: resumeId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete resume: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the resume.');
                });
            }
        }
    </script>
</body>
</html>