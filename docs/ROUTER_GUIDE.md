# Router Location & How It Works

## 📍 Router Location

The Router is located in:
```
app/core/Router.php
```

## 🔄 How Routing Works

### 1. Entry Point
**File:** `public/index.php`

This is the **front controller** - all requests go through here:

```php
<?php
// Load configuration
require_once __DIR__ . '/../app/config/config.php';

// Autoload classes
spl_autoload_register(function ($class) {
    // ... autoloader code
});

// Initialize and dispatch router
use App\Core\Router;

$router = new Router();
$router->dispatch();  // ← Router dispatches the request
```

### 2. Router Class
**File:** `app/core/Router.php`

The Router class handles:
- Reading URL parameters (`controller` and `action`)
- Finding the correct controller class
- Instantiating the controller
- Calling the action method

```php
class Router {
    public function dispatch() {
        session_start();
        
        // Get controller and action from URL
        $controllerName = $_GET['controller'] ?? 'home';
        $actionName = $_GET['action'] ?? 'index';
        
        // Build controller class name
        $controllerClass = 'App\\Controllers\\' . ucfirst($controllerName) . 'Controller';
        
        // Create controller instance
        $controller = new $controllerClass();
        
        // Call the action method
        $controller->$actionName();
    }
}
```

## 🌐 URL Structure

### Standard Routes
```
http://localhost/projecty/public/index.php?controller=home&action=index
                                    ↓              ↓           ↓
                              Entry Point    Controller   Action Method
```

### Examples:

| URL | Controller | Action | Result |
|-----|-----------|--------|--------|
| `?controller=home&action=index` | `HomeController` | `index()` | Home page |
| `?controller=auth&action=login` | `AuthController` | `login()` | Login page |
| `?controller=page&action=about` | `PageController` | `about()` | About page |
| `?controller=dashboard&action=index` | `DashboardController` | `index()` | Dashboard |

### CRUD Routes (Special Handling)
```
?controller=crud&action=index&entity=user
```

The Router has special handling for CRUD routes:
```php
if ($controllerName === 'crud') {
    $entityType = $_GET['entity'] ?? '';
    $id = $_GET['id'] ?? null;
    
    $controller = new CrudController();
    $controller->index($entityType);  // or show($entityType, $id)
}
```

## 📊 Request Flow Diagram

```
User Request
    ↓
public/index.php (Front Controller)
    ↓
Router::dispatch()
    ↓
Read URL Parameters (?controller=X&action=Y)
    ↓
Find Controller Class (App\Controllers\XController)
    ↓
Instantiate Controller
    ↓
Call Action Method (Y())
    ↓
Controller Uses Model
    ↓
Controller Renders View
    ↓
HTML Response to User
```

## 🔍 Router Code Breakdown

### Step 1: Get Parameters
```php
$controllerName = $_GET['controller'] ?? 'home';  // Default: 'home'
$actionName = $_GET['action'] ?? 'index';         // Default: 'index'
```

### Step 2: Build Class Name
```php
// Converts 'home' → 'App\Controllers\HomeController'
$controllerClass = 'App\\Controllers\\' . ucfirst($controllerName) . 'Controller';
```

### Step 3: Check Class Exists
```php
if (!class_exists($controllerClass)) {
    die("Controller not found: {$controllerClass}");
}
```

### Step 4: Create Controller
```php
$controller = new $controllerClass();
```

### Step 5: Check Method Exists
```php
if (!method_exists($controller, $actionName)) {
    die("Action not found: {$actionName} in {$controllerClass}");
}
```

### Step 6: Call Method
```php
$controller->$actionName();
```

## 📝 Example: Complete Flow

### URL:
```
http://localhost/projecty/public/index.php?controller=auth&action=login
```

### What Happens:

1. **Request hits:** `public/index.php`
2. **Router reads:** `controller=auth`, `action=login`
3. **Router builds:** `App\Controllers\AuthController`
4. **Router creates:** `new AuthController()`
5. **Router calls:** `$controller->login()`
6. **Controller executes:** Login logic
7. **Controller renders:** `app/views/auth/login.php`
8. **Response sent:** HTML page to browser

## 🎯 Key Files

| File | Purpose |
|------|---------|
| `public/index.php` | Entry point - initializes Router |
| `app/core/Router.php` | Router class - dispatches requests |
| `app/core/Controller.php` | Base controller - common methods |
| `app/controllers/*.php` | Individual controllers |

## 🔧 Customizing Routes

To add a new route, you don't need to modify the Router. Just:

1. **Create Controller Method:**
```php
// app/controllers/PageController.php
public function newPage() {
    $this->view('pages/newpage');
}
```

2. **Access via URL:**
```
?controller=page&action=newPage
```

The Router automatically finds and calls it!

## ⚠️ Important Notes

- **Router is in:** `app/core/Router.php`
- **Entry point is:** `public/index.php`
- **All requests** go through the Router
- **No manual routing** needed - it's automatic based on URL parameters
- **Default route:** `controller=home&action=index` (homepage)







