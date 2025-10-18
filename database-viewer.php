<?php
session_start();
require_once 'config/database.php';

// Simple authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<div style='padding: 20px; text-align: center;'>
        <h2>Access Denied</h2>
        <p>You need to be logged in as an admin to view the database.</p>
        <a href='login.php'>Go to Login</a>
    </div>";
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $selected_table = $_GET['table'] ?? $tables[0] ?? '';
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Database Viewer - EduPredict</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #2c5aa0; }
            .table-selector { margin-bottom: 20px; }
            .table-selector select { padding: 10px; font-size: 16px; border: 1px solid #ddd; border-radius: 5px; }
            .table-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background: #2c5aa0; color: white; }
            tr:hover { background: #f5f5f5; }
            .btn { background: #2c5aa0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; }
            .btn:hover { background: #1e3a8a; }
            .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
            .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
            .stat-number { font-size: 2rem; font-weight: bold; color: #2c5aa0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🗄️ Database Viewer</h1>
                <div>
                    <a href='admin-dashboard.php' class='btn'>← Back to Dashboard</a>
                    <a href='index.php' class='btn'>🏠 Home</a>
                </div>
            </div>";
    
    // Database statistics
    echo "<div class='stats'>";
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $stmt->fetchColumn();
        echo "<div class='stat-card'>
                <div class='stat-number'>$count</div>
                <div>$table</div>
              </div>";
    }
    echo "</div>";
    
    // Table selector
    echo "<div class='table-selector'>
            <label for='table-select'>Select Table: </label>
            <select id='table-select' onchange='window.location.href=\"?table=\" + this.value'>
                <option value=''>Choose a table...</option>";
    
    foreach ($tables as $table) {
        $selected = ($table === $selected_table) ? 'selected' : '';
        echo "<option value='$table' $selected>$table</option>";
    }
    
    echo "</select>
          </div>";
    
    if ($selected_table) {
        // Get table structure
        $stmt = $pdo->query("DESCRIBE `$selected_table`");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div class='table-info'>
                <h3>📋 Table: $selected_table</h3>
                <p><strong>Columns:</strong> " . count($columns) . " | <strong>Rows:</strong> ";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$selected_table`");
        echo $stmt->fetchColumn() . "</p>
              </div>";
        
        // Get table data
        $stmt = $pdo->query("SELECT * FROM `$selected_table` LIMIT 100");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($data)) {
            echo "<table>
                    <thead>
                        <tr>";
            foreach (array_keys($data[0]) as $column) {
                echo "<th>$column</th>";
            }
            echo "</tr>
                    </thead>
                    <tbody>";
            
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    $display_value = htmlspecialchars($value ?? 'NULL');
                    if (strlen($display_value) > 50) {
                        $display_value = substr($display_value, 0, 50) . '...';
                    }
                    echo "<td>$display_value</td>";
                }
                echo "</tr>";
            }
            echo "</tbody>
                  </table>";
        } else {
            echo "<p>No data found in this table.</p>";
        }
    }
    
    echo "</div>
    </body>
    </html>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; padding: 20px;'>
            <h2>Database Error</h2>
            <p>" . $e->getMessage() . "</p>
            <p><a href='quick-setup.php'>Setup Database First</a></p>
          </div>";
}
?>

