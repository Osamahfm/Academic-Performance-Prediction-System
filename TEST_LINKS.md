# 🧪 Test Links - Updated with Routes

## 🚀 Base URL
```
http://localhost/projecty/public/
```

---

## 🏠 Public Pages (Clean URLs)

### Home Page
```
http://localhost/projecty/public/
```
or
```
http://localhost/projecty/public/home
```

### About Page
```
http://localhost/projecty/public/about
```

### Services Page
```
http://localhost/projecty/public/services
```

### Portfolio Page
```
http://localhost/projecty/public/portfolio
```

### Contact Page
```
http://localhost/projecty/public/contact
```

---

## 🔐 Authentication Pages

### Login Page
```
http://localhost/projecty/public/login
```

### Register Page
```
http://localhost/projecty/public/register
```

### Logout
```
http://localhost/projecty/public/logout
```

---

## 👤 Dashboard Pages (Login Required)

### Dashboard (Auto-redirects based on role)
```
http://localhost/projecty/public/dashboard
```

### Admin Dashboard
```
http://localhost/projecty/public/dashboard/admin
```

### Instructor Dashboard
```
http://localhost/projecty/public/dashboard/instructor
```

### Student Dashboard
```
http://localhost/projecty/public/dashboard/student
```

---

## 📊 CRUD API Endpoints (Clean URLs)

### Users API

**List All Users**
```
http://localhost/projecty/public/api/user
```

**Get User by ID**
```
http://localhost/projecty/public/api/user/1
```

**Create User** (POST request)
```
POST http://localhost/projecty/public/api/user
```

**Update User** (POST request)
```
POST http://localhost/projecty/public/api/user/1
```

**Delete User** (DELETE request)
```
DELETE http://localhost/projecty/public/api/user/1
```

---

### Courses API

**List All Courses**
```
http://localhost/projecty/public/api/course
```

**Get Course by ID**
```
http://localhost/projecty/public/api/course/1
```

**Create Course** (POST request)
```
POST http://localhost/projecty/public/api/course
```

**Update Course** (POST request)
```
POST http://localhost/projecty/public/api/course/1
```

**Delete Course** (DELETE request)
```
DELETE http://localhost/projecty/public/api/course/1
```

---

### Grades API

**List All Grades**
```
http://localhost/projecty/public/api/grade
```

**Get Grade by ID**
```
http://localhost/projecty/public/api/grade/1
```

**Create Grade** (POST request)
```
POST http://localhost/projecty/public/api/grade
```

**Update Grade** (POST request)
```
POST http://localhost/projecty/public/api/grade/1
```

**Delete Grade** (DELETE request)
```
DELETE http://localhost/projecty/public/api/grade/1
```

---

## 🎛️ Menu Management (Admin Only)

### View Menu Items
```
http://localhost/projecty/public/admin/menu
```

### Get Menu JSON
```
http://localhost/projecty/public/api/menu
```

---

## 🔄 Backward Compatible URLs (Still Work)

### Query String Format
```
http://localhost/projecty/public/index.php?controller=home&action=index
http://localhost/projecty/public/index.php?controller=auth&action=login
http://localhost/projecty/public/index.php?controller=page&action=about
http://localhost/projecty/public/index.php?controller=dashboard&action=admin
```

### CRUD Query String Format
```
http://localhost/projecty/public/index.php?controller=crud&action=index&entity=user
http://localhost/projecty/public/index.php?controller=crud&action=show&entity=user&id=1
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

## 🧪 Quick Test Checklist

### ✅ Test Clean URLs
- [ ] `http://localhost/projecty/public/` - Homepage
- [ ] `http://localhost/projecty/public/about` - About page
- [ ] `http://localhost/projecty/public/login` - Login page
- [ ] `http://localhost/projecty/public/dashboard` - Dashboard (after login)

### ✅ Test API Routes
- [ ] `http://localhost/projecty/public/api/user` - List users (GET)
- [ ] `http://localhost/projecty/public/api/user/1` - Get user by ID (GET)
- [ ] `http://localhost/projecty/public/api/course` - List courses (GET)

### ✅ Test Authentication
- [ ] Login with admin credentials
- [ ] Login with instructor credentials
- [ ] Login with student credentials
- [ ] Access dashboard (should redirect based on role)

### ✅ Test CRUD Operations
- [ ] GET `/api/user` - List all users
- [ ] GET `/api/user/1` - Get specific user
- [ ] POST `/api/user` - Create user (use Postman/cURL)
- [ ] POST `/api/user/1` - Update user (use Postman/cURL)
- [ ] DELETE `/api/user/1` - Delete user (use Postman/cURL)

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
curl "http://localhost/projecty/public/api/user"
```

**Create User:**
```bash
curl -X POST "http://localhost/projecty/public/api/user" \
  -d "name=Test User" \
  -d "email=test@example.com" \
  -d "password=test123" \
  -d "role=student"
```

**Update User:**
```bash
curl -X POST "http://localhost/projecty/public/api/user/1" \
  -d "name=Updated Name" \
  -d "email=updated@example.com"
```

**Delete User:**
```bash
curl -X DELETE "http://localhost/projecty/public/api/user/1"
```

---

## 📝 Route Examples

### Simple Routes
```
GET  /about      → PageController::about()
GET  /login      → AuthController::login()
GET  /dashboard  → DashboardController::index()
```

### Routes with Parameters
```
GET  /api/user/1           → CrudController::show('user', '1')
GET  /api/course/5         → CrudController::show('course', '5')
GET  /dashboard/admin      → DashboardController::admin()
```

---

## ⚠️ Important Notes

1. **Clean URLs require `.htaccess`** - Make sure mod_rewrite is enabled
2. **API endpoints return JSON** - Use browser or API tools
3. **POST/DELETE requests** - Use Postman, cURL, or form submission
4. **Login required** - Some routes require authentication
5. **Role-based access** - Some routes require specific roles

---

## 🐛 Troubleshooting

### Routes not working?
- Check Apache `mod_rewrite` is enabled
- Verify `.htaccess` files exist in `public/` folder
- Check Apache `AllowOverride All` is set

### 404 errors?
- Verify route exists in `app/config/routes.php`
- Check controller and action method exist
- Check URL path matches route definition

### Query string still works?
- Yes! Both formats work for backward compatibility
- Clean URLs are preferred
- Query strings are fallback

---

## 📚 Documentation

- **Routes Guide**: `docs/ROUTES_GUIDE.md`
- **Router Explanation**: `docs/ROUTER_EXPLANATION.md`
- **Testing Guide**: `docs/TESTING_LINKS.md`



