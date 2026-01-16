// Admin Panel JavaScript
class AdminPanel {
    constructor() {
        this.currentSection = 'dashboard';
        this.currentFeedbackId = null;
        this.currentMessageId = null;
        this.init();
    }

    init() {
        this.setupNavigation();
        this.loadDashboard();
        this.setupEventListeners();
    }

    setupNavigation() {
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const section = link.getAttribute('data-section');
                this.showSection(section);
                
                // Update active state
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            });
        });
    }

    setupEventListeners() {
        // User form submission
        document.getElementById('userForm').addEventListener('submit', this.handleUserSubmit.bind(this));

        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });
    }

    showSection(section) {
        // Hide all sections
        document.querySelectorAll('.section').forEach(s => {
            s.style.display = 'none';
        });

        // Show selected section
        document.getElementById(section).style.display = 'block';
        this.currentSection = section;

        // Load section data
        switch(section) {
            case 'dashboard':
                this.loadDashboard();
                break;
            case 'users':
                this.loadUsers();
                break;
            case 'resumes':
                this.loadResumes();
                break;
            case 'feedback':
                this.loadFeedback();
                break;
            case 'messages':
                this.loadMessages();
                break;
            case 'settings':
                this.loadSettings();
                break;
        }
    }

    // Dashboard Functions
    async loadDashboard() {
        try {
            const response = await fetch('api/dashboard.php');
            const data = await response.json();
            
            if (data.success) {
                this.updateDashboardStats(data.stats);
                this.updateRecentActivity(data.recentActivity);
            } else {
                console.error('Failed to load dashboard:', data.message);
            }
        } catch (error) {
            console.error('Error loading dashboard:', error);
            this.showError('Failed to load dashboard data');
        }
    }

    updateDashboardStats(stats) {
        document.getElementById('total-users').textContent = stats.totalUsers || 0;
        document.getElementById('total-resumes').textContent = stats.totalResumes || 0;
        document.getElementById('pending-feedback').textContent = stats.pendingFeedback || 0;
        document.getElementById('total-messages').textContent = stats.totalMessages || 0;
    }

    updateRecentActivity(activities) {
        const tbody = document.getElementById('recent-activity-table');
        tbody.innerHTML = '';

        if (activities && activities.length > 0) {
            activities.forEach(activity => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${activity.action}</td>
                    <td>${activity.user || 'System'}</td>
                    <td>${activity.details}</td>
                    <td>${this.formatDate(activity.created_at)}</td>
                `;


                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">No recent activity</td></tr>';
        }

        document.getElementById('recent-activity-loading').style.display = 'none';
        document.getElementById('recent-activity-content').style.display = 'block';
    }

    // User Management Functions
    async loadUsers() {
        this.showLoading('users');
        
        try {
            const response = await fetch('api/users.php');
            const data = await response.json();
            
            if (data.success) {
                this.displayUsers(data.users);
            } else {
                this.showError('Failed to load users: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading users:', error);
            this.showError('Failed to load users');
        }
    }

    displayUsers(users) {
        const tbody = document.getElementById('users-table');
        tbody.innerHTML = '';

        if (users && users.length > 0) {
            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${this.formatDate(user.created_at)}</td>
                    <td>
                        <button class="btn btn-secondary btn-sm" onclick="adminPanel.editUser(${user.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="adminPanel.deleteUser(${user.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No users found</td></tr>';
        }

        this.hideLoading('users');
    }

    openUserModal(userId = null) {
        const modal = document.getElementById('userModal');
        const title = document.getElementById('userModalTitle');
        const form = document.getElementById('userForm');
        const passwordInput = document.getElementById('userPassword');
        const confirmPasswordInput = document.getElementById('userConfirmPassword');

        form.reset();
        document.getElementById('userId').value = '';

        if (userId) {
            title.textContent = 'Edit User';
            passwordInput.required = false;
            confirmPasswordInput.required = false;
            this.loadUserData(userId);
        } else {
            title.textContent = 'Add New User';
            passwordInput.required = true;
            confirmPasswordInput.required = true;
        }
        
        modal.style.display = 'block';
    }

    closeUserModal() {
        document.getElementById('userModal').style.display = 'none';
    }

    editUser(userId) {
        this.openUserModal(userId);
    }

    async loadUserData(userId) {
        try {
            const response = await fetch(`api/users.php?id=${userId}`);
            const data = await response.json();
            
            if (data.success) {
                const user = data.user;
                document.getElementById('userId').value = user.id;
                document.getElementById('userName').value = user.name;
                document.getElementById('userEmail').value = user.email;
                document.getElementById('userPassword').required = false;
                document.getElementById('userConfirmPassword').required = false;
            } else {
                this.showError('Failed to load user data: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error loading user data:', error);
            this.showError('Failed to load user data');
        }
    }

    async handleUserSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const userId = document.getElementById('userId').value;
        const password = document.getElementById('userPassword').value;
        const confirmPassword = document.getElementById('userConfirmPassword').value;

        if (password !== confirmPassword) {
            this.showError('Passwords do not match');
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const url = 'api/users.php';
        
        if (userId) {
            data._method = 'PUT';
        }

        // For updates, if password is not provided, don't send it
        if (userId && !password) {
            delete data.password;
            delete data.confirm_password;
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess(`User ${userId ? 'updated' : 'added'} successfully`);
                this.closeUserModal();
                this.loadUsers();
            } else {
                this.showError(`Failed to ${userId ? 'update' : 'add'} user: ${result.message}`);
            }
        } catch (error) {
            console.error(`Error ${userId ? 'updating' : 'adding'} user:`, error);
            this.showError(`Failed to ${userId ? 'update' : 'add'} user`);
        }
    }

    async deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(`api/users.php?id=${userId}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('User deleted successfully');
                this.loadUsers();
            } else {
                this.showError('Failed to delete user: ' + data.message);
            }
        } catch (error) {
            console.error('Error deleting user:', error);
            this.showError('Failed to delete user');
        }
    }

    // Resume Management Functions
    async loadResumes() {
        this.showLoading('resumes');
        
        try {
            const response = await fetch('api/resumes.php');
            const data = await response.json();
            
            if (data.success) {
                this.displayResumes(data.resumes);
            } else {
                this.showError('Failed to load resumes: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading resumes:', error);
            this.showError('Failed to load resumes');
        }
    }

    displayResumes(resumes) {
        const tbody = document.getElementById('resumes-table');
        tbody.innerHTML = '';

        if (resumes && resumes.length > 0) {
            resumes.forEach(resume => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${resume.id}</td>
                    <td>${resume.title}</td>
                    <td>${resume.user_name || 'Unknown'}</td>
                    <td>${resume.template}</td>
                    <td>
                        <span class="status-badge ${resume.is_public ? 'status-resolved' : 'status-pending'}">
                            ${resume.is_public ? 'Public' : 'Private'}
                        </span>
                    </td>
                    <td>${this.formatDate(resume.created_at)}</td>
                    <td>
                        <button class="btn btn-secondary btn-sm" onclick="adminPanel.viewResume(${resume.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="adminPanel.deleteResume(${resume.id})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No resumes found</td></tr>';
        }

        this.hideLoading('resumes');
    }

    async viewResume(resumeId) {
        try {
            const response = await fetch(`api/resumes.php?id=${resumeId}`);
            const data = await response.json();

            if (data.success) {
                const resume = data.resume;
                document.getElementById('resumeDetails').innerHTML = `
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${resume.title}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">User</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${resume.user_name || 'Unknown'}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Template</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${resume.template}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Public</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${resume.is_public ? 'Yes' : 'No'}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Content</label>
                        <div class="form-control" style="height: auto; min-height: 100px; background-color: var(--bg-secondary); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); white-space: pre-wrap;">${JSON.stringify(JSON.parse(resume.content), null, 2)}</div>
                    </div>
                `;
                document.getElementById('resumeModal').style.display = 'block';
            } else {
                console.error('API Error:', data.message);
                this.showError('Failed to load resume details: ' + data.message);
            }
        } catch (error) {
            console.error('Error fetching resume details:', error);
            this.showError('Failed to load resume details');
        }
    }

    closeResumeModal() {
        document.getElementById('resumeModal').style.display = 'none';
    }

    async deleteResume(resumeId) {
        if (!confirm('Are you sure you want to delete this resume? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(`api/resumes.php?id=${resumeId}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('Resume deleted successfully');
                this.loadResumes();
            } else {
                this.showError('Failed to delete resume: ' + data.message);
            }
        } catch (error) {
            console.error('Error deleting resume:', error);
            this.showError('Failed to delete resume');
        }
    }

    // Feedback Management Functions
    async loadFeedback() {
        this.showLoading('feedback');
        
        try {
            const response = await fetch('api/feedback.php');
            const data = await response.json();
            
            if (data.success) {
                this.displayFeedback(data.feedback);
            } else {
                this.showError('Failed to load feedback: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading feedback:', error);
            this.showError('Failed to load feedback');
        }
    }

    displayFeedback(feedbackList) {
        const tbody = document.getElementById('feedback-table');
        tbody.innerHTML = '';

        if (feedbackList && feedbackList.length > 0) {
            feedbackList.forEach(feedback => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${feedback.id}</td>
                    <td>${feedback.user_name || feedback.name || 'Anonymous'}</td>
                    <td>${feedback.subject}</td>
                    <td>${'⭐'.repeat(feedback.rating)}</td>
                    <td>${feedback.category}</td>
                    <td>
                        <span class="status-badge status-${feedback.status}">
                            ${feedback.status.replace('_', ' ')}
                        </span>
                    </td>
                    <td>${this.formatDate(feedback.created_at)}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="adminPanel.viewFeedback(${feedback.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center;">No feedback found</td></tr>';
        }

        this.hideLoading('feedback');
    }

    async viewFeedback(feedbackId) {
        this.currentFeedbackId = feedbackId;
        
        try {
            const response = await fetch(`api/feedback.php?id=${feedbackId}`);
            const data = await response.json();
            
            if (data.success) {
                const feedback = data.feedback;
                document.getElementById('feedbackDetails').innerHTML = `
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${feedback.subject}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${feedback.message}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rating</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${'⭐'.repeat(feedback.rating)}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${feedback.category}</p>
                    </div>
                `;
                
                document.getElementById('adminNotes').value = feedback.admin_notes || '';
                document.getElementById('feedbackStatus').value = feedback.status;
                
                document.getElementById('feedbackModal').style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading feedback details:', error);
        }
    }

    closeFeedbackModal() {
        document.getElementById('feedbackModal').style.display = 'none';
        this.currentFeedbackId = null;
    }

    async updateFeedback() {
        if (!this.currentFeedbackId) return;

        const adminNotes = document.getElementById('adminNotes').value;
        const status = document.getElementById('feedbackStatus').value;

        try {
            const response = await fetch(`api/feedback.php`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: this.currentFeedbackId,
                    admin_notes: adminNotes,
                    status: status
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('Feedback updated successfully');
                this.closeFeedbackModal();
                this.loadFeedback();
            } else {
                this.showError('Failed to update feedback: ' + data.message);
            }
        } catch (error) {
            console.error('Error updating feedback:', error);
            this.showError('Failed to update feedback');
        }
    }

    // Message Management Functions
    async loadMessages() {
        this.showLoading('messages');
        
        try {
            const response = await fetch('api/messages.php');
            const data = await response.json();
            
            if (data.success) {
                this.displayMessages(data.messages);
            } else {
                this.showError('Failed to load messages: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            this.showError('Failed to load messages');
        }
    }

    displayMessages(messages) {
        const tbody = document.getElementById('messages-table');
        tbody.innerHTML = '';

        if (messages && messages.length > 0) {
            messages.forEach(message => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${message.id}</td>
                    <td>${message.name}</td>
                    <td>${message.email}</td>
                    <td>${message.subject}</td>
                    <td>${this.formatDate(message.created_at)}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="adminPanel.viewMessage(${message.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No messages found</td></tr>';
        }

        this.hideLoading('messages');
    }

    async viewMessage(messageId) {
        this.currentMessageId = messageId;
        
        try {
            const response = await fetch(`api/messages.php?id=${messageId}`);
            const data = await response.json();
            
            if (data.success) {
                const message = data.message;
                document.getElementById('messageDetails').innerHTML = `
                    <div class="form-group">
                        <label class="form-label">From</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${message.name} (${message.email})</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${message.subject}</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <p style="color: var(--text-primary); margin-top: 0.5rem;">${message.message}</p>
                    </div>
                `;
                
                document.getElementById('messageModal').style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading message details:', error);
        }
    }

    closeMessageModal() {
        document.getElementById('messageModal').style.display = 'none';
        this.currentMessageId = null;
    }

    async deleteMessage() {
        if (!this.currentMessageId) return;

        if (!confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(`api/messages.php?id=${this.currentMessageId}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('Message deleted successfully');
                this.closeMessageModal();
                this.loadMessages();
            } else {
                this.showError('Failed to delete message: ' + data.message);
            }
        } catch (error) {
            console.error('Error deleting message:', error);
            this.showError('Failed to delete message');
        }
    }

    // Settings Functions
    loadSettings() {
        // Load current settings from localStorage or default values
        const settings = JSON.parse(localStorage.getItem('adminSettings')) || {
            siteName: 'ResumeAI',
            adminEmail: 'admin@resumeai.com',
            maxResumes: 10,
            publicResumes: 1
        };

        document.getElementById('site-name').value = settings.siteName;
        document.getElementById('admin-email').value = settings.adminEmail;
        document.getElementById('max-resumes').value = settings.maxResumes;
        document.getElementById('public-resumes').value = settings.publicResumes;
    }

    saveSettings() {
        const settings = {
            siteName: document.getElementById('site-name').value,
            adminEmail: document.getElementById('admin-email').value,
            maxResumes: parseInt(document.getElementById('max-resumes').value),
            publicResumes: parseInt(document.getElementById('public-resumes').value)
        };

        localStorage.setItem('adminSettings', JSON.stringify(settings));
        this.showSuccess('Settings saved successfully');
    }

    // Utility Functions
    showLoading(section) {
        document.getElementById(`${section}-loading`).style.display = 'block';
        document.getElementById(`${section}-content`).style.display = 'none';
    }

    hideLoading(section) {
        document.getElementById(`${section}-loading`).style.display = 'none';
        document.getElementById(`${section}-content`).style.display = 'block';
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            background: ${type === 'success' ? 'var(--success-color)' : 'var(--error-color)'};
            box-shadow: var(--shadow-lg);
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }
}

// Global functions for onclick handlers
function openUserModal() {
    adminPanel.openUserModal();
}

function closeUserModal() {
    adminPanel.closeUserModal();
}

function editUser(userId) {
    adminPanel.editUser(userId);
}

function deleteUser(userId) {
    adminPanel.deleteUser(userId);
}

function closeFeedbackModal() {
    adminPanel.closeFeedbackModal();
}

function closeMessageModal() {
    adminPanel.closeMessageModal();
}

function updateFeedback() {
    adminPanel.updateFeedback();
}

function deleteMessage() {
    adminPanel.deleteMessage();
}

function saveSettings() {
    adminPanel.saveSettings();
}

// Initialize admin panel when page loads
let adminPanel;
document.addEventListener('DOMContentLoaded', () => {
    adminPanel = new AdminPanel();
});
