# EduPredict - Academic Performance Prediction System

A comprehensive PHP-based web application for predicting academic performance using machine learning algorithms, with role-based access control and full CRUD operations.

![Language](https://img.shields.io/badge/language-PHP-blue?style=flat-square&logo=php)
![Languages](https://img.shields.io/badge/also%20uses-Shell%20%7C%20CSS%20%7C%20JavaScript-lightgrey?style=flat-square)
![Status](https://img.shields.io/badge/status-Active-success?style=flat-square)

---

## 📋 Table of Contents

- [Quick Start](#-quick-start)
- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [Project Structure](#-project-structure)
- [Default Login Credentials](#-default-login-credentials)
- [Installation & Setup](#-installation--setup)
- [Documentation](#-documentation)
- [Security](#-security--validation)
- [Testing](#-testing)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🚀 Quick Start

### Main Application
```
http://localhost/projecty/public/index.php
```

### Database Setup
```
http://localhost/projecty/utilities/quick-setup.php
```

---

## ✨ Features

### Core Functionality
- ✅ **Strict MVC Architecture** - Clean separation of concerns
- ✅ **Role-Based Authentication** - Admin, Instructor, and Student roles
- ✅ **Dynamic Menu System** - Context-aware navigation
- ✅ **Full CRUD Operations** - Create, Read, Update, Delete for all entities
- ✅ **Data Validation** - Client-side and server-side validation
- ✅ **Design Patterns** - Factory, Strategy, and Singleton patterns

### Academic Features
- ✅ **ML-Powered Predictions** - KNN algorithm for performance forecasting
- ✅ **Risk Factor Analysis** - Identify at-risk students early
- ✅ **Grade Management** - Track and analyze student grades
- ✅ **Course Management** - Manage courses and assignments
- ✅ **Student Performance Tracking** - Monitor academic progress

### Security
- ✅ **Comprehensive Validation** - Input and data validation
- ✅ **HTTPS Support** - Configurable HTTPS enforcement
- ✅ **Secure Authentication** - Role-based access control

---

## 🛠️ Technology Stack

| Category | Technologies |
|----------|--------------|
| **Backend** | PHP (88.7%) |
| **Frontend** | JavaScript (2.6%), CSS (3%) |
| **Scripting** | Shell (3.6%), Batchfile (0.7%), Perl (0.8%) |
| **Other** | Hack (0.6%) |

---

## 🏗️ Project Structure

```
projecty/
├── app/              # MVC Application Core
│   ├── models/       # Data models and business logic
│   ├── views/        # View templates
│   ├── controllers/  # Request handlers
│   └── config/       # Configuration files
├── public/           # Web Root (Front Controller)
│   ├── index.php     # Application entry point
│   └── assets/       # CSS, JavaScript, images
├── utilities/        # Setup scripts and tools
│   └── quick-setup.php
├── docs/             # Comprehensive Documentation
├── tests/            # Unit Test Suite
└── README.md         # This file
```

---

## 🔑 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@edupredict.edu | admin123 |
| **Instructor** | instructor@edupredict.edu | instructor123 |
| **Student** | student@edupredict.edu | student123 |

> ⚠️ **Important**: Change these credentials in production environments!

---

## 💻 Installation & Setup

### Prerequisites
- PHP 7.4+
- MySQL/MariaDB
- Web Server (Apache/Nginx)
- Composer (optional)

### Step-by-Step Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Osamahfm/Academic-Performance-Prediction-System.git
   cd Academic-Performance-Prediction-System
   ```

2. **Database Setup**
   - Navigate to `http://localhost/projecty/utilities/quick-setup.php`
   - Follow the setup wizard to create and populate the database

3. **Configure Application**
   - Edit `app/config/config.php` if needed
   - Set `FORCE_HTTPS` option for your environment
   - Configure database credentials

4. **Access the Application**
   - Open `http://localhost/projecty/public/index.php`
   - Login with default credentials (see table above)

---

## 📚 Documentation

All detailed documentation is available in the `docs/` folder:

| Document | Purpose |
|----------|---------|
| **[Quick Start Guide](docs/QUICK_START_PHASE2.md)** | Get up and running quickly |
| **[MVC Guide](docs/MVC_GUIDE.md)** | Understanding the MVC architecture |
| **[Testing Guide](docs/TESTING_LINKS.md)** | All test links and API endpoints |
| **[Phase 2 Implementation](docs/PHASE2_IMPLEMENTATION.md)** | Phase 2 features and enhancements |
| **[Project Structure](PROJECT_STRUCTURE.md)** | Detailed project organization |
| **[Unit Test Documentation](tests/README.md)** | Testing framework and test coverage |

### Key Documentation Highlights

- **MVC Architecture**: Complete guide to the application's design patterns
- **API Endpoints**: Comprehensive list of all testing and API links
- **Test Coverage**: 60+ unit tests covering core functionality
- **Validation**: Detailed validation rules for all entity types

---

## 🔐 Security & Validation

### Client-Side Validation
- Form validation implemented in `public/assets/js/script.js`
- Covers login, registration, and admin CRUD operations
- Real-time error feedback

### Server-Side Validation
- Full input validation on all requests
- Entity-specific validation strategies
- Comprehensive error handling

### HTTPS Configuration
- HTTPS enforcement is configurable via `FORCE_HTTPS` in `app/config/config.php`
- **Disabled by default** for localhost development
- **Enabled by default** for production environments

### Best Practices
- Never use default credentials in production
- Keep database credentials secure
- Regularly update PHP and dependencies
- Use HTTPS in production

---

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php tests/run-tests.php
```

### Test Coverage
The application includes comprehensive unit tests for:
- **Machine Learning Algorithm** (KNN Predictor)
- **Validation Logic** (All entity types)
- **Grade Calculations** (Student and course averages)
- **Prediction Service** (Risk factor identification)
- **Model Factory** (Factory pattern implementation)
- **Data Validators** (Input validation rules)

### Test Results
When tests pass:
```
✅ All tests passed!
```

When tests fail:
```
❌ Test failed: {error message}
```

See [tests/README.md](tests/README.md) for detailed test documentation.

---

## 🎯 Key Components

### Machine Learning
- **KNN Algorithm**: Predicts student academic performance
- **Risk Analysis**: Identifies at-risk students based on multiple factors
- **Performance Metrics**: Confidence scores and risk levels

### Database Schema
- **Users**: Admin, Instructor, Student roles
- **Courses**: Course information and credits
- **Grades**: Student grades for assignments and courses
- **Predictions**: ML prediction results and analysis

### API Endpoints
Comprehensive API endpoints for:
- User authentication and management
- Course and grade operations
- Prediction and risk analysis
- Admin functions

See [docs/TESTING_LINKS.md](docs/TESTING_LINKS.md) for complete endpoint list.

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards
- Include unit tests for new features
- Update documentation as needed
- Ensure all tests pass before submitting PR

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## 📞 Support & Issues

- 📧 For issues, please open a [GitHub Issue](https://github.com/Osamahfm/Academic-Performance-Prediction-System/issues)
- 📖 Check the [documentation](docs/) first
- 🧪 Review [test examples](tests/) for usage patterns

---

## 🚀 Roadmap

- [ ] Enhanced ML algorithms (Random Forest, Neural Networks)
- [ ] Dashboard analytics and reporting
- [ ] Mobile application
- [ ] Real-time notifications
- [ ] Advanced data visualization
- [ ] API rate limiting
- [ ] Automated backups

---

## 👨‍💻 Author

**Osama H.F.M** - [GitHub](https://github.com/Osamahfm)

---

**Last Updated**: 2026-05-05 | **Version**: 1.0.0
