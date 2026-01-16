<?php
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('auth/login.php', 'Please login to access the resume builder.');
}

$user = getCurrentUser($pdo);
$template = $_GET['template'] ?? 'simple';
$resume_id = $_GET['id'] ?? null;

// Get existing resume data if editing
$resume_data = null;
if ($resume_id) {
    $resume_data = getResume($pdo, $resume_id, $_SESSION['user_id']);
    if ($resume_data) {
        $template = $resume_data['template'];
    }
}

// Define available templates in the builder UI
$availableTemplates = [
    'simple', 'ats', 'executive', 'creative', 'modern',
    'minimalist', 'corporate', 'creative-pro', 'tech-savvy', 'elegant', 'startup', 'academic'
];

// If the saved template is no longer available (e.g., removed templates),
// fallback to a supported template so the correct option is selected and previewed
if (!in_array($template, $availableTemplates, true)) {
    $template = 'simple';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $template = sanitizeInput($_POST['template'] ?? 'simple');
    $content = $_POST['content'] ?? [];
    
    if (empty($title)) {
        $error = 'Resume title is required.';
    } else {
        if ($resume_id && $resume_data) {
            // Update existing resume
            if (updateResume($pdo, $resume_id, $_SESSION['user_id'], $title, $template, $content)) {
                redirect('dashboard.php', 'Resume updated successfully!');
            } else {
                $error = 'Failed to update resume.';
            }
        } else {
            // Create new resume
            if (saveResume($pdo, $_SESSION['user_id'], $title, $template, $content)) {
                redirect('dashboard.php', 'Resume created successfully!');
            } else {
                $error = 'Failed to create resume.';
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
    <title>Resume Builder - ResumeAI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">    
<link rel="shortcut icon" href="image/2.jpg" type="image/x-icon">
    <style>
        .builder-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }
        
        .builder-sidebar {
            background: var(--bg-tertiary);
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        
        .builder-preview {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            min-height: 800px;
        }
        
        .template-selector {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .template-option {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .template-option.active {
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
        }
        
        .template-option:hover {
            border-color: var(--primary-color);
        }
        
        .ai-suggestions {
            background: var(--bg-secondary);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .ai-suggestions h4 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .suggestion-tag {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            margin: 0.25rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .suggestion-tag:hover {
            background: var(--primary-dark);
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
        
        .dynamic-list {
            margin-bottom: 1rem;
        }
        
        .list-item {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            align-items: center;
        }
        
        .list-item input {
            flex: 1;
        }
        
        .remove-item {
            background: var(--error-color);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.25rem 0.5rem;
            cursor: pointer;
        }
        
        .add-item {
            background: var(--success-color);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            margin-top: 0.5rem;
        }
        
        .btn-full {
            width: 100%;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="builder-container">
            <!-- Form -->
            <div class="builder-sidebar">
                <form id="resumeForm" method="POST">
                    <div class="form-section">
                        <h3>Resume Details</h3>
                        <div class="form-group">
                            <label for="title" class="form-label">Resume Title</label>
                            <input type="text" id="title" name="title" class="form-input" 
                                   value="<?php echo htmlspecialchars($resume_data['title'] ?? 'My Professional Resume'); ?>" required>
                        </div>
                    </div>
                    
                    <!-- Template Selection -->
                    <div class="form-section">
                        <h3>Choose Template</h3>
                        <div class="template-selector">
                            <?php
                            foreach ($availableTemplates as $t) {
                                $preview = getTemplatePreview($t);
                                $active = ($t === $template) ? 'active' : '';
                                echo "<div class='template-option $active' data-template='$t'>
                                        <div style='background: {$preview['color']}; height: 40px; border-radius: 4px; margin-bottom: 0.5rem;'></div>
                                        <strong>{$preview['name']}</strong>
                                        <input type='radio' name='template' value='$t' " . ($t === $template ? 'checked' : '') . " style='display: none;'>
                                      </div>";
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- AI Suggestions -->
                    <div class="ai-suggestions">
                        <h4><i class="fas fa-robot"></i> AI Suggestions</h4>
                        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">
                            Generate a summary first to get job-specific suggestions
                        </p>
                        <div id="ai-keywords" style="display: none;">
                            <strong>Keywords:</strong><br>
                            <div id="keywords-container"></div>
                        </div>
                        <div id="ai-skills" style="margin-top: 0.5rem; display: none;">
                            <strong>Skills:</strong><br>
                            <div id="skills-container"></div>
                        </div>
                        <div id="ai-achievements" style="margin-top: 0.5rem; display: none;">
                            <strong>Achievements:</strong><br>
                            <div id="achievements-container"></div>
                        </div>
                    </div>
                    
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        <div class="form-group">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" id="full_name" name="content[personal][name]" class="form-input" 
                                   value="<?php echo htmlspecialchars($resume_data['content']['personal']['name'] ?? $user['name']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="content[personal][email]" class="form-input" 
                                   value="<?php echo htmlspecialchars($resume_data['content']['personal']['email'] ?? $user['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" id="phone" name="content[personal][phone]" class="form-input" 
                                   value="<?php echo htmlspecialchars($resume_data['content']['personal']['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" id="location" name="content[personal][location]" class="form-input" 
                                   value="<?php echo htmlspecialchars($resume_data['content']['personal']['location'] ?? ''); ?>">
                        </div>
                        
                        <!-- AI Summary Generator -->
                        <div class="ai-summary-section" style="background: var(--bg-secondary); border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem;">
                            <h4 style="color: var(--primary-color); margin-bottom: 1rem;">
                                <i class="fas fa-robot"></i> AI Summary Generator
                            </h4>
                            <div class="form-group">
                                <label for="job_role" class="form-label">Target Job Role</label>
                                <input type="text" id="job_role" class="form-input" placeholder="e.g., Software Developer, Project Manager, Marketing Manager">
                            </div>
                            <div class="form-group">
                                <label for="experience_years" class="form-label">Years of Experience</label>
                                <select id="experience_years" class="form-input">
                                    <option value="">Select experience level</option>
                                    <option value="0-1">0-1 years (Entry Level)</option>
                                    <option value="2-3">2-3 years (Junior)</option>
                                    <option value="4-6">4-6 years (Mid-Level)</option>
                                    <option value="7-10">7-10 years (Senior)</option>
                                    <option value="10+">10+ years (Expert)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="industry" class="form-label">Industry (Optional)</label>
                                <input type="text" id="industry" class="form-input" placeholder="e.g., Technology, Healthcare, Finance">
                            </div>
                            <button type="button" id="generate_summary" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-magic"></i>
                                Generate AI Summary
                            </button>
                        </div>
                        
                        <div class="form-group">
                            <label for="summary" class="form-label">Professional Summary</label>
                            <div style="font-size: 0.95em; color: var(--primary-color); margin-bottom: 0.25rem;">
                                <i class="fas fa-robot"></i> This summary is AI-generated and tailored to your job role.
                            </div>
                            <textarea id="summary" name="content[personal][summary]" class="form-input form-textarea" rows="4"><?php echo htmlspecialchars($resume_data['content']['personal']['summary'] ?? ''); ?></textarea>
                            <div style="margin-top: 0.5rem;">
                                <button type="button" id="regenerate_summary" class="btn btn-outline" style="font-size: 0.875rem;">
                                    <i class="fas fa-sync-alt"></i>
                                    Regenerate Summary
                                </button>
                                <button type="button" id="clear_summary" class="btn btn-outline" style="font-size: 0.875rem; margin-left: 0.5rem;">
                                    <i class="fas fa-trash"></i>
                                    Clear Summary
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Experience -->
                    <div class="form-section">
                        <h3>Work Experience</h3>
                        <div id="experience-list" class="dynamic-list">
                            <?php 
                            $experiences = $resume_data['content']['experience'] ?? [['title' => '', 'company' => '', 'dates' => '', 'description' => '']];
                            foreach ($experiences as $index => $exp): 
                            ?>
                            <div class="list-item">
                                <div style="flex: 1;">
                                    <input type="text" name="content[experience][<?php echo $index; ?>][title]" 
                                           class="form-input" placeholder="Job Title" value="<?php echo htmlspecialchars($exp['title']); ?>">
                                    <input type="text" name="content[experience][<?php echo $index; ?>][company]" 
                                           class="form-input" placeholder="Company" value="<?php echo htmlspecialchars($exp['company']); ?>">
                                    <input type="text" name="content[experience][<?php echo $index; ?>][dates]" 
                                           class="form-input" placeholder="Dates" value="<?php echo htmlspecialchars($exp['dates']); ?>">
                                    <textarea name="content[experience][<?php echo $index; ?>][description]" 
                                              class="form-input" placeholder="Description" rows="3"><?php echo htmlspecialchars($exp['description']); ?></textarea>
                                </div>
                                <button type="button" class="remove-item" onclick="removeItem(this)">×</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="add-item" onclick="addExperience()">Add Experience</button>
                    </div>
                    
                    <!-- Education -->
                    <div class="form-section">
                        <h3>Education</h3>
                        <div id="education-list" class="dynamic-list">
                            <?php 
                            $education = $resume_data['content']['education'] ?? [['degree' => '', 'school' => '', 'dates' => '', 'description' => '']];
                            foreach ($education as $index => $edu): 
                            ?>
                            <div class="list-item">
                                <div style="flex: 1;">
                                    <input type="text" name="content[education][<?php echo $index; ?>][degree]" 
                                           class="form-input" placeholder="Degree" value="<?php echo htmlspecialchars($edu['degree']); ?>">
                                    <input type="text" name="content[education][<?php echo $index; ?>][school]" 
                                           class="form-input" placeholder="School" value="<?php echo htmlspecialchars($edu['school']); ?>">
                                    <input type="text" name="content[education][<?php echo $index; ?>][dates]" 
                                           class="form-input" placeholder="Dates" value="<?php echo htmlspecialchars($edu['dates']); ?>">
                                    <textarea name="content[education][<?php echo $index; ?>][description]" 
                                              class="form-input" placeholder="Description" rows="2"><?php echo htmlspecialchars($edu['description']); ?></textarea>
                                </div>
                                <button type="button" class="remove-item" onclick="removeItem(this)">×</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="add-item" onclick="addEducation()">Add Education</button>
                    </div>
                    
                    <!-- Skills -->
                    <div class="form-section">
                        <h3>Skills</h3>
                        <div id="skills-list" class="dynamic-list">
                            <?php 
                            $skills = $resume_data['content']['skills'] ?? [''];
                            foreach ($skills as $index => $skill): 
                            ?>
                            <div class="list-item">
                                <input type="text" name="content[skills][<?php echo $index; ?>]" 
                                       class="form-input" placeholder="Skill" value="<?php echo htmlspecialchars($skill); ?>">
                                <button type="button" class="remove-item" onclick="removeItem(this)">×</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="add-item" onclick="addSkill()">Add Skill</button>
                    </div>
                    
                    <!-- Achievements -->
                    <div class="form-section">
                        <h3>Achievements</h3>
                        <div id="achievements-list" class="dynamic-list">
                            <?php 
                            $achievements = $resume_data['content']['achievements'] ?? [''];
                            foreach ($achievements as $index => $achievement): 
                            ?>
                            <div class="list-item">
                                <textarea name="content[achievements][<?php echo $index; ?>]" 
                                          class="form-input" placeholder="Describe your achievement (e.g., Increased sales by 25% in Q3 2023)" 
                                          rows="2"><?php echo htmlspecialchars($achievement); ?></textarea>
                                <button type="button" class="remove-item" onclick="removeItem(this)">×</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="add-item" onclick="addAchievement()">Add Achievement</button>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fas fa-save"></i>
                        <?php echo $resume_id ? 'Update Resume' : 'Save Resume'; ?>
                    </button>
                </form>
            </div>
            
            <!-- Preview -->
            <div class="builder-preview">
                <div id="resume-preview">
                    <!-- Resume preview will be generated here -->
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
    <script>
        // Template selection
        document.querySelectorAll('.template-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.template-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input[type="radio"]').checked = true;
                updatePreview();
            });
        });
        
        // Dynamic form handling
        function addExperience() {
            const container = document.getElementById('experience-list');
            const index = container.children.length;
            const html = `
                <div class="list-item">
                    <div style="flex: 1;">
                        <input type="text" name="content[experience][${index}][title]" class="form-input" placeholder="Job Title">
                        <input type="text" name="content[experience][${index}][company]" class="form-input" placeholder="Company">
                        <input type="text" name="content[experience][${index}][dates]" class="form-input" placeholder="Dates">
                        <textarea name="content[experience][${index}][description]" class="form-input" placeholder="Description" rows="3"></textarea>
                    </div>
                    <button type="button" class="remove-item" onclick="removeItem(this)">×</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            
            // Add event listeners to new inputs
            const newItem = container.lastElementChild;
            newItem.querySelectorAll('input, textarea').forEach(input => {
                input.addEventListener('input', updatePreview);
            });
        }
        
        function addEducation() {
            const container = document.getElementById('education-list');
            const index = container.children.length;
            const html = `
                <div class="list-item">
                    <div style="flex: 1;">
                        <input type="text" name="content[education][${index}][degree]" class="form-input" placeholder="Degree">
                        <input type="text" name="content[education][${index}][school]" class="form-input" placeholder="School">
                        <input type="text" name="content[education][${index}][dates]" class="form-input" placeholder="Dates">
                        <textarea name="content[education][${index}][description]" class="form-input" placeholder="Description" rows="2"></textarea>
                    </div>
                    <button type="button" class="remove-item" onclick="removeItem(this)">×</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            
            // Add event listeners to new inputs
            const newItem = container.lastElementChild;
            newItem.querySelectorAll('input, textarea').forEach(input => {
                input.addEventListener('input', updatePreview);
            });
        }
        
        function addSkill() {
            const container = document.getElementById('skills-list');
            const index = container.children.length;
            const html = `
                <div class="list-item">
                    <input type="text" name="content[skills][${index}]" class="form-input" placeholder="Skill">
                    <button type="button" class="remove-item" onclick="removeItem(this)">×</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            
            // Add event listener to new input
            const newItem = container.lastElementChild;
            newItem.querySelector('input').addEventListener('input', updatePreview);
            updatePreview();
        }
        
        function addAchievement(achievementText = '') {
            const container = document.getElementById('achievements-list');
            const index = container.children.length;
            const html = `
                <div class="list-item">
                    <textarea name="content[achievements][${index}]" 
                              class="form-input" placeholder="Describe your achievement (e.g., Increased sales by 25% in Q3 2023)" 
                              rows="2">${achievementText}</textarea>
                    <button type="button" class="remove-item" onclick="removeItem(this)">×</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            
            // Add event listener to new textarea
            const newItem = container.lastElementChild;
            newItem.querySelector('textarea').addEventListener('input', updatePreview);
            updatePreview();
        }
        
        function removeItem(button) {
            button.parentElement.remove();
            updatePreview();
        }
        
        // AI Summary Generation functionality
        function initializeAISummary() {
            const generateBtn = document.getElementById('generate_summary');
            const regenerateBtn = document.getElementById('regenerate_summary');
            const clearBtn = document.getElementById('clear_summary');
            const summaryTextarea = document.getElementById('summary');
            
            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    generateSummary();
                });
            }
            
            if (regenerateBtn) {
                regenerateBtn.addEventListener('click', generateSummary);
            }
            
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    summaryTextarea.value = '';
                    updatePreview();
                });
            }
        }
        
        function generateSummary() {
            const jobRole = document.getElementById('job_role').value.trim();
            const experienceYears = document.getElementById('experience_years').value;
            const industry = document.getElementById('industry').value.trim();
            const summaryTextarea = document.getElementById('summary');
            const generateBtn = document.getElementById('generate_summary');
            
            if (!jobRole) {
                alert('Please enter a target job role to generate a summary.');
                return;
            }
            
            if (!experienceYears) {
                alert('Please select your years of experience.');
                return;
            }
            
            // Show loading state
            const originalText = generateBtn.innerHTML;
            generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            generateBtn.disabled = true;
            
            // Make AJAX request to generate summary
            fetch('generate_summary.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    job_role: jobRole,
                    experience: experienceYears,
                    industry: industry
                })
            })
            .then(response => response.text())
            .then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    showNotification('Invalid response from server.', 'error');
                    return;
                }
                
                if (data.success) {
                    summaryTextarea.value = data.summary;
                    updatePreview();
                    
                    // Update AI suggestions if they exist
                    if (data.suggestions) {
                        updateAISuggestions(data.suggestions);
                    }
                    
                    // Show success message
                    showNotification('AI summary generated for ' + jobRole + '!', 'success');
                } else {
                    showNotification('Failed to generate summary: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('An error occurred while generating the summary: ' + error.message, 'error');
            })
            .finally(() => {
                // Restore button state
                generateBtn.innerHTML = originalText;
                generateBtn.disabled = false;
            });
        }
        
        function updateAISuggestions(suggestions) {
            // Update keywords
            const keywordsContainer = document.getElementById('keywords-container');
            const aiKeywords = document.getElementById('ai-keywords');
            
            if (suggestions.keywords && suggestions.keywords.length > 0) {
                keywordsContainer.innerHTML = suggestions.keywords.map(keyword => 
                    `<span class="suggestion-tag" onclick="addKeyword('${keyword}')">${keyword}</span>`
                ).join('');
                aiKeywords.style.display = 'block';
            }
            
            // Update skills
            const skillsContainer = document.getElementById('skills-container');
            const aiSkills = document.getElementById('ai-skills');
            
            if (suggestions.skills && suggestions.skills.length > 0) {
                skillsContainer.innerHTML = suggestions.skills.map(skill => 
                    `<span class="suggestion-tag" onclick="addSkill('${skill}')">${skill}</span>`
                ).join('');
                aiSkills.style.display = 'block';
            }
            
            // Update achievements
            const achievementsContainer = document.getElementById('achievements-container');
            const aiAchievements = document.getElementById('ai-achievements');
            
            if (suggestions.achievements && suggestions.achievements.length > 0) {
                achievementsContainer.innerHTML = suggestions.achievements.map(achievement => 
                    `<span class="suggestion-tag" onclick="addAchievementFromAI('${achievement}')">${achievement}</span>`
                ).join('');
                aiAchievements.style.display = 'block';
            }
        }
        
        function addKeyword(keyword) {
            const summary = document.getElementById('summary');
            summary.value += (summary.value ? ' ' : '') + keyword;
            updatePreview();
        }
        
        function addSkill(skill) {
            const skillsList = document.getElementById('skills-list');
            const index = skillsList.children.length;
            const html = `
                <div class="list-item">
                    <input type="text" name="content[skills][${index}]" class="form-input" value="${skill}">
                    <button type="button" class="remove-item" onclick="removeItem(this)">×</button>
                </div>
            `;
            skillsList.insertAdjacentHTML('beforeend', html);
            
            // Add event listener to new input
            const newItem = skillsList.lastElementChild;
            newItem.querySelector('input').addEventListener('input', updatePreview);
            updatePreview();
        }
        
        function addAchievementFromAI(achievement) {
            // Add achievement to achievements section
            addAchievement(achievement);
        }
        
        // Initialize preview on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners for real-time updates
            document.querySelectorAll('#resumeForm input, #resumeForm textarea').forEach(input => {
                input.addEventListener('input', updatePreview);
            });
            
            // Initial preview update
            setTimeout(updatePreview, 100);
            
            // Initialize AI Summary Generation
            initializeAISummary();
        });
    </script>
</body>
</html>