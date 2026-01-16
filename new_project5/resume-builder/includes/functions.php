<?php
require_once 'db.php';
require_once 'email_config.php';
// Ensure PHPMailer is available when emails are enabled
if (defined('EMAIL_ENABLED') && EMAIL_ENABLED) {
    // 1) Try Composer autoloader
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
    } else {
        // 2) Fallback: manual include from libs/PHPMailer (no Composer)
        $phpMailerDir = __DIR__ . '/../libs/PHPMailer/src';
        if (is_dir($phpMailerDir)) {
            $phpMailerFiles = ['PHPMailer.php', 'SMTP.php', 'Exception.php'];
            foreach ($phpMailerFiles as $file) {
                $path = $phpMailerDir . '/' . $file;
                if (file_exists($path)) {
                    require_once $path;
                }
            }
        }
    }
}

// Generate OTP
function generateOTP($length = 6) {
    return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

// Send email function using Gmail SMTP
function sendEmail($to, $subject, $message) {
    // For development, also log the email
    error_log("[Email Debug] To: $to | Subject: $subject");
    
    // If email is disabled, return true for development
    if (!EMAIL_ENABLED) {
        return true;
    }
    
    // Check if PHPMailer is available
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log("PHPMailer not available. Run 'composer install' in resume-builder to enable emails.");
        return false;
    }
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        // Debug settings
        if (EMAIL_DEBUG) {
            $mail->SMTPDebug = 2; // Client + Server messages
        }
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Check password reset rate limit
function checkPasswordResetRateLimit($pdo, $email) {
    $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM password_resets WHERE email = ? AND created_at > ?");
    $stmt->execute([$email, $oneHourAgo]);
    $result = $stmt->fetch();
    
    return $result['count'] < MAX_PASSWORD_RESET_ATTEMPTS;
}

// Clean expired password reset tokens
function cleanExpiredPasswordResets($pdo) {
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
    $stmt->execute();
}

// Sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user data
function getCurrentUser($pdo) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $resume = $stmt->fetch();

    if ($resume && isset($resume['content'])) {
        $resume['content'] = json_decode($resume['content'], true);
    }

    return $resume;
}

// Create user session
function createUserSession($pdo, $user_id) {
    $token = generateToken();
    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $token, $expires]);
    
    return $token;
}

