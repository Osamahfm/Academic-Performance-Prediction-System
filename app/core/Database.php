<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $dbname = 'edupredict';
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), '2002') !== false) {
                die("
                    <div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
                        <h3>❌ MySQL Connection Failed</h3>
                        <p><strong>Error:</strong> Cannot connect to MySQL server</p>
                        <h4>To fix this issue:</h4>
                        <ol>
                            <li>Open XAMPP Control Panel</li>
                            <li>Click 'Start' next to MySQL</li>
                            <li>Wait for MySQL to show 'Running' status</li>
                            <li>Refresh this page</li>
                        </ol>
                    </div>
                ");
            } elseif (strpos($e->getMessage(), '1049') !== false) {
                die("
                    <div style='padding: 20px; background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; border-radius: 5px; margin: 20px;'>
                        <h3>⚠️ Database Not Found</h3>
                        <p><strong>Error:</strong> Database 'edupredict' does not exist</p>
                        <h4>To fix this issue:</h4>
                        <ol>
                            <li>Visit: <a href='/projecty/quick-setup.php'>http://localhost/projecty/quick-setup.php</a></li>
                            <li>This will create the database and all required tables</li>
                            <li>Refresh this page after setup</li>
                        </ol>
                    </div>
                ");
            } else {
                die("Database connection error: " . $e->getMessage());
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}









