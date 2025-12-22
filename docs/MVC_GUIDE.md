# 🏗️ MVC Architecture Guide - EduPredict

## 📁 Project Structure

```
projecty/
├── app/
│   ├── config/
│   │   └── config.php          # Application configuration
│   ├── core/
│   │   ├── Controller.php      # Base controller class
│   │   ├── Database.php        # Database singleton
│   │   ├── Model.php           # Base model class
│   │   └── Router.php           # Routing system
│   ├── controllers/
│   │   ├── AuthController.php  # Authentication (login, register, logout)
│   │   ├── ContactController.php
│   │   ├── DashboardController.php
│   │   ├── HomeController.php
│   │   └── PageController.php
│   ├── models/
│   │   ├── ContactModel.php
│   │   ├── CourseModel.php
│   │   ├── GradeModel.php
│   │   ├── StudentModel.php
│   │   └── UserModel.php
│   └── views/
│       ├── layouts/
│       │   ├── header.php      # Common header/navigation
│       │   └── footer.php      # Common footer
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── dashboard/
│       │   ├── admin.php
│       │   ├── instructor.php
│       │   └── student.php
│       ├── home/
│       │   └── index.php
│       └── pages/
│           ├── about.php
│           ├── contact.php
│           ├── portfolio.php
│           └── services.php
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── styles.css
│   │   └── js/
│   │       └── script.js
│   ├── .htaccess               # URL rewriting
│   └── index.php               # Entry point
├── config/
│   └── database.php            # Legacy database config (still used)
└── [other files...]
```

## 🚀 How to Access

### **New MVC URLs:**
- **Homepage:** `http://localhost/projecty/public/index.php?controller=home&action=index`
- **Login:** `http://localhost/projecty/public/index.php?controller=auth&action=login`
- **Register:** `http://localhost/projecty/public/index.php?controller=auth&action=register`
- **Dashboard:** `http://localhost/projecty/public/index.php?controller=dashboard&action=index`
- **About:** `http://localhost/projecty/public/index.php?controller=page&action=about`
- **Contact:** `http://localhost/projecty/public/index.php?controller=contact&action=index`

### **URL Structure:**
```
?controller=ControllerName&action=actionName
```

## 📝 MVC Components

### **1. Models (app/models/)**
- Handle database operations
- Extend `App\Core\Model`
- Example: `UserModel`, `StudentModel`

### **2. Views (app/views/)**
- HTML/PHP templates
- Use layouts for common elements
- Example: `home/index.php`, `auth/login.php`

### **3. Controllers (app/controllers/)**
- Handle requests
- Process data using models
- Render views
- Example: `HomeController`, `AuthController`

### **4. Core Classes (app/core/)**
- **Database:** Singleton for database connection
- **Controller:** Base controller with common methods
- **Model:** Base model with CRUD operations
- **Router:** Routes requests to controllers

## 🔧 How It Works

1. **Request comes in** → `public/index.php`
2. **Router dispatches** → Finds controller and action
3. **Controller processes** → Uses models to get data
4. **View renders** → Displays HTML to user

## 📚 Example: Creating a New Page

### **Step 1: Create Controller Method**
```php
// app/controllers/PageController.php
public function newPage() {
    $current_page = 'newpage';
    $this->view('pages/newpage', compact('current_page'));
}
```

### **Step 2: Create View**
```php
// app/views/pages/newpage.php
<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<h1>New Page</h1>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
```

### **Step 3: Access**
```
http://localhost/projecty/public/index.php?controller=page&action=newPage
```

## 🔐 Authentication

- **Login:** `AuthController::login()`
- **Register:** `AuthController::register()`
- **Logout:** `AuthController::logout()`
- **Session:** Stored in `$_SESSION`

## 🎯 Key Features

✅ **Separation of Concerns** - Models, Views, Controllers are separate
✅ **Reusable Code** - Base classes for common functionality
✅ **Clean URLs** - Query string routing
✅ **Security** - Role-based access control
✅ **Maintainable** - Easy to add new features

## 📖 Common Tasks

### **Add a New Model:**
```php
// app/models/NewModel.php
namespace App\Models;
use App\Core\Model;

class NewModel extends Model {
    protected $table = 'table_name';
}
```

### **Add a New Controller:**
```php
// app/controllers/NewController.php
namespace App\Controllers;
use App\Core\Controller;

class NewController extends Controller {
    public function index() {
        $this->view('new/index');
    }
}
```

### **Access Database in Controller:**
```php
$userModel = new \App\Models\UserModel();
$users = $userModel->findAll();
```

## ⚠️ Important Notes

1. **Entry Point:** All requests go through `public/index.php`
2. **Assets:** CSS/JS are in `public/assets/`
3. **Old Files:** Original PHP files still exist but MVC is the new structure
4. **Database:** Uses the same database configuration

## 🔄 Migration Status

- ✅ Core MVC structure created
- ✅ All controllers implemented
- ✅ All models created
- ✅ Views created
- ✅ Routing system working
- ⚠️ Old files still exist (can be removed later)

---

**Next Steps:**
1. Test all MVC routes
2. Remove old PHP files (optional)
3. Add more features as needed


