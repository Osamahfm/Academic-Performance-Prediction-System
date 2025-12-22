<?php
// MVC Entry Point

// Start output buffering
ob_start();

// Load configuration
require_once __DIR__ . '/../app/config/config.php';

// Optional HTTPS enforcement for production
if (defined('FORCE_HTTPS') && FORCE_HTTPS) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

    if (!$isSecure) {
        $httpsUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/');
        header('Location: ' . $httpsUrl);
        exit;
    }
}

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize and dispatch router
use App\Core\Router;

$router = new Router();
$router->dispatch();

// Flush output buffer
ob_end_flush();






