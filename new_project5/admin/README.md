# ResumeAI Admin Panel

A comprehensive administration panel for managing the ResumeAI resume builder application.

## Features

### 🎯 Dashboard
- Real-time statistics overview
- Recent activity monitoring
- Quick access to all sections

### 👥 User Management
- View all registered users
- Add new users
- Edit existing user information
- Delete users (with cascade delete for resumes)

### 📄 Resume Management
- View all created resumes
- See resume details and user information
- Delete resumes
- Monitor resume creation activity

### 💬 Feedback Management
- View user feedback and support requests
- Update feedback status (pending, in progress, resolved, closed)
- Add admin notes to feedback
- Track feedback categories and ratings

### 📧 Contact Messages
- View contact form submissions
- Read message details
- Delete messages
- Monitor user inquiries

### ⚙️ Settings
- Configure system settings
- Manage site configuration
- Set user limits and preferences

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB database
- Web server (Apache/Nginx)
- ResumeAI application already set up

### Setup Steps

1. **Database Connection**
   - Ensure the `resume_builder` database exists
   - Verify database connection in `../resume-builder/includes/db.php`

2. **Admin Credentials**
   - Default login: `admin` / `admin123`
   - **Important**: Change these credentials in `login.php` for production use

3. **File Permissions**
   - Ensure web server has read/write access to admin directory
   - Set appropriate permissions for API endpoints

4. **Access the Admin Panel**
   - Navigate to `/admin/login.php`
   - Login with admin credentials
   - Access the main panel at `/admin/index.html`

## Security Features

### Authentication
- Session-based authentication
- Protected API endpoints
- Secure logout functionality

### Data Validation
- Input sanitization
- SQL injection prevention
- XSS protection

### Access Control
- Admin-only access to sensitive operations
- Secure password handling
- Session management

## API Endpoints

### Dashboard
- `GET /admin/api/dashboard.php` - Get dashboard statistics and recent activity

### Users
- `GET /admin/api/users.php` - Get all users or specific user
- `POST /admin/api/users.php` - Create new user
- `PUT /admin/api/users.php` - Update existing user
- `DELETE /admin/api/users.php?id={id}` - Delete user

### Resumes
- `GET /admin/api/resumes.php` - Get all resumes or specific resume
- `DELETE /admin/api/resumes.php?id={id}` - Delete resume

### Feedback
- `GET /admin/api/feedback.php` - Get all feedback or specific feedback
- `PUT /admin/api/feedback.php` - Update feedback status and notes

### Messages
- `GET /admin/api/messages.php` - Get all contact messages or specific message
- `DELETE /admin/api/messages.php?id={id}` - Delete message

## Usage Guide

### Adding a New User
1. Navigate to Users section
2. Click "Add User" button
3. Fill in user details (name, email, password)
4. Click "Save User"

### Managing Feedback
1. Go to Feedback section
2. Click "View" on any feedback item
3. Update status and add admin notes
4. Click "Update" to save changes

### Deleting Content
1. Navigate to appropriate section
2. Click "Delete" button on item
3. Confirm deletion in popup dialog

### System Settings
1. Go to Settings section
2. Modify configuration values
3. Click "Save Settings" to apply changes

## Customization

### Styling
- Color palette is imported from `../resume-builder/assets/css/style.css`
- Modify CSS variables in `index.html` for custom theming
- Responsive design for mobile and tablet devices

### Functionality
- Add new API endpoints in `/admin/api/` directory
- Extend JavaScript functionality in `admin.js`
- Modify database queries for custom data requirements

### Security
- Implement additional authentication methods
- Add role-based access control
- Enhance input validation and sanitization

## Troubleshooting

### Common Issues

**API Endpoints Not Working**
- Check database connection
- Verify file permissions
- Ensure PHP error reporting is enabled

**Login Issues**
- Verify admin credentials in `login.php`
- Check session configuration
- Clear browser cookies and cache

**Database Errors**
- Verify table structure matches expected schema
- Check database user permissions
- Review error logs for specific issues

### Error Messages

**"Unauthorized access"**
- Login to admin panel first
- Check session status
- Verify authentication middleware

**"Database error"**
- Check database connection
- Verify table existence
- Review SQL query syntax

## Production Considerations

### Security
- Change default admin credentials
- Use HTTPS for all admin access
- Implement rate limiting
- Add IP whitelisting if needed

### Performance
- Enable PHP OPcache
- Use database indexing
- Implement caching for dashboard stats
- Optimize database queries

### Monitoring
- Set up error logging
- Monitor admin access logs
- Implement audit trails for sensitive operations
- Regular security updates

## Support

For technical support or feature requests:
- Check the main ResumeAI documentation
- Review error logs and debugging information
- Ensure all prerequisites are met
- Verify database schema compatibility

## License

This admin panel is part of the ResumeAI project and follows the same licensing terms.

---

**Note**: This admin panel is designed for the ResumeAI resume builder application. Ensure compatibility with your specific setup and requirements.
