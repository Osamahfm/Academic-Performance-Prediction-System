<?php
echo "<h2>Database Connection Test</h2>";

// Test basic MySQL connection
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ MySQL connection successful!</p>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'edupredict'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ EduPredict database exists!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ EduPredict database does not exist. Run setup-database.php to create it.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ MySQL connection failed: " . $e->getMessage() . "</p>";
    echo "<h3>Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li>Make sure XAMPP Control Panel is open</li>";
    echo "<li>Start MySQL service (click 'Start' next to MySQL)</li>";
    echo "<li>Start Apache service (click 'Start' next to Apache)</li>";
    echo "<li>Wait a few seconds for services to fully start</li>";
    echo "<li>Refresh this page</li>";
    echo "</ol>";
}

// Test our database configuration
echo "<h3>Testing Database Configuration</h3>";
try {
    require_once 'config/database.php';
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database configuration works!</p>";
    
    // Test table existence
    $tables = ['users', 'students', 'courses', 'enrollments', 'grades', 'predictions', 'alerts', 'feedback'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Table '$table' does not exist</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database configuration error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='setup-database.php'>Setup Database</a> | <a href='login.php'>Go to Login</a></p>";
?>

