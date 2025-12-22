<?php
// Application Configuration

// Base URL
define('BASE_URL', '/projecty/public/');

// Application Paths
define('APP_PATH', __DIR__ . '/../');
define('VIEWS_PATH', APP_PATH . 'views/');
define('MODELS_PATH', APP_PATH . 'models/');
define('CONTROLLERS_PATH', APP_PATH . 'controllers/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'edupredict');
define('DB_USER', 'root');
define('DB_PASS', '');

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour

// Application Settings
define('APP_NAME', 'EduPredict');
define('APP_VERSION', '1.0.0');

// HTTPS enforcement (set to true in production environments)
// When enabled, the front controller will redirect all HTTP traffic to HTTPS.
define('FORCE_HTTPS', false);

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);






