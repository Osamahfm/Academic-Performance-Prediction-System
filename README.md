# EduPredict - Academic Performance Prediction System

## 🚀 Quick Start

### Main Application
```
http://localhost/projecty/public/index.php
```

### Database Setup
```
http://localhost/projecty/utilities/quick-setup.php
```

## 📚 Documentation

All documentation is in the `docs/` folder:

- **[Quick Start Guide](docs/QUICK_START_PHASE2.md)** - Get started quickly
- **[Testing Guide](docs/TESTING_LINKS.md)** - All test links and API endpoints
- **[MVC Guide](docs/MVC_GUIDE.md)** - MVC architecture documentation
- **[Phase 2 Implementation](docs/PHASE2_IMPLEMENTATION.md)** - Phase 2 features
- **[Project Structure](PROJECT_STRUCTURE.md)** - Project organization

### Security & Validation Notes

- HTTPS enforcement is configurable via `FORCE_HTTPS` in `app/config/config.php` (disabled by default for localhost).
- Client-side validation is implemented in `public/assets/js/script.js` for login, registration, and admin user CRUD forms, in addition to full server-side validation.

## 🏗️ Project Structure

```
projecty/
├── app/          # MVC Application (Models, Views, Controllers)
├── public/       # Web root (front controller + assets)
├── utilities/    # Setup scripts and tools
├── docs/         # Documentation
└── tests/        # Unit tests
```

## 🔑 Default Login Credentials

- **Admin**: admin@edupredict.edu / admin123
- **Instructor**: instructor@edupredict.edu / instructor123
- **Student**: student@edupredict.edu / student123

## ✨ Features

- ✅ Strict MVC Architecture
- ✅ Role-Based Authentication
- ✅ Dynamic Menu System
- ✅ Full CRUD Operations
- ✅ Data Validation
- ✅ Design Patterns (Factory, Strategy, Singleton)
- ✅ Unit Tests

## 📖 More Information

See [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) for detailed structure information.
