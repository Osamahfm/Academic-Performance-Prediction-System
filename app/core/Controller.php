<?php
namespace App\Core;

class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function view($view, $data = []) {
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("View not found: {$view}");
        }
    }
    
    protected function redirect($url) {
        header("Location: " . $url);
        exit;
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/projecty/public/index.php?controller=auth&action=login');
        }
    }
    
    protected function requireRole($role) {
        $this->requireLogin();
        if ($_SESSION['role'] !== $role) {
            $this->redirect('/projecty/public/index.php?controller=dashboard&action=index');
        }
    }
    
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

