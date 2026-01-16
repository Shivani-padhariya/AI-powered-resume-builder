<?php
// Email Configuration for ResumeAI
// Update these settings with your Gmail credentials

// Gmail SMTP Settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Replace with your Gmail address
define('SMTP_PASSWORD', 'your-app-password'); // Replace with your Gmail app password
define('SMTP_FROM_EMAIL', 'your-email@gmail.com'); // Replace with your Gmail address
define('SMTP_FROM_NAME', 'ResumeAI');

// Email Settings
define('EMAIL_ENABLED', true); // Set to true to enable email sending
define('EMAIL_DEBUG', true); // Set to true to enable debug mode for troubleshooting

// Security Settings
define('PASSWORD_RESET_EXPIRY', 3600); // Password reset link expiry time in seconds (1 hour)
define('MAX_PASSWORD_RESET_ATTEMPTS', 5); // Maximum password reset attempts per hour per email 