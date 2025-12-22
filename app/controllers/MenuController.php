<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\MenuModel;

/**
 * Menu Controller
 * Handles dynamic menu management
 */
class MenuController extends Controller {
    private $menuModel;
    
    public function __construct() {
        parent::__construct();
        $this->menuModel = new MenuModel();
    }
    
    /**
     * Get menu items for current user role
     */
    public function getMenu() {
        // Auto-create table if it doesn't exist
        $this->ensureMenuTableExists();
        
        $role = $_SESSION['role'] ?? null;
        $menuItems = $this->menuModel->getMenuByRole($role);
        
        $this->jsonResponse([
            'success' => true,
            'data' => $menuItems
        ]);
    }
    
    /**
     * Admin: List all menu items
     */
    public function index() {
        $this->requireRole('admin');
        
        // Auto-create table if it doesn't exist
        $this->ensureMenuTableExists();
        
        $menuItems = $this->menuModel->getAllMenuItems();
        
        $this->view('admin/menu', [
            'menuItems' => $menuItems
        ]);
    }
    
    /**
     * Ensure menu_items table exists, create if it doesn't
     */
    private function ensureMenuTableExists() {
        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            
            // Check if table exists
            $stmt = $db->query("SHOW TABLES LIKE 'menu_items'");
            if ($stmt->rowCount() == 0) {
                // Create table
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
                
                $db->exec($sql);
                
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
                
                $stmt = $db->prepare("INSERT INTO menu_items (title, url, icon, role, parent_id, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
                foreach ($defaultItems as $item) {
                    $stmt->execute($item);
                }
            }
        } catch (\Exception $e) {
            // Silently fail - table might already exist or there's a permission issue
            // The error will show when trying to use the model
        }
    }
    
    /**
     * Admin: Create menu item
     */
    public function create() {
        $this->requireRole('admin');
        
        // Auto-create table if it doesn't exist
        $this->ensureMenuTableExists();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'url' => $_POST['url'] ?? '',
                'icon' => $_POST['icon'] ?? '',
                'role' => $_POST['role'] ?? 'public',
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'sort_order' => (int)($_POST['sort_order'] ?? 0),
                'status' => $_POST['status'] ?? 'active'
            ];
            
            // Validation
            $validator = new \App\Core\Validator($data);
            $validator->required('title', 'Title is required.');
            $validator->required('url', 'URL is required.');
            $validator->maxLength('title', 100, 'Title cannot exceed 100 characters.');
            $validator->maxLength('url', 255, 'URL cannot exceed 255 characters.');
            $validator->in('role', ['admin', 'instructor', 'student', 'public'], 'Invalid role.');
            $validator->in('status', ['active', 'inactive'], 'Invalid status.');
            
            if ($validator->isValid()) {
                $id = $this->menuModel->createMenuItem($data);
                $this->redirect('/projecty/public/index.php?controller=menu&action=index&success=1');
            } else {
                $errors = $validator->getErrors();
                $this->view('admin/menu', ['errors' => $errors]);
            }
        } else {
            $this->view('admin/menu');
        }
    }
    
    /**
     * Admin: Update menu item
     */
    public function update() {
        $this->requireRole('admin');
        
        // Auto-create table if it doesn't exist
        $this->ensureMenuTableExists();
        
        $id = $_GET['id'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            $data = [
                'title' => $_POST['title'] ?? '',
                'url' => $_POST['url'] ?? '',
                'icon' => $_POST['icon'] ?? '',
                'role' => $_POST['role'] ?? 'public',
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'sort_order' => (int)($_POST['sort_order'] ?? 0),
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $validator = new \App\Core\Validator($data);
            $validator->required('title', 'Title is required.');
            $validator->required('url', 'URL is required.');
            
            if ($validator->isValid()) {
                $this->menuModel->updateMenuItem($id, $data);
                $this->redirect('/projecty/public/index.php?controller=menu&action=index&success=1');
            }
        }
        
        $this->redirect('/projecty/public/index.php?controller=menu&action=index');
    }
    
    /**
     * Admin: Delete menu item
     */
    public function delete() {
        $this->requireRole('admin');
        
        // Auto-create table if it doesn't exist
        $this->ensureMenuTableExists();
        
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $this->menuModel->deleteMenuItem($id);
            $this->redirect('/projecty/public/index.php?controller=menu&action=index&success=1');
        }
        
        $this->redirect('/projecty/public/index.php?controller=menu&action=index');
    }
}


