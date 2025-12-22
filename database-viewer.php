<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Simple authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<div style='padding: 20px; text-align: center;'>
        <h2>Access Denied</h2>
        <p>You need to be logged in as an admin to view the database.</p>
        <a href='/projecty/public/index.php?controller=auth&action=login'>Go to Login</a>
    </div>";
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Ensure all required tables exist
    ensureAllTablesExist($pdo);
    
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
            .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; cursor: pointer; transition: all 0.3s; }
            .stat-card:hover { background: #e9ecef; transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
            .stat-number { font-size: 2rem; font-weight: bold; color: #2c5aa0; }
            .info-message { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #bee5eb; }
            .info-message a { color: #0c5460; text-decoration: underline; }
            .success-message { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🗄️ Database Viewer</h1>
                <div>
                    <a href='/projecty/public/index.php?controller=dashboard&action=admin' class='btn'>← Back to Dashboard</a>
                    <a href='/projecty/public/index.php?controller=home&action=index' class='btn'>🏠 Home</a>
                </div>
            </div>";
    
    // Show info if tables were just created
    $stmt = $pdo->query("SHOW TABLES LIKE 'menu_items'");
    if ($stmt->rowCount() > 0) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM menu_items");
        $menuCount = $countStmt->fetchColumn();
        if ($menuCount > 0) {
            echo "<div class='success-message'>
                    <strong>✅ Database Ready!</strong> All tables are set up. Found " . count($tables) . " table(s) in the database.
                  </div>";
        }
    }
    
    // Database statistics
    echo "<div class='stats'>";
    if (!empty($tables)) {
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
                $count = $stmt->fetchColumn();
                echo "<div class='stat-card'>
                        <div class='stat-number'>$count</div>
                        <div>$table</div>
                      </div>";
            } catch (PDOException $e) {
                // Skip tables that can't be accessed
            }
        }
    } else {
        echo "<div class='stat-card' style='grid-column: 1 / -1;'>
                <p>No tables found. <a href='/projecty/utilities/quick-setup.php'>Setup Database</a></p>
              </div>";
    }
    echo "</div>";
    
    // Table selector
    if (!empty($tables)) {
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
    } else {
        echo "<div class='info-message'>
                <strong>ℹ️ No Tables Found</strong>
                <p>The database appears to be empty. <a href='/projecty/utilities/quick-setup.php'>Click here to set up the database</a></p>
              </div>";
    }
    
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
            <p><a href='/projecty/utilities/quick-setup.php'>Setup Database First</a></p>
          </div>";
}

/**
 * Ensure all required tables exist, create if missing
 */
function ensureAllTablesExist($pdo) {
    try {
        // Check and create menu_items table if missing
        $stmt = $pdo->query("SHOW TABLES LIKE 'menu_items'");
        if ($stmt->rowCount() == 0) {
            $sql = "CREATE TABLE menu_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(100) NOT NULL,
                url VARCHAR(255) NOT NULL,
                icon VARCHAR(50),
                role ENUM('admin', 'instructor', 'student', 'public') DEFAULT 'public',
                parent_id INT NULL,
                sort_order INT DEFAULT 0,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE,
                INDEX idx_role_status (role, status),
                INDEX idx_parent (parent_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $pdo->exec($sql);
            
            // Insert default menu items
            $defaultItems = [
                ['Home', '/projecty/public/index.php?controller=home&action=index', 'fas fa-home', 'public', NULL, 1],
                ['About', '/projecty/public/index.php?controller=page&action=about', 'fas fa-info-circle', 'public', NULL, 2],
                ['Services', '/projecty/public/index.php?controller=page&action=services', 'fas fa-chart-line', 'public', NULL, 3],
                ['Portfolio', '/projecty/public/index.php?controller=page&action=portfolio', 'fas fa-chart-bar', 'public', NULL, 4],
                ['Contact', '/projecty/public/index.php?controller=contact&action=index', 'fas fa-envelope', 'public', NULL, 5],
                ['Dashboard', '/projecty/public/index.php?controller=dashboard&action=admin', 'fas fa-tachometer-alt', 'admin', NULL, 1],
                ['Users', '/projecty/public/index.php?controller=crud&action=index&entity=user', 'fas fa-users', 'admin', NULL, 2],
                ['Courses', '/projecty/public/index.php?controller=crud&action=index&entity=course', 'fas fa-book', 'admin', NULL, 3],
                ['Grades', '/projecty/public/index.php?controller=crud&action=index&entity=grade', 'fas fa-graduation-cap', 'admin', NULL, 4],
                ['Menu Management', '/projecty/public/index.php?controller=menu&action=index', 'fas fa-bars', 'admin', NULL, 5],
                ['Dashboard', '/projecty/public/index.php?controller=dashboard&action=instructor', 'fas fa-tachometer-alt', 'instructor', NULL, 1],
                ['My Courses', '/projecty/public/index.php?controller=crud&action=index&entity=course', 'fas fa-book-open', 'instructor', NULL, 2],
                ['Grades', '/projecty/public/index.php?controller=crud&action=index&entity=grade', 'fas fa-graduation-cap', 'instructor', NULL, 3],
                ['Dashboard', '/projecty/public/index.php?controller=dashboard&action=student', 'fas fa-tachometer-alt', 'student', NULL, 1],
                ['My Grades', '/projecty/public/index.php?controller=crud&action=index&entity=grade', 'fas fa-chart-line', 'student', NULL, 2],
            ];
            
            $stmt = $pdo->prepare("INSERT INTO menu_items (title, url, icon, role, parent_id, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            foreach ($defaultItems as $item) {
                $stmt->execute($item);
            }
        }
        
        // Run setupDatabase function to ensure all other tables exist
        if (function_exists('setupDatabase')) {
            setupDatabase();
        } elseif (function_exists('initializeDatabase')) {
            initializeDatabase();
        }
        
    } catch (Exception $e) {
        // Silently fail - tables might already exist
    }
}
?>











