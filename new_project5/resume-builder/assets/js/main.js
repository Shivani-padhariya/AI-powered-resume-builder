// Main JavaScript file for ResumeAI

// Template selection functionality
document.addEventListener('DOMContentLoaded', function() {
    // Template selector
    const templateOptions = document.querySelectorAll('.template-option');
    templateOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            templateOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active class to clicked option
            this.classList.add('active');
            
            // Check the radio button
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
            
            // Update preview if on resume builder page
            if (typeof updatePreview === 'function') {
                updatePreview();
            }
        });
    });
    
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuToggle && navLinks) {
        mobileMenuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('field-error');
                } else {
                    field.classList.remove('field-error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields.', 'error');
            }
        });
    });
    
    // Password strength indicator
    const passwordField = document.querySelector('input[type="password"]');
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            updatePasswordStrengthIndicator(strength);
        });
    }
    
    // Auto-save functionality for resume builder
    if (window.location.pathname.includes('resume_builder.php')) {
        setupAutoSave();
        // Initialize preview on page load
        setTimeout(updatePreview, 100);
    }
});

// Global object for resume AI functionalities
window.ResumeAI = window.ResumeAI || {};

// Function to generate template preview
window.ResumeAI.generateTemplatePreview = function(template, data) {
    const contentDiv = document.getElementById('resume-content');
    if (!contentDiv) return;

    let htmlContent = '';

    // Helper function to safely get nested data
    const get = (obj, path, defaultValue = '') => {
        return path.split('.').reduce((acc, part) => acc && acc[part], obj) || defaultValue;
    };

    // Common sections for all templates
    const personalInfoHtml = `
        <div class="resume-header">
            <h1>${get(data, 'personal.name', 'Your Name')}</h1>
            <p>${get(data, 'personal.email', 'email@example.com')} | ${get(data, 'personal.phone', 'Phone')}</p>
            <p>${get(data, 'personal.location', 'Location')}</p>
        </div>
    `;

    const summaryHtml = get(data, 'personal.summary') ? `
        <div class="resume-section">
            <h2>Professional Summary</h2>
            <p>${get(data, 'personal.summary')}</p>
        </div>
    ` : '';

    const experienceHtml = Object.values(get(data, 'experience', {})).length > 0 ? `
        <div class="resume-section">
            <h2>Work Experience</h2>
            ${Object.values(data.experience).map(exp => `
                <div class="experience-item">
                    <h3>${get(exp, 'title', 'Job Title')}</h3>
                    <p class="company">${get(exp, 'company', 'Company')}</p>
                    <p class="dates">${get(exp, 'dates', 'Dates')}</p>
                    <p>${get(exp, 'description', 'Description')}</p>
                </div>
            `).join('')}
        </div>
    ` : '';

    const educationHtml = Object.values(get(data, 'education', {})).length > 0 ? `
        <div class="resume-section">
            <h2>Education</h2>
            ${Object.values(data.education).map(edu => `
                <div class="education-item">
                    <h3>${get(edu, 'degree', 'Degree')}</h3>
                    <p class="school">${get(edu, 'school', 'School')}</p>
                    <p class="dates">${get(edu, 'dates', 'Dates')}</p>
                    <p>${get(edu, 'description', 'Description')}</p>
                </div>
            `).join('')}
        </div>
    ` : '';

    const skillsHtml = Object.values(get(data, 'skills', {})).filter(s => s).length > 0 ? `
        <div class="resume-section">
            <h2>Skills</h2>
            <p>${Object.values(data.skills).filter(s => s).join(', ')}</p>
        </div>
    ` : '';

    switch (template) {
        case 'simple':
            htmlContent = `
                ${personalInfoHtml}
                ${summaryHtml}
                ${experienceHtml}
                ${educationHtml}
                ${skillsHtml}
            `;
            break;
        case 'ats':
            // ATS template might prioritize keywords and clear sections
            htmlContent = `
                ${personalInfoHtml}
                ${summaryHtml}
                ${skillsHtml}
                ${experienceHtml}
                ${educationHtml}
            `;
            break;
        case 'executive':
            // Executive template might emphasize summary and leadership experience
            htmlContent = `
                ${personalInfoHtml}
                ${summaryHtml}
                ${experienceHtml}
                ${educationHtml}
                ${skillsHtml}
            `;
            break;
        case 'creative':
            // Creative template might have a different layout or include portfolio links
            htmlContent = `
                ${personalInfoHtml}
                ${summaryHtml}
                ${experienceHtml}
                ${educationHtml}
                ${skillsHtml}
            `;
            break;
        case 'modern':
            // Modern template might have a clean, minimalist design
            htmlContent = `
                ${personalInfoHtml}
                ${summaryHtml}
                ${experienceHtml}
                ${educationHtml}
                ${skillsHtml}
            `;
            break;
        default:
            // Fallback to simple if template is not recognized
            htmlContent = `
                ${personalInfoHtml}
                ${summaryHtml}
                ${experienceHtml}
                ${educationHtml}
                ${skillsHtml}
            `;
            break;
    }

    contentDiv.innerHTML = htmlContent;
};

