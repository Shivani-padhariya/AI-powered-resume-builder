<?php
// Ensure session is available so login state reflects in the header on all pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="header">
    <div class="container">
        <nav class="nav">
          <a href="index.php" class="logo">ResumeAI</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="resume_builder.php">Builder</a></li>
                <li><a href="templates.php">Templates</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="feedback.php">Feedback</a></li>
            </ul>
            <div class="nav-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                    <a href="auth/logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="auth/login.php" class="btn btn-secondary">Login</a>
                    <a href="auth/register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>