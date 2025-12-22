# Router Explanation

## ❓ Why There Was No Routes File Before

Previously, the Router only used **query string parameters**:
```
?controller=home&action=index
```

This worked but wasn't a proper routing system like Laravel/Symfony.

## ✅ Now We Have Proper Routes!

### Routes File Location
**`app/config/routes.php`**

This file defines all application routes, similar to:
- Laravel: `routes/web.php`
- Symfony: `config/routes.yaml`
- CodeIgniter: `config/routes.php`

## 🔄 How It Works Now

### 1. Routes File (`app/config/routes.php`)
Defines all routes:
```php
return [
    'GET' => [
        '/about' => ['controller' => 'page', 'action' => 'about'],
        '/api/{entity}/{id}' => ['controller' => 'crud', 'action' => 'show'],
    ],
];
```

### 2. Router (`app/core/Router.php`)
- Loads routes from config file
- Matches URL to route definition
- Extracts parameters from URL
- Calls controller with parameters

### 3. Entry Point (`public/index.php`)
- Initializes Router
- Router dispatches request

## 📊 Comparison

### Before (Query String Only)
```
URL: ?controller=page&action=about
     ↓
Router reads query string
     ↓
Calls PageController::about()
```

### After (Routes + Query String)
```
URL: /about
     ↓
Router matches route definition
     ↓
Finds: /about → PageController::about()
     ↓
Calls PageController::about()
```

## 🎯 Benefits

1. **Clean URLs** - `/about` instead of `?controller=page&action=about`
2. **Centralized Routes** - All routes in one file
3. **Parameter Support** - `/api/user/1` extracts `id=1`
4. **HTTP Method Support** - Different routes for GET/POST/DELETE
5. **Backward Compatible** - Query strings still work

## 📝 Route Examples

### Simple Route
```php
'/about' => ['controller' => 'page', 'action' => 'about']
```
**URL:** `http://localhost/projecty/public/about`

### Route with Parameter
```php
'/api/{entity}/{id}' => ['controller' => 'crud', 'action' => 'show']
```
**URL:** `http://localhost/projecty/public/api/user/1`  
**Calls:** `CrudController::show('user', '1')`

### Different HTTP Methods
```php
'GET' => ['/login' => ['controller' => 'auth', 'action' => 'login']],
'POST' => ['/login' => ['controller' => 'auth', 'action' => 'login']],
```
Same URL, different methods handled by same controller.

## 🔍 Router Flow

```
Request: GET /about
    ↓
Router::dispatch()
    ↓
Load routes from app/config/routes.php
    ↓
Match route: /about → PageController::about()
    ↓
Call: PageController::about()
    ↓
Render view
```

## ✅ Summary

- **Routes File:** `app/config/routes.php` ✅
- **Router:** `app/core/Router.php` ✅ (now loads routes)
- **Entry Point:** `public/index.php` ✅

Now you have a proper routing system! 🎉