// Validate session token
function validateSessionToken($pdo, $token) {
    $stmt = $pdo->prepare("SELECT user_id FROM user_sessions WHERE session_token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $result = $stmt->fetch();
    
    if ($result) {
        $_SESSION['user_id'] = $result['user_id'];
        return true;
    }
    
    return false;
}

// Logout user
function logoutUser($pdo) {
    if (isset($_SESSION['user_id'])) {
        // Remove session from database
        $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }
    
    // Clear session
    session_destroy();
    session_start();
}

// AI-powered resume suggestions (placeholder)
function getAISuggestions($jobTitle, $experience, $industry) {
    // In a real implementation, this would call an AI API like OpenAI
    $suggestions = [
        'keywords' => [
            'leadership', 'project management', 'strategic planning', 'team collaboration',
            'problem solving', 'communication', 'analytical skills', 'innovation'
        ],
        'skills' => [
            'Microsoft Office Suite', 'Project Management Tools', 'Data Analysis',
            'Customer Relationship Management', 'Agile Methodologies'
        ],
        'achievements' => [
            'Increased team productivity by 25% through process optimization',
            'Led cross-functional team of 10 members to deliver project on time',
            'Reduced operational costs by 15% through strategic initiatives'
        ]
    ];
    
    // Filter suggestions based on job title and industry
    if (stripos($jobTitle, 'developer') !== false) {
        $suggestions['skills'][] = 'Programming Languages (JavaScript, Python, Java)';
        $suggestions['skills'][] = 'Web Development Frameworks';
        $suggestions['skills'][] = 'Database Management';
    }
    
    if (stripos($jobTitle, 'manager') !== false) {
        $suggestions['skills'][] = 'Team Leadership';
        $suggestions['skills'][] = 'Budget Management';
        $suggestions['skills'][] = 'Performance Management';
    }
    
    return $suggestions;
}

// AI-generated professional summary based on job role
function generateAISummary($jobRole, $experience, $skills, $industry = '') {
    // In a real implementation, this would call an AI API like OpenAI
    // For now, we'll use intelligent templates based on job role
    
    $summaries = [
        'software developer' => [
            'Experienced software developer with expertise in full-stack development and modern programming languages. Skilled in designing scalable applications, collaborating with cross-functional teams, and delivering high-quality software solutions. Proven track record of optimizing performance and implementing best practices in software development.',
            'Passionate software developer with strong problem-solving abilities and experience in developing innovative web applications. Proficient in multiple programming languages and frameworks, with a focus on creating user-friendly, efficient, and maintainable code. Committed to continuous learning and staying current with emerging technologies.',
            'Results-driven software developer with expertise in building robust, scalable applications. Experienced in agile methodologies, code review processes, and mentoring junior developers. Strong analytical skills and ability to translate complex requirements into elegant technical solutions.'
        ],
        'project manager' => [
            'Accomplished project manager with proven track record of delivering complex projects on time and within budget. Skilled in leading cross-functional teams, managing stakeholder relationships, and implementing strategic initiatives. Strong expertise in project planning, risk management, and process optimization.',
            'Dynamic project manager with experience in managing large-scale projects across multiple industries. Excellent leadership skills with ability to motivate teams and drive results. Proficient in project management methodologies and tools, with focus on continuous improvement and stakeholder satisfaction.',
            'Strategic project manager with expertise in planning, executing, and closing projects successfully. Strong analytical and problem-solving skills, with ability to manage competing priorities and deliver value to stakeholders. Experienced in change management and process optimization.'
        ],
        'marketing manager' => [
            'Creative marketing manager with expertise in developing and executing comprehensive marketing strategies. Skilled in brand management, digital marketing, and customer acquisition. Proven track record of increasing brand awareness and driving revenue growth through innovative campaigns.',
            'Results-oriented marketing manager with experience in both traditional and digital marketing channels. Strong analytical skills with ability to interpret market data and optimize campaigns for maximum ROI. Excellent communication skills and experience leading marketing teams.',
            'Strategic marketing manager with expertise in market research, campaign development, and brand positioning. Skilled in developing customer personas, creating compelling content, and managing marketing budgets. Proven ability to increase market share and customer engagement.'
        ],
        'data analyst' => [
            'Analytical data professional with expertise in collecting, analyzing, and interpreting complex data sets. Skilled in statistical analysis, data visualization, and creating actionable insights. Experience in developing dashboards and reports to support business decision-making.',
            'Detail-oriented data analyst with strong quantitative skills and experience in business intelligence tools. Proficient in SQL, Python, and data visualization platforms. Proven ability to translate data into meaningful insights that drive strategic business decisions.',
            'Results-driven data analyst with expertise in predictive modeling and statistical analysis. Skilled in identifying trends, patterns, and opportunities from large datasets. Experience in creating comprehensive reports and presentations for executive stakeholders.'
        ],
        'sales representative' => [
            'Dynamic sales professional with proven track record of exceeding targets and building lasting client relationships. Skilled in consultative selling, negotiation, and account management. Strong ability to identify customer needs and provide tailored solutions.',
            'Results-driven sales representative with expertise in B2B sales and customer relationship management. Excellent communication and presentation skills with ability to close deals and maintain high customer satisfaction. Proven track record of achieving sales quotas.',
            'Customer-focused sales professional with experience in solution selling and account development. Skilled in prospecting, qualifying leads, and managing sales pipelines. Strong ability to understand customer pain points and position products effectively.'
        ],
        'human resources' => [
            'Experienced HR professional with expertise in talent acquisition, employee relations, and HR operations. Skilled in developing HR policies, managing recruitment processes, and fostering positive workplace culture. Strong knowledge of employment laws and best practices.',
            'Strategic HR professional with experience in organizational development and change management. Skilled in performance management, employee engagement, and HR analytics. Proven ability to align HR initiatives with business objectives.',
            'People-focused HR professional with expertise in recruitment, training, and employee development. Skilled in conflict resolution, benefits administration, and HR technology systems. Strong ability to build relationships and support organizational growth.'
        ],
        'designer' => [
            'Creative designer with expertise in user experience design and visual communication. Skilled in creating intuitive interfaces, brand identities, and engaging visual content. Strong portfolio demonstrating innovative design solutions across various mediums.',
            'Versatile designer with experience in graphic design, web design, and digital media. Proficient in design software and tools with strong understanding of design principles and user-centered design methodologies. Proven ability to deliver creative solutions.',
            'Innovative designer with expertise in creating compelling visual experiences and brand communications. Skilled in typography, color theory, and layout design. Experience in collaborating with cross-functional teams to deliver high-quality design solutions.'
        ],
        'engineer' => [
            'Experienced engineer with expertise in designing and implementing technical solutions. Skilled in problem-solving, technical analysis, and project management. Strong background in engineering principles with proven track record of delivering innovative solutions.',
            'Results-driven engineer with experience in product development and technical project management. Skilled in engineering design, testing, and optimization. Strong analytical abilities with focus on quality, efficiency, and continuous improvement.',
            'Innovative engineer with expertise in developing and optimizing engineering systems and processes. Skilled in technical documentation, cross-functional collaboration, and implementing engineering best practices. Proven ability to solve complex technical challenges.'
        ]
    ];
    
    // Find the best match for the job role
    $jobRoleLower = strtolower($jobRole);
    $bestMatch = '';
    $bestScore = 0;
    
    foreach ($summaries as $key => $summaryList) {
        similar_text($jobRoleLower, $key, $score);
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestMatch = $key;
        }
    }
    
    // If we have a good match, return a summary
    if ($bestScore > 50 && isset($summaries[$bestMatch])) {
        $summaryList = $summaries[$bestMatch];
        return $summaryList[array_rand($summaryList)];
    }
    
    // Generic summary if no good match found
    $genericSummaries = [
        "Experienced professional with strong expertise in $jobRole. Skilled in problem-solving, communication, and delivering results. Proven track record of success with focus on continuous improvement and innovation.",
        "Results-driven $jobRole with experience in achieving organizational objectives. Strong analytical and interpersonal skills with ability to work effectively in team environments. Committed to excellence and professional development.",
        "Dedicated $jobRole with expertise in relevant industry practices and methodologies. Skilled in strategic planning, execution, and stakeholder management. Proven ability to drive results and exceed expectations."
    ];
    
    return $genericSummaries[array_rand($genericSummaries)];
}

