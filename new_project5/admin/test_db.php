<?php
// Test database connection
echo "<h2>Testing Database Connection</h2>";

try {
    require_once './resume-builder/includes/db.php';
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test if tables exist
    $tables = ['users', 'resumes', 'feedback', 'contact_messages'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "<p style='color: green;'>✅ Table '$table' exists with $count records</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Table '$table' does not exist or has issues: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Current directory: " . __DIR__ . "</p>";
    echo "<p>Trying to include: " . realpath('../resume-builder/includes/db.php') . "</p>";
}
?>
