# Testing Links & Guide

## 🔗 Quick Access Links

### Base URL
```
http://localhost/projecty/public/index.php
```

---

## 🏠 Public Pages (No Login Required)

### Home Page
```
http://localhost/projecty/public/index.php?controller=home&action=index
```
or simply:
```
http://localhost/projecty/public/index.php
```

### About Page
```
http://localhost/projecty/public/index.php?controller=page&action=about
```

### Services Page
```
http://localhost/projecty/public/index.php?controller=page&action=services
```

### Portfolio Page
```
http://localhost/projecty/public/index.php?controller=page&action=portfolio
```

### Contact Page
```
http://localhost/projecty/public/index.php?controller=contact&action=index
```

---

## 🔐 Authentication Pages

### Login Page
```
http://localhost/projecty/public/index.php?controller=auth&action=login
```

### Register Page
```
http://localhost/projecty/public/index.php?controller=auth&action=register
```

### Logout
```
http://localhost/projecty/public/index.php?controller=auth&action=logout
```

---

## 👤 Dashboard Pages (Login Required)

### Dashboard (Auto-redirects based on role)
```
http://localhost/projecty/public/index.php?controller=dashboard&action=index
```

### Admin Dashboard
```
http://localhost/projecty/public/index.php?controller=dashboard&action=admin
```

### Instructor Dashboard
```
http://localhost/projecty/public/index.php?controller=dashboard&action=instructor
```

### Student Dashboard
```
http://localhost/projecty/public/index.php?controller=dashboard&action=student
```

---

## 📊 CRUD API Endpoints (JSON Responses)

### Users CRUD

**List All Users**
```
http://localhost/projecty/public/index.php?controller=crud&action=index&entity=user
```

**Get User by ID**
```
http://localhost/projecty/public/index.php?controller=crud&action=show&entity=user&id=1
```

**Create User** (POST request required)
```
http://localhost/projecty/public/index.php?controller=crud&action=create&entity=user
```
POST Data:
```json
{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "role": "student",
  "status": "active"
}
```

**Update User** (POST request required)
```
http://localhost/projecty/public/index.php?controller=crud&action=update&entity=user&id=1
```

**Delete User** (POST request required)
```
http://localhost/projecty/public/index.php?controller=crud&action=delete&entity=user&id=1
```

---

### Courses CRUD

**List All Courses**
```
http://localhost/projecty/public/index.php?controller=crud&action=index&entity=course
```

**Get Course by ID**
```
http://localhost/projecty/public/index.php?controller=crud&action=show&entity=course&id=1
```

**Create Course** (POST request required)
```
http://localhost/projecty/public/index.php?controller=crud&action=create&entity=course
```
POST Data:
```json
{
  "course_code": "CS101",
  "course_name": "Introduction to Computer Science",
  "instructor_id": 2,
  "credits": 3
}
```

**Update Course** (POST request required)
```
http://localhost/projecty/public/index.php?controller=crud&action=update&entity=course&id=1
```

**Delete Course** (POST request required)
```
http://localhost/projecty/public/index.php?controller=crud&action=delete&entity=course&id=1
```

---

### Grades CRUD

**List All Grades**
```
http://localhost/projecty/public/index.php?controller=crud&action=index&entity=grade
```

**Get Grade by ID**
```
http://localhost/projecty/public/index.php?controller=crud&action=show&entity=grade&id=1
```

**Create Grade** (POST request required)
```
http://localhost/projecty/public/index.php?controller=crud&action=create&entity=grade
```
POST Data:
```json
{
  "student_id": 1,
  "course_id": 1,
  "grade": 85,
  "max_grade": 100,
  "assignment_type": "exam"
}
```

**Update Grade** (POST request required)
```
http://localhost/projecty/public/index.php?controller=crud&action=update&entity=grade&id=1
```

**Delete Grade** (POST request required)
```
http://localhost/projecty/public/index.php?controller=crud&action=delete&entity=grade&id=1
```

---

### Contact Messages CRUD

**List All Contact Messages**
```
http://localhost/projecty/public/index.php?controller=crud&action=index&entity=contact
```

**Get Contact Message by ID**
```
http://localhost/projecty/public/index.php?controller=crud&action=show&entity=contact&id=1
```

---