// Enhanced AI suggestions with job-specific content
function getEnhancedAISuggestions($jobRole, $experience, $industry) {
    $suggestions = getAISuggestions($jobRole, $experience, $industry);
    
    // Add job-specific keywords and skills
    $jobRoleLower = strtolower($jobRole);
    
    if (stripos($jobRoleLower, 'developer') !== false || stripos($jobRoleLower, 'programmer') !== false) {
        $suggestions['keywords'] = array_merge($suggestions['keywords'], [
            'software development', 'web development', 'application development',
            'code review', 'version control', 'API development', 'database design',
            'frontend development', 'backend development', 'full-stack development'
        ]);
        $suggestions['skills'] = array_merge($suggestions['skills'], [
            'JavaScript/TypeScript', 'Python', 'Java', 'React', 'Node.js',
            'Git', 'Docker', 'AWS/Azure', 'RESTful APIs', 'Microservices'
        ]);
    } elseif (stripos($jobRoleLower, 'manager') !== false) {
        $suggestions['keywords'] = array_merge($suggestions['keywords'], [
            'team leadership', 'strategic planning', 'budget management',
            'stakeholder management', 'process improvement', 'performance management',
            'change management', 'resource allocation', 'risk management'
        ]);
        $suggestions['skills'] = array_merge($suggestions['skills'], [
            'Leadership', 'Strategic Planning', 'Team Management', 'Budget Planning',
            'Performance Reviews', 'Project Management Tools', 'Conflict Resolution'
        ]);
    } elseif (stripos($jobRoleLower, 'analyst') !== false) {
        $suggestions['keywords'] = array_merge($suggestions['keywords'], [
            'data analysis', 'business intelligence', 'statistical analysis',
            'reporting', 'data visualization', 'predictive modeling',
            'market research', 'trend analysis', 'performance metrics'
        ]);
        $suggestions['skills'] = array_merge($suggestions['skills'], [
            'SQL', 'Python/R', 'Tableau', 'Power BI', 'Excel Advanced',
            'Statistical Analysis', 'Data Mining', 'Business Intelligence Tools'
        ]);
    } elseif (stripos($jobRoleLower, 'marketing') !== false) {
        $suggestions['keywords'] = array_merge($suggestions['keywords'], [
            'digital marketing', 'brand management', 'campaign management',
            'market research', 'customer acquisition', 'content marketing',
            'social media marketing', 'email marketing', 'SEO/SEM'
        ]);
        $suggestions['skills'] = array_merge($suggestions['skills'], [
            'Google Analytics', 'Facebook Ads', 'Google Ads', 'Mailchimp',
            'Content Management Systems', 'Social Media Platforms', 'CRM Systems'
        ]);
    }
    
    return $suggestions;
}

