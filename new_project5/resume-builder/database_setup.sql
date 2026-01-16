-- ResumeAI Database Setup
-- This file creates the complete database structure for the resume builder

-- Create the database
CREATE DATABASE IF NOT EXISTS resume_builder;
USE resume_builder;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NULL,
    google_id VARCHAR(255) UNIQUE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Resumes table
CREATE TABLE IF NOT EXISTS resumes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    template VARCHAR(50) NOT NULL DEFAULT 'simple',
    content JSON NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Password reset tokens
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User sessions
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample data for testing (optional)
-- Sample user (password: test123)
INSERT INTO users (name, email, password) VALUES 
('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE name = name;

-- Sample resume
INSERT INTO resumes (user_id, title, template, content, is_public) VALUES 
(1, 'My Professional Resume', 'simple', '{"personal":{"name":"Test User","email":"test@example.com","phone":"+1 234 567 8900","location":"New York, NY","summary":"Experienced professional with strong expertise in software development. Skilled in problem-solving, communication, and delivering results. Proven track record of success with focus on continuous improvement and innovation."},"experience":{"0":{"title":"Software Developer","company":"Tech Corp","dates":"2020 - Present","description":"Developed web applications using modern technologies. Collaborated with cross-functional teams to deliver high-quality software solutions."}},"education":{"0":{"degree":"Bachelor of Science in Computer Science","school":"University of Technology","dates":"2016 - 2020","description":"Graduated with honors. Focused on software engineering and web development."}},"skills":{"0":"JavaScript","1":"Python","2":"React","3":"Node.js","4":"SQL"}}', TRUE)
ON DUPLICATE KEY UPDATE title = title;

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_resumes_user_id ON resumes(user_id);
CREATE INDEX idx_resumes_template ON resumes(template);
CREATE INDEX idx_password_resets_email ON password_resets(email);
CREATE INDEX idx_password_resets_token ON password_resets(token);
CREATE INDEX idx_user_sessions_token ON user_sessions(session_token);
CREATE INDEX idx_user_sessions_user_id ON user_sessions(user_id);

-- Show created tables
SHOW TABLES;

-- Show table structures
DESCRIBE users;
DESCRIBE resumes;
DESCRIBE password_resets;
DESCRIBE user_sessions;