// Password strength calculation
function calculatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

// Update password strength indicator
function updatePasswordStrengthIndicator(strength) {
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    if (strengthBar && strengthText) {
        const percentage = (strength / 5) * 100;
        strengthBar.style.width = percentage + '%';
        
        let text = '';
        let color = '';
        
        if (strength <= 1) {
            text = 'Very Weak';
            color = '#ef4444';
        } else if (strength <= 2) {
            text = 'Weak';
            color = '#f59e0b';
        } else if (strength <= 3) {
            text = 'Fair';
            color = '#f59e0b';
        } else if (strength <= 4) {
            text = 'Good';
            color = '#10b981';
        } else {
            text = 'Strong';
            color = '#10b981';
        }
        
        strengthBar.style.background = color;
        strengthText.textContent = text;
        strengthText.style.color = color;
    }
}

// Auto-save functionality
function setupAutoSave() {
    const form = document.getElementById('resumeForm');
    if (!form) return;
    
    let autoSaveTimeout;
    
    form.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            saveDraft();
        }, 2000); // Auto-save after 2 seconds of inactivity
    });
}

// Save draft function
function saveDraft() {
    const form = document.getElementById('resumeForm');
    if (!form) return;
    
    const formData = new FormData(form);
    formData.append('action', 'save_draft');
    
    fetch('save_draft.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Draft saved automatically', 'success');
        }
    })
    .catch(error => {
        console.error('Auto-save error:', error);
    });
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
        notification.remove();
    });
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 300px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    `;
    
    // Set background color based on type
    switch (type) {
        case 'success':
            notification.style.background = 'var(--success-color)';
            break;
        case 'error':
            notification.style.background = 'var(--error-color)';
            break;
        case 'warning':
            notification.style.background = 'var(--warning-color)';
            break;
        default:
            notification.style.background = 'var(--primary-color)';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Real-time preview update function
function updatePreview() {
    const form = document.getElementById('resumeForm');
    const preview = document.getElementById('resume-preview');
    
    if (!form) {
        console.log('Form not found');
        return;
    }
    
    if (!preview) {
        console.log('Preview container not found');
        return;
    }
    
    const formData = new FormData(form);
    const data = {};
    
    // Reconstruct nested object from FormData
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('content[')) {
            // content[section][index][field] -> ['content', 'section', 'index', 'field']
            const path = key.replace(/\]/g, '').split(/[\[]/);
            
            // Skip 'content' part
            const keys = path.slice(1);
            
            let current = data;
            for (let i = 0; i < keys.length - 1; i++) {
                const k = keys[i];
                if (!current[k]) {
                    // If the next key is a number, create an array, otherwise an object
                    current[k] = /^\d+$/.test(keys[i + 1]) ? [] : {};
                }
                current = current[k];
            }
            
            if (keys.length > 0) {
                current[keys[keys.length - 1]] = value;
            }
        }
    }
    
    // Get selected template
    const templateRadio = form.querySelector('input[name="template"]:checked');
    const template = templateRadio ? templateRadio.value : 'simple';
    
    console.log('Updating preview with data:', data, 'template:', template);
    
    // Generate preview
    generatePreview(data, template);
}

// Generate preview with template
function generatePreview(data, template) {
    const preview = document.getElementById('resume-preview');
    if (!preview) {
        console.log('Preview container not found in generatePreview');
        return;
    }
    
    try {
        // Generate template preview directly
        generateTemplatePreview(template, data);
    } catch (error) {
        console.error('Error generating preview:', error);
        // Fallback preview
        preview.innerHTML = `
            <div class="resume-simple">
                <header class="resume-header">
                    <h1>${data.personal?.name || 'Your Name'}</h1>
                    <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                    <p>${data.personal?.location || 'Location'}</p>
                </header>
                
                <section class="resume-section">
                    <h2>Professional Summary</h2>
                    <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
                </section>
                
                <section class="resume-section">
                    <h2>Work Experience</h2>
                    ${Object.values(data.experience || {}).map(exp => `
                        <div class="experience-item">
                            <h3>${exp.title || 'Job Title'}</h3>
                            <p class="company">${exp.company || 'Company'}</p>
                            <p class="dates">${exp.dates || 'Dates'}</p>
                            <p>${exp.description || 'Description'}</p>
                        </div>
                    `).join('')}
                </section>
                
                <section class="resume-section">
                    <h2>Education</h2>
                    ${Object.values(data.education || {}).map(edu => `
                        <div class="education-item">
                            <h3>${edu.degree || 'Degree'}</h3>
                            <p class="school">${edu.school || 'School'}</p>
                            <p class="dates">${edu.dates || 'Dates'}</p>
                        </div>
                    `).join('')}
                </section>
                
                <section class="resume-section">
                    <h2>Skills</h2>
                    <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
                </section>
            </div>
        `;
    }
}

// Template preview generation
function generateTemplatePreview(template, data) {
    // Support both preview containers used across pages
    const previewContainer =
        document.getElementById('resume-preview') ||
        document.getElementById('resume-content');
    if (!previewContainer) {
        console.log('Preview container not found (expected #resume-preview or #resume-content)');
        return;
    }
    
    let html = '';
    
    switch (template) {
        case 'simple':
            html = generateSimpleTemplate(data);
            break;
        case 'ats':
            html = generateATSTemplate(data);
            break;
        case 'executive':
            html = generateExecutiveTemplate(data);
            break;
        case 'creative':
            html = generateCreativeTemplate(data);
            break;
        case 'modern':
            html = generateModernTemplate(data);
            break;
        case 'minimalist':
            html = generateMinimalistTemplate(data);
            break;
        case 'corporate':
            html = generateCorporateTemplate(data);
            break;
        case 'creative-pro':
            html = generateCreativeProTemplate(data);
            break;
        case 'tech-savvy':
            html = generateTechSavvyTemplate(data);
            break;
        case 'elegant':
            html = generateElegantTemplate(data);
            break;
        case 'startup':
            html = generateStartupTemplate(data);
            break;
        case 'academic':
            html = generateAcademicTemplate(data);
            break;
        default:
            html = generateSimpleTemplate(data);
    }
    
    previewContainer.innerHTML = html;
}

// Template generators
function generateSimpleTemplate(data) {
    return `
        <div class="resume-simple">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>Professional Summary</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Work Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                        <p>${edu.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Skills</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

function generateATSTemplate(data) {
    return `
        <div class="resume-ats">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>SUMMARY</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>PROFESSIONAL EXPERIENCE</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'} | ${exp.dates || 'Dates'}</p>
                        <ul>
                            <li>${exp.description || 'Description'}</li>
                        </ul>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>EDUCATION</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>SKILLS</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>ACHIEVEMENTS</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

// Photo template removed

function generateExecutiveTemplate(data) {
    return `
        <div class="resume-executive">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p class="executive-title">Executive Professional</p>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>EXECUTIVE SUMMARY</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>PROFESSIONAL EXPERIENCE</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>EDUCATION</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>CORE COMPETENCIES</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>KEY ACHIEVEMENTS</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

function generateCreativeTemplate(data) {
    return `
        <div class="resume-creative">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <div class="creative-subtitle">
                    <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                    <p>${data.personal?.location || 'Location'}</p>
                </div>
            </header>
            
            <section class="resume-section">
                <h2>About Me</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                        <p>${edu.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Skills & Tools</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

function generateModernTemplate(data) {
    return `
        <div class="resume-modern">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>Summary</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                        <p>${edu.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Skills</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

// New template generators
function generateMinimalistTemplate(data) {
    return `
        <div class="resume-minimalist">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>About</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Skills</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

function generateCorporateTemplate(data) {
    return `
        <div class="resume-corporate">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>Professional Summary</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Professional Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Core Competencies</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Key Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

function generateCreativeProTemplate(data) {
    return `
        <div class="resume-creative-pro">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>Creative Vision</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Creative Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Creative Skills</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Creative Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

function generateTechSavvyTemplate(data) {
    return `
        <div class="resume-tech-savvy">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>Technical Summary</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Technical Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Technical Skills</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Technical Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

function generateElegantTemplate(data) {
    return `
        <div class="resume-elegant">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>Professional Profile</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Professional Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Core Competencies</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Notable Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

function generateStartupTemplate(data) {
    return `
        <div class="resume-startup">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>Vision</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Company'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'School'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Skills</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Key Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

function generateAcademicTemplate(data) {
    return `
        <div class="resume-academic">
            <header class="resume-header">
                <h1>${data.personal?.name || 'Your Name'}</h1>
                <p>${data.personal?.email || 'email@example.com'} | ${data.personal?.phone || 'Phone'}</p>
                <p>${data.personal?.location || 'Location'}</p>
            </header>
            
            <section class="resume-section">
                <h2>Research Focus</h2>
                <p>${data.personal?.summary || 'Professional summary goes here...'}</p>
            </section>
            
            <section class="resume-section">
                <h2>Research Experience</h2>
                ${Object.values(data.experience || {}).map(exp => `
                    <div class="experience-item">
                        <h3>${exp.title || 'Job Title'}</h3>
                        <p class="company">${exp.company || 'Institution'}</p>
                        <p class="dates">${exp.dates || 'Dates'}</p>
                        <p>${exp.description || 'Description'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Education</h2>
                ${Object.values(data.education || {}).map(edu => `
                    <div class="education-item">
                        <h3>${edu.degree || 'Degree'}</h3>
                        <p class="school">${edu.school || 'Institution'}</p>
                        <p class="dates">${edu.dates || 'Dates'}</p>
                    </div>
                `).join('')}
            </section>
            
            <section class="resume-section">
                <h2>Research Skills</h2>
                <p>${Object.values(data.skills || {}).filter(s => s).join(', ')}</p>
            </section>
            
            ${Object.values(data.achievements || {}).filter(a => a).length > 0 ? `
            <section class="resume-section">
                <h2>Research Achievements</h2>
                <ul>
                    ${Object.values(data.achievements || {}).filter(a => a).map(achievement => `
                        <li>${achievement}</li>
                    `).join('')}
                </ul>
            </section>
            ` : ''}
        </div>
    `;
}

// Export functions for use in other scripts
window.ResumeAI = {
    generateTemplatePreview,
    showNotification,
    calculatePasswordStrength,
    updatePasswordStrengthIndicator,
    updatePreview
};