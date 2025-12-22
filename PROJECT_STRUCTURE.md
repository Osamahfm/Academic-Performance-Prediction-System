# Project Structure

## 📁 Directory Organization

```
projecty/
├── app/                          # MVC Application Core
│   ├── config/                   # Configuration files
│   │   └── config.php           # App configuration
│   ├── controllers/              # Controllers (MVC)
│   │   ├── AuthController.php
│   │   ├── ContactController.php
│   │   ├── CrudController.php
│   │   ├── DashboardController.php
│   │   ├── HomeController.php
│   │   ├── MenuController.php
│   │   └── PageController.php
│   ├── core/                     # Core framework classes
│   │   ├── Controller.php        # Base controller
│   │   ├── Database.php          # Database singleton
│   │   ├── Model.php             # Base model
│   │   ├── Router.php            # Router
│   │   ├── Validator.php         # Validation class
│   │   ├── Factory/              # Factory pattern
│   │   │   └── ModelFactory.php
│   │   └── Strategy/             # Strategy pattern
│   │       └── ValidationStrategy.php
│   ├── models/                   # Models (MVC)
│   │   ├── ContactModel.php
│   │   ├── CourseModel.php
│   │   ├── GradeModel.php
│   │   ├── MenuModel.php
│   │   ├── StudentModel.php
│   │   └── UserModel.php
│   └── views/                    # Views (MVC)
│       ├── auth/
│       ├── dashboard/
│       ├── home/
│       ├── layouts/
│       └── pages/
│
├── config/                       # Configuration
│   └── database.php             # Database config
│
├── public/                       # Public web root
│   ├── assets/                   # Static assets
│   │   ├── css/
│   │   │   └── styles.css
│   │   └── js/
│   │       └── script.js
│   └── index.php                 # Front controller (MVC entry point)
│
├── utilities/                    # Utility scripts
│   ├── database/                 # Database migrations
│   │   └── menu_items_migration.sql
│   ├── quick-setup.php          # Database setup wizard
│   ├── setup-database.php        # Database setup script
│   ├── database-viewer.php       # Database viewer (admin)
│   └── README.md
│
├── docs/                         # Documentation
│   ├── README.md
│   ├── MVC_GUIDE.md
│   ├── PROJECT_PROMPT.md
│   ├── PHASE2_IMPLEMENTATION.md
│   ├── QUICK_START_PHASE2.md
│   └── TESTING_LINKS.md
│
├── tests/                        # Unit tests
│   ├── Unit/
│   │   ├── ValidatorTest.php
│   │   └── ModelFactoryTest.php
│   └── run-tests.php
│
├── Root Redirect Files           # Redirect to MVC
│   ├── index.php                 # → public/index.php
│   ├── about.php                 # → PageController::about
│   ├── contact.php               # → ContactController::index
│   ├── services.php              # → PageController::services
│   ├── portfolio.php             # → PageController::portfolio
│   ├── login.php                 # → AuthController::login
│   ├── register.php              # → AuthController::register
│   ├── logout.php                # → AuthController::logout
│   ├── admin-dashboard.php       # → DashboardController::admin
│   ├── instructor-dashboard.php  # → DashboardController::instructor
│   └── student-dashboard.php     # → DashboardController::student
│
└── PROJECT_STRUCTURE.md         # This file
```

## 🎯 Key Points

### MVC Architecture
- **Models**: `app/models/` - Data access layer
- **Views**: `app/views/` - Presentation layer
- **Controllers**: `app/controllers/` - Business logic layer

### Public Assets
- **CSS**: `public/assets/css/styles.css`
- **JavaScript**: `public/assets/js/script.js`
- **Front Controller**: `public/index.php` (MVC entry point)

### Utilities
- **Setup Scripts**: `utilities/quick-setup.php`
- **Database Tools**: `utilities/database-viewer.php`
- **Migrations**: `utilities/database/`

### Documentation
- All documentation in `docs/` folder
- Testing guide: `docs/TESTING_LINKS.md`
- Phase 2 guide: `docs/QUICK_START_PHASE2.md`

## 🚀 Entry Points

### Main Application
```
http://localhost/projecty/public/index.php
```

### Utilities
```
http://localhost/projecty/utilities/quick-setup.php
http://localhost/projecty/utilities/database-viewer.php
```

## 📝 File Organization Rules

1. **Root Directory**: Only redirect files and essential config
2. **app/**: All MVC application code
3. **public/**: Web-accessible files (front controller, assets)
4. **utilities/**: Setup scripts and tools
5. **docs/**: All documentation
6. **tests/**: Unit tests

## 🔒 Security Notes

- Utilities folder should be protected in production
- Database config should not be in public directory
- All root PHP files are redirects (no direct access)