// Generate PDF from resume data
function generatePDF($resumeData, $template) {
    // In a real implementation, use a library like TCPDF, mPDF, or wkhtmltopdf
    // For now, return a placeholder
    return [
        'success' => true,
        'filename' => 'resume_' . time() . '.pdf',
        'path' => '/tmp/resume_' . time() . '.pdf'
    ];
}

// Save resume
function saveResume($pdo, $user_id, $title, $template, $content) {
    $stmt = $pdo->prepare("INSERT INTO resumes (user_id, title, template, content) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$user_id, $title, $template, json_encode($content)]);
}

// Get user resumes
function getUserResumes($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE user_id = ? ORDER BY updated_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Get resume by ID
function getResume($pdo, $resume_id, $user_id = null) {
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
        $stmt->execute([$resume_id, $user_id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND is_public = 1");
        $stmt->execute([$resume_id]);
    }
    $resume = $stmt->fetch();
    
    if ($resume && isset($resume['content'])) {
        $resume['content'] = json_decode($resume['content'], true);
    }
    
    return $resume;
}

// Update resume
function updateResume($pdo, $resume_id, $user_id, $title, $template, $content) {
    $stmt = $pdo->prepare("UPDATE resumes SET title = ?, template = ?, content = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([$title, $template, json_encode($content), $resume_id, $user_id]);
}

// Delete resume
function deleteResume($pdo, $resume_id, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM resumes WHERE id = ? AND user_id = ?");
    return $stmt->execute([$resume_id, $user_id]);
}

// Format date
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Get template preview data
function getTemplatePreview($template) {
    $previews = [
        'simple' => [
            'name' => 'Simple',
            'description' => 'Clean and minimal design for any industry',
            'color' => '#f3f4f6'
        ],
        'ats' => [
            'name' => 'ATS Friendly',
            'description' => 'Optimized for Applicant Tracking Systems',
            'color' => '#dbeafe'
        ],
        'executive' => [
            'name' => 'Executive',
            'description' => 'Sophisticated design for senior positions',
            'color' => '#e0e7ff'
        ],
        'creative' => [
            'name' => 'Creative',
            'description' => 'Stand out with unique design elements',
            'color' => '#fce7f3'
        ],
        'modern' => [
            'name' => 'Modern',
            'description' => 'Contemporary layout with visual appeal',
            'color' => '#d1fae5'
        ],
        'minimalist' => [
            'name' => 'Minimalist',
            'description' => 'Ultra-clean layout with strong typographic hierarchy',
            'color' => '#f3f4f6'
        ],
        'corporate' => [
            'name' => 'Corporate',
            'description' => 'Traditional corporate formatting with clear sections',
            'color' => '#e5e7eb'
        ],
        'creative-pro' => [
            'name' => 'Creative Pro',
            'description' => 'Premium creative style with bold accents',
            'color' => '#fbcfe8'
        ],
        'tech-savvy' => [
            'name' => 'Tech Savvy',
            'description' => 'Technical aesthetic with modern UI touches',
            'color' => '#bfdbfe'
        ],
        'elegant' => [
            'name' => 'Elegant',
            'description' => 'Refined serif headings and balanced spacing',
            'color' => '#fde68a'
        ],
        'startup' => [
            'name' => 'Startup',
            'description' => 'Bold, energetic layout with modern section cards',
            'color' => '#a7f3d0'
        ],
        'academic' => [
            'name' => 'Academic',
            'description' => 'Scholarly format emphasizing research and education',
            'color' => '#e2e8f0'
        ]
    ];
    
    return $previews[$template] ?? $previews['simple'];
}

// Redirect with message
function redirect($url, $message = '', $type = 'success') {
    if ($message) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header("Location: $url");
    exit();
}

// Display message
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'success';
        $message = $_SESSION['message'];
        unset($_SESSION['message'], $_SESSION['message_type']);
        
        return "<div class='alert alert-$type'>$message</div>";
    }
    return '';
}
?>