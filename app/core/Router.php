<?php
namespace App\Core;

class Router {
    private $routes = [];
    private $routeDefinitions = [];
    
    public function __construct() {
        // Load route definitions from config
        $this->loadRoutes();
    }
    
    /**
     * Load routes from config file
     */
    private function loadRoutes() {
        $routesFile = __DIR__ . '/../config/routes.php';
        if (file_exists($routesFile)) {
            $this->routeDefinitions = require $routesFile;
        }
    }
    
    /**
     * Add a route programmatically
     */
    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    /**
     * Match route from URL
     */
    private function matchRoute($method, $path) {
        // First check programmatically added routes
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                return $route;
            }
        }
        
        // Then check route definitions
        if (isset($this->routeDefinitions[$method])) {
            foreach ($this->routeDefinitions[$method] as $routePath => $routeConfig) {
                if ($this->matchPath($routePath, $path, $params)) {
                    return array_merge($routeConfig, ['params' => $params ?? []]);
                }
            }
        }
        
        return null;
    }
    
    /**
     * Match path with parameters (e.g., /user/{id})
     */
    private function matchPath($pattern, $path, &$params = []) {
        // Store original pattern for parameter extraction
        $originalPattern = $pattern;
        
        // Convert route pattern to regex
        $regexPattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $pattern);
        $regexPattern = '#^' . $regexPattern . '$#';
        
        if (preg_match($regexPattern, $path, $matches)) {
            // Extract parameter names from original pattern
            preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $originalPattern, $paramNames);
            
            // Map values to parameter names
            $params = [];
            foreach ($paramNames[1] as $index => $name) {
                $params[$name] = $matches[$index + 1] ?? null;
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Dispatch the request
     */
    public function dispatch() {
        session_start();
        
        // Get request method and path
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if needed
        $basePath = '/projecty/public';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        // Normalize path
        $path = $path ?: '/';
        $path = rtrim($path, '/') ?: '/';
        
        // Try to match route
        $route = $this->matchRoute($method, $path);
        
        if ($route) {
            // Route matched - use route definition
            $controllerName = $route['controller'];
            $actionName = $route['action'];
            $params = $route['params'] ?? [];
        } else {
            // Fallback to query string parameters (backward compatibility)
            $controllerName = $_GET['controller'] ?? 'home';
            $actionName = $_GET['action'] ?? 'index';
            $params = [];
            
            // Handle CRUD routes (backward compatibility)
            if ($controllerName === 'crud') {
                $entityType = $_GET['entity'] ?? '';
                $id = $_GET['id'] ?? null;
                
                $controllerClass = 'App\\Controllers\\CrudController';
                
                if (!class_exists($controllerClass)) {
                    die("Controller not found: {$controllerClass}");
                }
                
                $controller = new $controllerClass();
                
                $methodMap = [
                    'index' => 'index',
                    'show' => 'show',
                    'create' => 'create',
                    'update' => 'update',
                    'delete' => 'delete'
                ];
                
                $method = $methodMap[$actionName] ?? 'index';
                
                if ($id) {
                    $controller->$method($entityType, $id);
                } else {
                    $controller->$method($entityType);
                }
                return;
            }
        }
        
        // Build controller class name
        $controllerClass = 'App\\Controllers\\' . ucfirst($controllerName) . 'Controller';
        
        // Check if controller class exists
        if (!class_exists($controllerClass)) {
            http_response_code(404);
            die("Controller not found: {$controllerClass}");
        }
        
        // Instantiate controller
        $controller = new $controllerClass();
        
        // Check if action method exists
        if (!method_exists($controller, $actionName)) {
            http_response_code(404);
            die("Action not found: {$actionName} in {$controllerClass}");
        }
        
        // Call the action method with parameters
        if (!empty($params)) {
            // Pass parameters to controller method
            call_user_func_array([$controller, $actionName], array_values($params));
        } else {
            $controller->$actionName();
        }
    }
}


