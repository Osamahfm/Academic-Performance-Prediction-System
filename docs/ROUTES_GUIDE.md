# Routes Configuration Guide

## 📍 Routes File Location

**File:** `app/config/routes.php`

This is where all application routes are defined, similar to Laravel's `routes/web.php`.

## 🎯 Route Structure

Routes are organized by HTTP method:

```php
return [
    'GET' => [
        '/path' => ['controller' => 'controllerName', 'action' => 'methodName'],
    ],
    'POST' => [
        '/path' => ['controller' => 'controllerName', 'action' => 'methodName'],
    ],
];
```

## 📝 Example Routes

### Simple Route
```php
'GET' => [
    '/about' => ['controller' => 'page', 'action' => 'about'],
],
```
**URL:** `http://localhost/projecty/public/about`  
**Calls:** `PageController::about()`

### Route with Parameters
```php
'GET' => [
    '/api/{entity}/{id}' => ['controller' => 'crud', 'action' => 'show'],
],
```
**URL:** `http://localhost/projecty/public/api/user/1`  
**Calls:** `CrudController::show('user', '1')`

## 🔄 Current Routes

### Home Routes
- `GET /` → `HomeController::index()`
- `GET /home` → `HomeController::index()`

### Page Routes
- `GET /about` → `PageController::about()`
- `GET /services` → `PageController::services()`
- `GET /portfolio` → `PageController::portfolio()`
- `GET /contact` → `ContactController::index()`

### Auth Routes
- `GET /login` → `AuthController::login()`
- `POST /login` → `AuthController::login()`
- `GET /register` → `AuthController::register()`
- `POST /register` → `AuthController::register()`
- `GET /logout` → `AuthController::logout()`

### Dashboard Routes
- `GET /dashboard` → `DashboardController::index()`
- `GET /dashboard/admin` → `DashboardController::admin()`
- `GET /dashboard/instructor` → `DashboardController::instructor()`
- `GET /dashboard/student` → `DashboardController::student()`

### CRUD API Routes
- `GET /api/{entity}` → `CrudController::index($entity)`
- `GET /api/{entity}/{id}` → `CrudController::show($entity, $id)`
- `POST /api/{entity}` → `CrudController::create($entity)`
- `POST /api/{entity}/{id}` → `CrudController::update($entity, $id)`
- `DELETE /api/{entity}/{id}` → `CrudController::delete($entity, $id)`

### Menu Routes
- `GET /admin/menu` → `MenuController::index()`
- `GET /api/menu` → `MenuController::getMenu()`
- `POST /admin/menu` → `MenuController::create()`
- `POST /admin/menu/{id}` → `MenuController::update($id)`
- `DELETE /admin/menu/{id}` → `MenuController::delete($id)`

## ➕ Adding New Routes

### Step 1: Add Route Definition
```php
// app/config/routes.php
'GET' => [
    '/my-new-page' => ['controller' => 'page', 'action' => 'myNewPage'],
],
```

### Step 2: Create Controller Method
```php
// app/controllers/PageController.php
public function myNewPage() {
    $this->view('pages/mynewpage');
}
```

### Step 3: Create View
```php
// app/views/pages/mynewpage.php
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<h1>My New Page</h1>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
```

### Step 4: Access Route
```
http://localhost/projecty/public/my-new-page
```

## 🔀 Route Parameters

Use `{paramName}` syntax for dynamic parameters:

```php
'GET' => [
    '/user/{id}' => ['controller' => 'user', 'action' => 'show'],
    '/user/{id}/posts/{postId}' => ['controller' => 'user', 'action' => 'showPost'],
],
```

Parameters are passed to controller methods:
```php
// Controller receives: show($id, $postId)
public function showPost($id, $postId) {
    // $id and $postId are available
}
```

## 🔄 Backward Compatibility

The Router still supports query string parameters for backward compatibility:

```
?controller=home&action=index
```

But routes are preferred for cleaner URLs.

## 📊 Route Matching Priority

1. **Defined Routes** (from `routes.php`) - Checked first
2. **Query String Parameters** - Fallback for backward compatibility

## ⚙️ How Router Works

1. **Request comes in** → `public/index.php`
2. **Router loads routes** → Reads `app/config/routes.php`
3. **Router matches URL** → Finds matching route
4. **Router extracts parameters** → From URL path
5. **Router calls controller** → With parameters

## 🎨 Clean URLs

### Before (Query String):
```
http://localhost/projecty/public/index.php?controller=page&action=about
```

### After (Routes):
```
http://localhost/projecty/public/about
```

Much cleaner! 🎉



