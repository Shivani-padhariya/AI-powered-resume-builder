# Email Setup Guide for ResumeAI

This guide will help you set up Gmail SMTP authentication for the password reset functionality in your ResumeAI application.

## Prerequisites

1. A Gmail account
2. 2-Factor Authentication enabled on your Google account
3. Composer installed (for PHPMailer dependency)

## Step-by-Step Setup

### 1. Enable 2-Factor Authentication

1. Go to your [Google Account settings](https://myaccount.google.com/)
2. Navigate to "Security"
3. Enable "2-Step Verification" if not already enabled

### 2. Generate App Password

1. Go to [Google App Passwords](https://myaccount.google.com/apppasswords)
2. Select "Mail" as the app
3. Select "Other" as the device
4. Enter "ResumeAI" as the device name
5. Click "Generate"
6. Copy the 16-character password (you'll need this later)

### 3. Install PHPMailer

Run this command in your project directory:

```bash
composer install
```

If you don't have Composer installed, you can:
1. Download PHPMailer manually from [GitHub](https://github.com/PHPMailer/PHPMailer)
2. Extract the files to your project
3. Include the PHPMailer files in your project

### 4. Configure Email Settings

#### Option A: Use the Setup Script (Recommended)

1. Navigate to `setup_email.php` in your browser
2. Enter your Gmail address
3. Enter the app password you generated in Step 2
4. Enter a sender name (e.g., "ResumeAI")
5. Click "Save Configuration"

#### Option B: Manual Configuration

1. Open `includes/email_config.php`
2. Replace the placeholder values:
   - `'your-email@gmail.com'` with your Gmail address
   - `'your-app-password'` with the app password from Step 2
   - `'ResumeAI'` with your desired sender name

### 5. Test the Setup

1. Go to the login page
2. Click "Forgot your password?"
3. Enter a valid email address
4. Check if the reset email is received

## Configuration Options

You can modify these settings in `includes/email_config.php`:

- `EMAIL_ENABLED`: Set to `false` to disable email sending (useful for development)
- `EMAIL_DEBUG`: Set to `true` to enable SMTP debugging
- `PASSWORD_RESET_EXPIRY`: Time in seconds before reset links expire (default: 3600 = 1 hour)
- `MAX_PASSWORD_RESET_ATTEMPTS`: Maximum reset attempts per hour per email (default: 5)

## Troubleshooting

### Common Issues

1. **"Authentication failed" error**
   - Make sure you're using the app password, not your regular Gmail password
   - Ensure 2-Factor Authentication is enabled

2. **"Connection refused" error**
   - Check if your hosting provider allows SMTP connections
   - Try using port 465 with SSL instead of port 587 with TLS

3. **Emails not being sent**
   - Check the error logs for detailed error messages
   - Verify that PHPMailer is properly installed
   - Ensure the email configuration is correct

### Alternative Email Services

If Gmail doesn't work for you, you can use other email services:

- **SendGrid**: Update the SMTP settings to use SendGrid's servers
- **Mailgun**: Use Mailgun's SMTP configuration
- **AWS SES**: Configure for Amazon SES

## Security Notes

- Never commit your app password to version control
- Consider using environment variables for sensitive configuration
- Regularly rotate your app passwords
- Monitor your email sending logs for suspicious activity

## Support

If you encounter issues:

1. Check the PHP error logs
2. Enable `EMAIL_DEBUG` to see detailed SMTP communication
3. Verify your Gmail account settings
4. Test with a simple email first before using the password reset feature 