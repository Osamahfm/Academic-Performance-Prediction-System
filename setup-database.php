<?php
require_once 'config/database.php';

echo "<h2>EduPredict Database Setup</h2>";

try {
    echo "<p>Creating database and tables...</p>";
    setupDatabase();
    echo "<p style='color: green;'>✅ Database setup completed successfully!</p>";
    
    echo "<h3>Default Login Credentials:</h3>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@edupredict.edu / admin123</li>";
    echo "<li><strong>Instructor:</strong> instructor@edupredict.edu / instructor123</li>";
    echo "<li><strong>Student:</strong> student@edupredict.edu / student123</li>";
    echo "</ul>";
    
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

