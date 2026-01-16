# ResumeAI - AI-Powered Resume Builder

A modern, AI-powered resume builder with multiple professional templates, built with PHP, MySQL, and modern CSS.

## Features

- 🤖 **AI-Powered Suggestions**: Get intelligent recommendations for content, keywords, and formatting
- 🎨 **Multiple Templates**: Choose from 5 professional templates (Simple, ATS-Friendly, Executive, Creative, Modern)
- 📱 **Mobile Responsive**: Fully responsive design that works on all devices
- 🔒 **Secure**: User authentication and data protection
- 📄 **PDF Export**: Download resumes as high-quality PDF files
- 💾 **Auto-Save**: Never lose your work with automatic saving
- 🎯 **ATS Optimized**: Templates designed to pass through Applicant Tracking Systems

## Templates Included

1. **Simple** - Clean and minimal design for any industry
2. **ATS Friendly** - Optimized for Applicant Tracking Systems
3. **Executive** - Sophisticated design for senior positions
4. **Creative** - Stand out with unique design elements
5. **Modern** - Contemporary layout with visual appeal

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Custom CSS with CSS Grid and Flexbox
- **Icons**: Font Awesome 6.0
- **Fonts**: Inter (Google Fonts)

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/resume-builder.git
   cd resume-builder
   ```

2. **Configure the database**
   - Create a new MySQL database
   - Update database credentials in `includes/db.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'resume_builder');
     ```

3. **Set up the web server**
   - Point your web server to the `resume-builder` directory
   - Ensure mod_rewrite is enabled for Apache
   - Make sure the web server has write permissions for file uploads

4. **Access the application**
   - Navigate to your domain in a web browser
   - The application will automatically create the required database tables

## Project Structure

```
resume-builder/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   └── main.js           # JavaScript functionality
│   └── images/               # Images and icons
├── includes/
│   ├── db.php               # Database connection
│   ├── functions.php        # Helper functions
│   ├── email_config.php     # Email configuration
│   ├── header.php           # Common header
│   └── footer.php           # Common footer
├── auth/
│   ├── login.php            # User login
│   ├── register.php         # User registration
│   ├── logout.php           # User logout
│   ├── forgot_password.php  # Password recovery
│   ├── reset_password.php   # Password reset
│   ├── google_login.php     # Google OAuth login
│   └── google_callback.php  # Google OAuth callback
├── admin/                   # Admin panel (future)
├── index.php               # Landing page
├── dashboard.php           # User dashboard
├── resume_builder.php      # Main resume builder
├── about.php               # About page
├── contact.php             # Contact page
├── setup_email.php         # Email configuration setup
├── composer.json           # PHP dependencies
├── .htaccess               # URL rewrites and security
├── README.md               # This file
└── EMAIL_SETUP.md          # Email setup guide
```

## Configuration

### Database Setup

The application automatically creates the following tables:

- `users` - User accounts and authentication
- `resumes` - User resumes and content
- `password_resets` - Password reset tokens
- `user_sessions` - User session management

### Email Configuration

The application now includes a complete email setup for password reset functionality using Gmail SMTP.

#### Quick Setup

1. **Install PHPMailer**: Run `composer install` in the project directory
2. **Configure Email**: Visit `setup_email.php` in your browser to configure Gmail settings
3. **Generate App Password**: Follow the instructions in the setup page to create a Gmail app password

#### Manual Configuration

If you prefer manual setup, update `includes/email_config.php`:

```php
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'your-email@gmail.com');
define('SMTP_FROM_NAME', 'ResumeAI');
```

For detailed setup instructions, see [EMAIL_SETUP.md](EMAIL_SETUP.md).

### AI Integration
Currently, AI suggestions are implemented using rule-based logic.
The system is designed to easily integrate APIs like OpenAI in future.

The current AI suggestions are placeholder functions. To integrate with real AI services:

1. Update the `getAISuggestions()` function in `includes/functions.php`
2. Add your AI API credentials
3. Implement proper API calls to services like OpenAI, GPT, or similar

## Usage

### For Users

1. **Register/Login**: Create an account or sign in
2. **Choose Template**: Select from available resume templates
3. **Fill Information**: Add your personal details, experience, education, and skills
4. **AI Suggestions**: Use AI-powered suggestions to improve your content
5. **Preview**: See real-time preview of your resume
6. **Save/Download**: Save your resume and download as PDF

### For Developers

#### Adding New Templates

1. Create template styles in `assets/css/style.css`
2. Add template preview in `includes/functions.php`
3. Update the template selector in `resume_builder.php`

#### Customizing AI Suggestions

Modify the `getAISuggestions()` function to integrate with your preferred AI service.

## Security Features

- Password hashing with PHP's built-in `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- CSRF protection (implement in production(Basic CSRF protection can be enabled using token-based validation.
))
- Secure session management
- File upload restrictions

## Performance Optimization

- CSS and JavaScript minification (implement in production)
- Image optimization
- Database query optimization
- Caching headers for static assets
- Gzip compression

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, email support@resumeai.com or create an issue in the GitHub repository.

## Roadmap

- [ ] Advanced AI integration
- [ ] More resume templates
- [ ] Cover letter builder
- [ ] Resume analytics
- [ ] Team collaboration features
- [ ] API for third-party integrations
- [ ] Mobile app

## Acknowledgments

- Font Awesome for icons
- Google Fonts for typography
- The open-source community for inspiration and tools 
