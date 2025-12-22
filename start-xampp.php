<?php
echo "<h2>XAMPP Services Status</h2>";

// Function to check if a port is open
function checkPort($host, $port) {
    $connection = @fsockopen($host, $port, $errno, $errstr, 2);
    if ($connection) {
        fclose($connection);
        return true;
    }
    return false;
}

// Check Apache (port 80)
if (checkPort('localhost', 80)) {
    echo "<p style='color: green;'>✅ Apache is running on port 80</p>";
} else {
    echo "<p style='color: red;'>❌ Apache is not running on port 80</p>";
}

// Check MySQL (port 3306)
if (checkPort('localhost', 3306)) {
    echo "<p style='color: green;'>✅ MySQL is running on port 3306</p>";
} else {
    echo "<p style='color: red;'>❌ MySQL is not running on port 3306</p>";
}

echo "<h3>Manual XAMPP Start Instructions:</h3>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel</li>";
echo "<li>Click 'Start' next to Apache</li>";
echo "<li>Click 'Start' next to MySQL</li>";
echo "<li>Wait for both services to show 'Running' status</li>";
echo "<li>Refresh this page to check status</li>";
echo "</ol>";

echo "<h3>Alternative Start Methods:</h3>";
echo "<p><strong>Method 1:</strong> Double-click 'xampp-control.exe' in C:\\xampp\\ folder</p>";
echo "<p><strong>Method 2:</strong> Run as Administrator: Right-click xampp-control.exe → 'Run as administrator'</p>";
echo "<p><strong>Method 3:</strong> Start services manually from command line</p>";

echo "<hr>";
echo "<p><a href='test-connection.php'>Test Database Connection</a> | <a href='setup-database.php'>Setup Database</a></p>";
?>

