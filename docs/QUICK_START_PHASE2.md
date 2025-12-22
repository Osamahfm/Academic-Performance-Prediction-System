# Phase 2 Quick Start Guide

## 🚀 Setup Instructions

### 1. Database Migration
Run the menu items migration to enable dynamic menu:
```sql
-- Execute: database/menu_items_migration.sql
-- This creates the menu_items table and inserts default menu items
```

### 2. Run Unit Tests
```bash
php tests/run-tests.php
```

### 3. Access the Application
- **Front Controller**: `http://localhost/projecty/public/index.php`
- **Default Routes**:
  - Home: `?controller=home&action=index`
  - Login: `?controller=auth&action=login`
  - Dashboard: `?controller=dashboard&action=index`

## 📋 Default Login Credentials

- **Admin**: admin@edupredict.edu / admin123
- **Instructor**: instructor@edupredict.edu / instructor123
- **Student**: student@edupredict.edu / student123

## 🎯 Key Features Implemented

### ✅ Authentication & Roles
- Enhanced login/register with validation
- Role-based access control (admin, instructor, student)
- Session management

### ✅ Dynamic Menu
- Database-driven menu system
- Self-referencing hierarchical structure
- Role-based menu filtering
- Admin can manage menu items

### ✅ CRUD Operations
- Generic CRUD controller for all entities
- Full Create, Read, Update, Delete support
- JSON API responses
- Works with: Users, Courses, Grades, Contacts

### ✅ Data Validation
- Comprehensive Validator class
- Strategy pattern for entity-specific validation
- Input sanitization (XSS protection)
- All forms validated

### ✅ Design Patterns
- **Singleton**: Database connection, ModelFactory caching
- **Factory**: ModelFactory for creating model instances
- **Strategy**: ValidationStrategy for different validation rules

### ✅ Unit Tests
- Validator tests
- ModelFactory tests
- Test runner included

## 🔗 API Endpoints

### CRUD Operations
```
GET    /projecty/public/index.php?controller=crud&action=index&entity={entity}
GET    /projecty/public/index.php?controller=crud&action=show&entity={entity}&id={id}
POST   /projecty/public/index.php?controller=crud&action=create&entity={entity}
POST   /projecty/public/index.php?controller=crud&action=update&entity={entity}&id={id}
POST   /projecty/public/index.php?controller=crud&action=delete&entity={entity}&id={id}
```

**Entities**: `user`, `course`, `grade`, `contact`

### Menu Management (Admin Only)
```
GET    /projecty/public/index.php?controller=menu&action=index
POST   /projecty/public/index.php?controller=menu&action=create
POST   /projecty/public/index.php?controller=menu&action=update&id={id}
POST   /projecty/public/index.php?controller=menu&action=delete&id={id}
```

## 📝 Code Examples

### Using Validator
```php
use App\Core\Validator;

$validator = new Validator($_POST);
$validator->required('email');
$validator->email('email');
$validator->minLength('password', 6);

if ($validator->isValid()) {
    $email = $validator->sanitize('email');
    // Process...
} else {
    $errors = $validator->getErrors();
}
```

### Using ModelFactory
```php
use App\Core\Factory\ModelFactory;

$userModel = ModelFactory::create('user');
$courseModel = ModelFactory::create('course');
```

### Using Validation Strategy
```php
use App\Core\Strategy\UserValidationStrategy;

$strategy = new UserValidationStrategy();
if ($strategy->validate($data)) {
    // Valid
} else {
    $errors = $strategy->getErrors();
}
```

## 🗂️ File Structure

```
projecty/
├── app/
│   ├── core/
│   │   ├── Validator.php
│   │   ├── Factory/
│   │   │   └── ModelFactory.php
│   │   └── Strategy/
│   │       └── ValidationStrategy.php
│   ├── controllers/
│   │   ├── CrudController.php
│   │   ├── MenuController.php
│   │   └── AuthController.php (updated)
│   └── models/
│       └── MenuModel.php
├── tests/
│   ├── Unit/
│   │   ├── ValidatorTest.php
│   │   └── ModelFactoryTest.php
│   └── run-tests.php
└── database/
    └── menu_items_migration.sql
```

## ⚠️ Important Notes

1. **All root PHP files redirect** to the MVC front controller
2. **Menu system requires database migration** to work
3. **CRUD endpoints return JSON** - use for API calls
4. **Validation is strict** - all inputs are validated and sanitized
5. **Role-based access** - check user role before accessing protected resources

## 🐛 Troubleshooting

### Menu not showing?
- Run the database migration: `database/menu_items_migration.sql`
- Check database connection in `app/config/config.php`

### Tests failing?
- Ensure PHPUnit or assert functions are available
- Check that all dependencies are loaded

### CRUD not working?
- Verify entity name is correct (user, course, grade, contact)
- Check user is logged in
- Verify database tables exist

## 📚 Documentation

See `PHASE2_IMPLEMENTATION.md` for detailed implementation documentation.