## 🎛️ Menu Management (Admin Only)

### View Menu Items
```
http://localhost/projecty/public/index.php?controller=menu&action=index
```

### Get Menu JSON (for current user role)
```
http://localhost/projecty/public/index.php?controller=menu&action=getMenu
```

---

## 🧪 Unit Tests

### Run All Tests
```bash
php tests/run-tests.php
```

### Run Validator Tests Only
```bash
php tests/Unit/ValidatorTest.php
```

### Run Factory Tests Only
```bash
php tests/Unit/ModelFactoryTest.php
```

---

## 🔑 Default Login Credentials

### Admin Account
```
Email: admin@edupredict.edu
Password: admin123
```

### Instructor Account
```
Email: instructor@edupredict.edu
Password: instructor123
```

### Student Account
```
Email: student@edupredict.edu
Password: student123
```

---

## 📝 Testing Checklist

### ✅ Authentication & Roles
- [ ] Login with admin credentials
- [ ] Login with instructor credentials
- [ ] Login with student credentials
- [ ] Try accessing admin dashboard as student (should redirect)
- [ ] Register new user
- [ ] Logout functionality

### ✅ Dynamic Menu
- [ ] View menu as guest (public items only)
- [ ] View menu as student (student + public items)
- [ ] View menu as instructor (instructor + public items)
- [ ] View menu as admin (admin + public items)
- [ ] Admin: Access menu management page
- [ ] Admin: Create new menu item
- [ ] Admin: Update menu item
- [ ] Admin: Delete menu item

### ✅ CRUD Operations
- [ ] List all users (GET)
- [ ] Get user by ID (GET)
- [ ] Create new user (POST)
- [ ] Update user (POST)
- [ ] Delete user (POST)
- [ ] Repeat for courses, grades, contacts

### ✅ Data Validation
- [ ] Submit invalid email format (should show error)
- [ ] Submit password less than 6 characters (should show error)
- [ ] Submit empty required fields (should show error)
- [ ] Submit valid data (should succeed)

### ✅ Design Patterns
- [ ] Test ModelFactory creates correct model instances
- [ ] Test ModelFactory singleton behavior
- [ ] Test ValidationStrategy for different entities
- [ ] Verify Database singleton pattern

### ✅ Unit Tests
- [ ] Run ValidatorTest
- [ ] Run ModelFactoryTest
- [ ] Run all tests via run-tests.php

---

## 🛠️ Testing Tools

### Browser Testing
- Use Chrome/Firefox Developer Tools
- Check Network tab for API responses
- Check Console for JavaScript errors

### API Testing Tools
- **Postman**: For testing POST/PUT/DELETE requests
- **cURL**: Command-line testing
- **Browser**: For GET requests

### cURL Examples

**Get Users List:**
```bash
curl "http://localhost/projecty/public/index.php?controller=crud&action=index&entity=user"
```

**Create User:**
```bash
curl -X POST "http://localhost/projecty/public/index.php?controller=crud&action=create&entity=user" \
  -d "name=Test User" \
  -d "email=test@example.com" \
  -d "password=test123" \
  -d "role=student"
```

---

## ⚠️ Important Notes

1. **All CRUD endpoints require login** - Make sure you're logged in first
2. **POST requests** - Use form submission or API tools (Postman/cURL)
3. **JSON responses** - CRUD endpoints return JSON, not HTML
4. **Role-based access** - Some endpoints require specific roles
5. **Database must be set up** - Run migrations before testing

---

## 🐛 Troubleshooting

### "Controller not found" error
- Check URL spelling: `controller=home` not `controller=Home`
- Verify controller file exists in `app/controllers/`

### "Action not found" error
- Check action name spelling
- Verify method exists in controller class

### "Database connection failed"
- Start MySQL in XAMPP Control Panel
- Run `quick-setup.php` to create database

### Menu not showing
- Run `database/menu_items_migration.sql`
- Check database connection

### CRUD returns 404
- Ensure you're logged in
- Check entity name is correct (user, course, grade, contact)

---

## 📞 Quick Reference

**Base URL:** `http://localhost/projecty/public/index.php`

**Format:** `?controller={name}&action={method}&entity={type}&id={number}`

**Example:** `?controller=crud&action=show&entity=user&id=1`

