<?php
/**
 * Application Routes Configuration
 * Define all routes here
 * 
 * Format: 'METHOD' => ['path' => ['controller' => 'name', 'action' => 'method']]
 */

return [
    // Home Routes
    'GET' => [
        '/' => ['controller' => 'home', 'action' => 'index'],
        '/home' => ['controller' => 'home', 'action' => 'index'],
        
        // Page Routes
        '/about' => ['controller' => 'page', 'action' => 'about'],
        '/services' => ['controller' => 'page', 'action' => 'services'],
        '/portfolio' => ['controller' => 'page', 'action' => 'portfolio'],
        '/contact' => ['controller' => 'contact', 'action' => 'index'],
        
        // Auth Routes
        '/login' => ['controller' => 'auth', 'action' => 'login'],
        '/register' => ['controller' => 'auth', 'action' => 'register'],
        '/logout' => ['controller' => 'auth', 'action' => 'logout'],
        
        // Dashboard Routes
        '/dashboard' => ['controller' => 'dashboard', 'action' => 'index'],
        '/dashboard/admin' => ['controller' => 'dashboard', 'action' => 'admin'],
        '/dashboard/instructor' => ['controller' => 'dashboard', 'action' => 'instructor'],
        '/dashboard/student' => ['controller' => 'dashboard', 'action' => 'student'],
        
        // CRUD Routes
        '/api/{entity}' => ['controller' => 'crud', 'action' => 'index'],
        '/api/{entity}/{id}' => ['controller' => 'crud', 'action' => 'show'],
        
        // Menu Routes (Admin)
        '/admin/menu' => ['controller' => 'menu', 'action' => 'index'],
        '/api/menu' => ['controller' => 'menu', 'action' => 'getMenu'],
        
        // Alert Routes
        '/alerts' => ['controller' => 'alert', 'action' => 'index'],
        
        // Prediction Routes
        '/predictions' => ['controller' => 'prediction', 'action' => 'index'],
        '/predictions/course' => ['controller' => 'prediction', 'action' => 'course'],
        '/predictions/train' => ['controller' => 'prediction', 'action' => 'train'],
        
        // Grade Management Routes (Instructor)
        '/grade/manage' => ['controller' => 'grade', 'action' => 'manage'],
        
        // Enrollment Management Routes (Admin)
        '/enrollment' => ['controller' => 'enrollment', 'action' => 'index'],
    ],
    
    // POST Routes
    'POST' => [
        '/login' => ['controller' => 'auth', 'action' => 'login'],
        '/register' => ['controller' => 'auth', 'action' => 'register'],
        '/contact' => ['controller' => 'contact', 'action' => 'index'],
        
        // CRUD Routes
        '/api/{entity}' => ['controller' => 'crud', 'action' => 'create'],
        '/api/{entity}/{id}' => ['controller' => 'crud', 'action' => 'update'],
        
        // Menu Routes
        '/admin/menu' => ['controller' => 'menu', 'action' => 'create'],
        '/admin/menu/{id}' => ['controller' => 'menu', 'action' => 'update'],
        
        // Prediction Routes (API)
        '/api/predict/student' => ['controller' => 'prediction', 'action' => 'predictStudent'],
        '/api/predict/all' => ['controller' => 'prediction', 'action' => 'predictAll'],
        '/api/predict/course' => ['controller' => 'prediction', 'action' => 'predictCourse'],
        
        // Grade Management Routes (Instructor)
        '/grade/create' => ['controller' => 'grade', 'action' => 'create'],
        '/grade/update' => ['controller' => 'grade', 'action' => 'update'],
        '/grade/delete' => ['controller' => 'grade', 'action' => 'delete'],
        
        // Enrollment Management Routes (Admin)
        '/enrollment/enroll' => ['controller' => 'enrollment', 'action' => 'enroll'],
        '/enrollment/unenroll' => ['controller' => 'enrollment', 'action' => 'unenroll'],
        '/enrollment/bulkEnroll' => ['controller' => 'enrollment', 'action' => 'bulkEnroll'],
    ],
    
    // DELETE Routes
    'DELETE' => [
        '/api/{entity}/{id}' => ['controller' => 'crud', 'action' => 'delete'],
        '/admin/menu/{id}' => ['controller' => 'menu', 'action' => 'delete'],
    ],
];
