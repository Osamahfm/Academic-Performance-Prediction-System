<?php
/**
 * Create Menu Items Table
 * Run this script to create the menu_items table for dynamic menu system
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDBConnection();
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Create Menu Table - EduPredict</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #2c5aa0; }
            .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #c3e6cb; }
            .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #f5c6cb; }
            .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #bee5eb; }
            .btn { display: inline-block; padding: 10px 20px; background: #2c5aa0; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            .btn:hover { background: #1e3a8a; }
        </style>
    </head>
    <body>
        <div class='container'>";
    
    // Read the migration SQL file
    $sqlFile = __DIR__ . '/database/menu_items_migration.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("Migration file not found: {$sqlFile}");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    echo "<h1>📋 Creating Menu Items Table</h1>";
    echo "<div class='info'><strong>Status:</strong> Executing migration...</div>";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $pdo->exec($statement);
            $successCount++;
            
            // Check if it's a CREATE TABLE statement
            if (stripos($statement, 'CREATE TABLE') !== false) {
                echo "<div class='success'>✅ Table created successfully</div>";
            }
            // Check if it's an INSERT statement
            elseif (stripos($statement, 'INSERT') !== false) {
                echo "<div class='success'>✅ Default menu items inserted</div>";
            }
        } catch (PDOException $e) {
            // Ignore "table already exists" errors
            if (strpos($e->getMessage(), 'already exists') === false && 
                strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo "<div class='error'>⚠️ Warning: " . htmlspecialchars($e->getMessage()) . "</div>";
                $errorCount++;
            } else {
                $successCount++;
            }
        }
    }
    
    // Verify table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'menu_items'");
    if ($stmt->rowCount() > 0) {
        // Count menu items
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM menu_items");
        $count = $countStmt->fetch()['count'];
        
        echo "<div class='success'>
            <h2>✅ Success!</h2>
            <p><strong>Menu Items Table Created Successfully</strong></p>
            <p>Total menu items in database: <strong>{$count}</strong></p>
        </div>";
        
        echo "<div class='info'>
            <h3>Next Steps:</h3>
            <ul>
                <li>You can now access the <a href='/projecty/public/index.php?controller=menu&action=index'>Menu Management</a> page</li>
                <li>Or go back to the <a href='/projecty/public/index.php?controller=dashboard&action=admin'>Admin Dashboard</a></li>
            </ul>
        </div>";
        
        echo "<a href='/projecty/public/index.php?controller=menu&action=index' class='btn'>Go to Menu Management</a>";
        echo "<a href='/projecty/public/index.php?controller=dashboard&action=admin' class='btn' style='margin-left: 10px;'>Go to Dashboard</a>";
    } else {
        echo "<div class='error'>
            <h2>❌ Error</h2>
            <p>Table was not created. Please check the error messages above.</p>
        </div>";
    }
    
    echo "</div></body></html>";
    
} catch (Exception $e) {
    echo "<div class='error'>
        <h2>❌ Error</h2>
        <p>" . htmlspecialchars($e->getMessage()) . "</p>
        <p>Please check:</p>
        <ul>
            <li>Database connection is working</li>
            <li>Migration file exists at: utilities/database/menu_items_migration.sql</li>
            <li>You have proper database permissions</li>
        </ul>
    </div>";
    echo "</div></body></html>";
}
?>



