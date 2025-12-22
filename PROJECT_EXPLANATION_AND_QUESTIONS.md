# 📚 EduPredict - Academic Performance Prediction System
## Detailed Project Explanation & Expected Questions

---

## 🎯 PROJECT OVERVIEW

**EduPredict** is a web-based academic performance prediction system that uses machine learning (K-Nearest Neighbors algorithm) to predict student academic performance and identify at-risk students. The system helps educational institutions make data-driven decisions to improve student outcomes through early intervention.

### Core Purpose
- **Predict student academic performance** using ML algorithms
- **Identify at-risk students** before they fail or drop out
- **Provide actionable insights** to educators and administrators
- **Enable early intervention** for struggling students
- **Improve educational outcomes** through data-driven decision making

---

## 🏗️ SYSTEM ARCHITECTURE

### Technology Stack

**Backend:**
- **PHP 8.2+** (Object-Oriented Programming)
- **MySQL 8.0+** (Relational Database)
- **Apache HTTP Server** (Web Server)
- **PDO** (Database Access Layer)

**Frontend:**
- **HTML5** (Structure)
- **CSS3** (Responsive Design, Glass-morphism UI)
- **JavaScript (ES6+)** (Client-side validation, interactivity)
- **Chart.js** (Data Visualization)
- **Font Awesome** (Icons)

**Development Environment:**
- **XAMPP** (Local development stack)

### Architecture Pattern: MVC (Model-View-Controller)

The system follows strict MVC architecture:

1. **Model Layer** (`app/models/`):
   - Handles all database operations
   - Business logic and data validation
   - Examples: `UserModel`, `StudentModel`, `GradeModel`, `PredictionModel`

2. **View Layer** (`app/views/`):
   - Presentation layer (HTML/PHP templates)
   - Role-specific dashboards
   - Responsive UI components

3. **Controller Layer** (`app/controllers/`):
   - Handles HTTP requests
   - Coordinates between Models and Views
   - Implements business logic
   - Examples: `AuthController`, `DashboardController`, `PredictionController`

4. **Core Framework** (`app/core/`):
   - `Router.php` - URL routing and request dispatching
   - `Database.php` - Singleton pattern for database connection
   - `Controller.php` - Base controller class
   - `Model.php` - Base model class
   - `Validator.php` - Input validation

---

## 🔐 USER ROLES & PERMISSIONS

### 1. Administrator
**Capabilities:**
- Full system access and configuration
- User management (create, edit, delete users)
- Course creation and assignment to instructors
- Student enrollment management
- KNN model training and retraining
- System-wide analytics and reports
- Database administration tools
- View all predictions and risk assessments

**Default Credentials:**
- Email: `admin@edupredict.edu`
- Password: `admin123`

### 2. Instructor
**Capabilities:**
- View assigned courses and enrolled students
- Enter and manage student grades
- View student performance predictions
- Identify at-risk students in their courses
- Receive automated alerts for high-risk students
- View course-specific analytics
- Manage course alerts and notifications

**Default Credentials:**
- Email: `instructor@edupredict.edu`
- Password: `instructor123`

### 3. Student
**Capabilities:**
- View personal dashboard with current GPA and attendance
- Access predicted GPA and performance trends
- View individual course grades and assignments
- Review risk factors and improvement recommendations
- Monitor academic progress over time
- View course-specific predictions

**Default Credentials:**
- Email: `student@edupredict.edu`
- Password: `student123`

---

## 🤖 MACHINE LEARNING IMPLEMENTATION

### K-Nearest Neighbors (KNN) Algorithm

**Location:** `app/core/ML/KNNPredictor.php`

**How It Works:**

1. **Training Data Preparation:**
   - Collects all students with complete data (GPA, attendance, grades)
   - Each student is a training example with features:
     - GPA (0.00 - 4.00)
     - Attendance Rate (0% - 100%)
     - Average Grade (0 - 100)
     - Assignments Completed (count)

2. **Feature Normalization:**
   - Normalizes all features to 0-1 range for fair distance calculation
   - Prevents features with larger scales from dominating

3. **Prediction Process:**
   - Calculates Euclidean distance between target student and all training students
   - Selects K nearest neighbors (default K=5)
   - Uses weighted voting based on inverse distance
   - Predicts risk level (low/medium/high) based on majority vote
   - Calculates predicted grade using weighted average of neighbors

4. **Risk Assessment:**
   - **Low Risk:** GPA ≥ 3.0, good attendance, good grades
   - **Medium Risk:** GPA 2.0-3.0, moderate performance
   - **High Risk:** GPA < 2.0, poor attendance, low grades

**Key Methods:**
- `loadTrainingData($data)` - Loads training dataset
- `predict($studentFeatures)` - Predicts performance for a student
- `predictCourseGrade($studentFeatures, $courseGrades)` - Course-specific prediction
- `euclideanDistance($point1, $point2)` - Calculates distance between feature vectors
- `normalizeFeatures($features, $minMax)` - Normalizes features to 0-1 range

### Prediction Service

**Location:** `app/services/PredictionService.php`

**Responsibilities:**
- Coordinates prediction workflow
- Prepares training data from database
- Extracts student features
- Calculates GPA from actual grades
- Identifies risk factors
- Generates alerts for high-risk students
- Saves predictions to database

**Key Methods:**
- `predictPerformance($studentId, $courseId)` - Main prediction method
- `predictAllStudents()` - Batch prediction for all students
- `predictCourseStudents($courseId)` - Predictions for course students
- `identifyRiskFactors($features, $prediction, $studentId)` - Risk factor analysis
- `gradeToGpa($grade)` - Converts percentage grade to 4.0 GPA scale

---

## 🗄️ DATABASE SCHEMA

### Core Tables

1. **users**
   - Stores authentication and profile information
   - Fields: id, username, email, password (hashed), name, role, created_at, updated_at
   - Roles: admin, instructor, student

2. **students**
   - Student-specific academic data
   - Fields: id, user_id (FK), student_id, gpa, attendance_rate, risk_level, created_at, updated_at
   - Links to users table (one-to-one)

3. **courses**
   - Course information and instructor assignments
   - Fields: id, course_code, course_name, instructor_id (FK), credits, description, created_at, updated_at

4. **enrollments**
   - Many-to-many relationship between students and courses
   - Fields: id, student_id (FK), course_id (FK), status, enrolled_at, updated_at
   - Status: active, completed, dropped

5. **grades**
   - Individual grade entries for assignments/exams
   - Fields: id, student_id (FK), course_id (FK), grade, max_grade, assignment_type, description, graded_at, created_at, updated_at

6. **predictions**
   - ML prediction results
   - Fields: id, student_id (FK), course_id (FK, nullable), predicted_gpa, predicted_grade, risk_level, risk_factors (JSON), trend, gpa_change, created_at, updated_at

7. **alerts**
   - Automated alerts for at-risk students
   - Fields: id, student_id (FK), instructor_id (FK), course_id (FK, nullable), message, status, created_at, updated_at
   - Status: active, acknowledged, dismissed

8. **menu_items**
   - Dynamic menu system (self-referential)
   - Fields: id, parent_id (FK to menu_items.id), title, url, icon, role, order, created_at

---

## 🎨 DESIGN PATTERNS IMPLEMENTED

### 1. Singleton Pattern
**Location:** `app/core/Database.php`
- Ensures only one database connection instance
- Prevents multiple connections and resource waste
- Provides global access point: `Database::getInstance()`

### 2. Factory Pattern
**Location:** `app/core/Factory/ModelFactory.php`
- Creates model instances dynamically based on type string
- Caches instances for performance
- Centralizes model creation logic
- Usage: `ModelFactory::create('user')` returns `UserModel` instance

### 3. Strategy Pattern
**Location:** `app/core/Strategy/ValidationStrategy.php`
- Different validation strategies for different entities
- Easy to extend with new validation rules
- Implementations: `UserValidationStrategy`, `CourseValidationStrategy`, `GradeValidationStrategy`

### 4. MVC Pattern
- Separation of concerns across the entire application
- Models handle data, Views handle presentation, Controllers handle logic

---

## 🔒 SECURITY FEATURES

1. **Password Hashing:**
   - Uses PHP's `password_hash()` and `password_verify()`
   - Bcrypt algorithm for secure password storage

2. **SQL Injection Prevention:**
   - All database queries use PDO prepared statements
   - Parameterized queries prevent SQL injection

3. **XSS Protection:**
   - Input sanitization in `Validator` class
   - Output escaping in views

4. **Session Management:**
   - Secure session handling
   - Role-based access control (RBAC)
   - Authentication middleware

5. **Input Validation:**
   - Client-side validation (JavaScript)
   - Server-side validation (PHP Validator class)
   - Strategy pattern for entity-specific validation

---

## 📊 KEY FEATURES

### 1. Role-Based Dashboards

**Admin Dashboard:**
- System-wide statistics (total users, courses, students)
- User distribution charts (Chart.js)
- Risk level distribution visualization
- Quick actions (create user, create course, train model)
- Recent activity monitoring

**Instructor Dashboard:**
- Assigned courses overview
- At-risk students list
- Active alerts count
- Average class GPA
- Recent grades section

**Student Dashboard:**
- Current GPA with trend indicators
- Predicted GPA comparison
- Attendance rate visualization
- Enrolled courses list
- Risk factors and recommendations
- Performance trend charts

### 2. Prediction System

**Automatic Predictions:**
- Triggered when grades are added
- Triggered when students are enrolled
- Can be manually triggered via training page

**Prediction Features:**
- Overall GPA prediction
- Course-specific GPA prediction
- Risk level assessment (low/medium/high)
- Risk factor identification
- GPA trend analysis (increasing/decreasing/stable)
- Confidence scoring

### 3. Alert System

**Automated Alerts:**
- Generated when student risk level becomes "high"
- Sent to instructors of affected courses
- Includes risk factors and recommendations
- Can be acknowledged or dismissed

### 4. Dynamic Menu System

**Features:**
- Database-driven menu items
- Self-referential structure (parent-child relationships)
- Role-based menu filtering
- Hierarchical menu rendering
- Icon support (Font Awesome)

### 5. CRUD Operations

**Generic CRUD Controller:**
- Handles Create, Read, Update, Delete for all entities
- Entity-agnostic design
- Supports: Users, Courses, Grades, Contacts, Menu Items
- Full validation integration

---

## 🧪 TESTING

**Test Suite Location:** `tests/Unit/`

**Unit Tests:**
- `ValidatorTest.php` - Input validation tests
- `ModelFactoryTest.php` - Factory pattern tests
- `KNNPredictorTest.php` - ML algorithm tests
- `PredictionServiceTest.php` - Prediction service tests
- `ValidationStrategyTest.php` - Strategy pattern tests

**Test Runner:** `tests/run-tests.php`

---

## 📈 WORKFLOW EXAMPLES

### Example 1: Adding a Grade and Triggering Prediction

1. Instructor logs in
2. Navigates to Grade Management
3. Selects course and student
4. Enters grade (e.g., 85/100 for Quiz)
5. System automatically:
   - Saves grade to database
   - Triggers prediction update
   - Calculates new GPA
   - Updates risk level if needed
   - Generates alert if high risk
6. Prediction appears in dashboard

### Example 2: Training the KNN Model

1. Admin logs in
2. Navigates to Training page
3. System checks training data:
   - At least 5 students with complete data
   - Students have GPA, attendance, and grades
4. Admin clicks "Train KNN Model"
5. System:
   - Loads all student data as training examples
   - Runs predictions for all students
   - Updates prediction records
   - Displays training statistics

### Example 3: Student Viewing Predictions

1. Student logs in
2. Views dashboard:
   - Current GPA: 3.20
   - Predicted GPA: 3.45 (+0.25)
   - Trend: Increasing
   - Risk Level: Low
3. Clicks "View Predictions"
4. Sees:
   - Overall prediction
   - Course-specific predictions
   - Risk factors (if any)
   - Recommendations

---

## 🚀 INSTALLATION & SETUP

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- PHP 8.2+
- MySQL 8.0+
- Web browser

### Setup Steps

1. **Install XAMPP**
   - Download and install XAMPP
   - Start Apache and MySQL services

2. **Clone/Copy Project**
   - Place project in `C:\xampp\htdocs\projecty\`

3. **Database Setup**
   - Visit: `http://localhost/projecty/utilities/quick-setup.php`
   - This creates database, tables, and sample data

4. **Access Application**
   - Homepage: `http://localhost/projecty/public/index.php`
   - Login: Use default credentials

---

## 📝 EXPECTED QUESTIONS & ANSWERS

### Architecture & Design Questions

**Q1: Why did you choose MVC architecture?**
**A:** MVC provides clear separation of concerns, making the codebase maintainable and scalable. It separates data access (Models), business logic (Controllers), and presentation (Views), allowing for easier testing, debugging, and future enhancements.

**Q2: Explain the Singleton pattern implementation in your Database class.**
**A:** The Singleton pattern ensures only one database connection exists throughout the application lifecycle. This prevents resource waste, maintains connection pooling, and provides a single point of access via `Database::getInstance()`. The private constructor prevents direct instantiation, and static `getInstance()` method returns the single instance.

**Q3: How does the Factory pattern help in your application?**
**A:** The `ModelFactory` centralizes model creation logic. Instead of manually instantiating models throughout the codebase, we use `ModelFactory::create('user')` which returns the appropriate model instance. This makes the code more maintainable, allows for easy extension, and enables instance caching for performance.

**Q4: What is the Strategy pattern used for?**
**A:** The Strategy pattern is used for validation. Different entities (User, Course, Grade) have different validation rules. Instead of having one large validation method with conditionals, we have separate strategy classes (`UserValidationStrategy`, `CourseValidationStrategy`) that implement the same interface but with entity-specific rules. This makes validation extensible and maintainable.

**Q5: How does your routing system work?**
**A:** The `Router` class in `app/core/Router.php` parses URL query parameters (`?controller=name&action=method`). It identifies the controller class, instantiates it, checks authentication/authorization, and calls the appropriate action method. All requests go through `public/index.php` (front controller pattern).

### Machine Learning Questions

**Q6: Why did you choose KNN for this project?**
**A:** KNN is simple, interpretable, and doesn't require training a model beforehand. It works well with the available features (GPA, attendance, grades) and provides good results for classification (risk levels). It's also easy to implement in PHP and doesn't require heavy ML libraries.

**Q7: How does your KNN algorithm work?**
**A:** 
1. Load all students with complete data as training examples
2. For a new student, calculate Euclidean distance to all training students using normalized features (GPA, attendance, avg grade, assignments)
3. Select K=5 nearest neighbors
4. Use weighted voting (inverse distance) to predict risk level
5. Calculate predicted grade using weighted average of neighbors' grades
6. Return prediction with confidence score

**Q8: How do you handle feature normalization?**
**A:** Features are normalized to 0-1 range using min-max normalization: `(value - min) / (max - min)`. This ensures features with different scales (GPA 0-4, attendance 0-100) contribute equally to distance calculations.

**Q9: What features do you use for prediction?**
**A:** Four features:
- **GPA** (0.00-4.00): Overall academic performance
- **Attendance Rate** (0-100%): Class participation
- **Average Grade** (0-100): Average of all assignment grades
- **Assignments Completed** (count): Number of completed assignments

**Q10: How do you calculate predicted GPA?**
**A:** Predicted GPA is calculated from actual final grades, not predicted grades. For each enrolled course, we calculate the average grade percentage from all actual grades, convert to GPA using grade-to-GPA mapping (A=4.0, B=3.0, C=2.0, D=1.0, F=0.0), then average all course GPAs to get overall GPA.

**Q11: How do you determine risk levels?**
**A:** Risk levels are determined by:
- Predicted GPA/grade thresholds (< 60 = high, 60-75 = medium, > 75 = low)
- Current GPA and attendance rates
- KNN prediction from similar students
- Risk factors (low attendance, incomplete assignments)

**Q12: What happens when a student is identified as high-risk?**
**A:** The system automatically:
1. Updates student's risk_level to "high" in database
2. Creates an alert record linked to the student and instructor
3. Displays alert in instructor dashboard
4. Includes risk factors and recommendations

### Database & Data Questions

**Q13: Explain your database schema design.**
**A:** The schema follows normalized relational design:
- **users**: Authentication and profiles (one-to-one with students)
- **students**: Student-specific academic data
- **courses**: Course information (many-to-one with instructors)
- **enrollments**: Many-to-many junction table (students ↔ courses)
- **grades**: Individual grade entries
- **predictions**: ML prediction results
- **alerts**: Risk notifications
- **menu_items**: Self-referential menu structure

**Q14: How do you prevent SQL injection?**
**A:** All database queries use PDO prepared statements with parameterized queries. For example: `$stmt->prepare("SELECT * FROM users WHERE id = :id"); $stmt->execute([':id' => $userId]);` This ensures user input is never directly concatenated into SQL queries.

**Q15: How is GPA calculated?**
**A:** GPA is calculated course-weighted (like real transcripts):
1. For each enrolled course, calculate average grade percentage from all grades
2. Convert percentage to GPA (90-100=A=4.0, 80-89=B=3.0, etc.)
3. Average all course GPAs to get overall GPA

### Security Questions

**Q16: How do you secure user passwords?**
**A:** Passwords are hashed using PHP's `password_hash()` with bcrypt algorithm. When verifying, we use `password_verify($password, $hash)`. Passwords are never stored in plain text.

**Q17: How do you implement role-based access control?**
**A:** 
- Each user has a `role` field (admin, instructor, student)
- Controllers use `requireRole($role)` method to check permissions
- Router checks authentication before routing
- Views conditionally render content based on role
- Menu items are filtered by role

**Q18: How do you prevent XSS attacks?**
**A:** 
- Input sanitization in `Validator` class using `htmlspecialchars()`
- Output escaping in views
- Never directly echo user input without escaping

### Functionality Questions

**Q19: How does the dynamic menu system work?**
**A:** Menu items are stored in `menu_items` table with self-referential `parent_id` foreign key. The `MenuModel` loads all items, builds a hierarchical tree structure, filters by user role, and renders the menu. This allows admins to manage menu items through the UI without code changes.

**Q20: When are predictions automatically triggered?**
**A:** Predictions are automatically triggered when:
- A grade is added (via `GradeModel::create()`)
- A student is enrolled in a course
- Admin manually triggers training via training page

**Q21: How do you handle students with insufficient data?**
**A:** If a student lacks GPA, attendance, or grades, the system:
- Shows "Insufficient data for prediction" message
- Uses default/fallback predictions if needed
- Excludes student from training data until data is complete
- Provides utilities to add missing data (GPA management page)

**Q22: How does the alert system work?**
**A:** 
- Alerts are automatically created when student risk_level changes to "high"
- Alerts are linked to student, instructor, and optional course
- Instructors see alerts in their dashboard
- Alerts can be acknowledged or dismissed
- Status tracking prevents duplicate alerts

### Testing Questions

**Q23: What testing approach did you use?**
**A:** Unit testing with PHPUnit for critical components:
- Validator class (input validation)
- ModelFactory (factory pattern)
- KNNPredictor (ML algorithm)
- PredictionService (business logic)
- ValidationStrategy (strategy pattern)

**Q24: How do you test the KNN algorithm?**
**A:** 
- Create test training data with known outcomes
- Test distance calculations
- Test normalization
- Test prediction accuracy with known test cases
- Verify risk level predictions match expectations

### Performance & Scalability Questions

**Q25: How would you optimize the system for large datasets?**
**A:** 
- Implement pagination for large lists
- Add database indexes on frequently queried columns
- Cache training data instead of loading every time
- Use database query optimization
- Consider implementing prediction caching
- Add database connection pooling

**Q26: What are the limitations of your current KNN implementation?**
**A:** 
- Requires loading all training data into memory
- Distance calculation is O(n) for each prediction
- No incremental learning (must retrain with all data)
- K value is fixed (could be optimized)
- No feature selection or weighting

**Q27: How would you improve the prediction accuracy?**
**A:** 
- Collect more training data
- Add more features (study hours, previous semester GPA, etc.)
- Implement feature weighting
- Optimize K value using cross-validation
- Consider ensemble methods
- Add time-series analysis for trends

### Future Enhancements Questions

**Q28: What features would you add next?**
**A:** 
- Email notifications for alerts
- Password recovery functionality
- Advanced reporting and data export
- Real-time dashboard updates
- Mobile-responsive improvements
- Integration with external LMS systems
- More sophisticated ML models (Random Forest, Neural Networks)

**Q29: How would you deploy this to production?**
**A:** 
- Set up production server (Linux, Apache/Nginx, MySQL)
- Configure HTTPS/SSL certificates
- Set up environment variables for configuration
- Implement database backups
- Add logging and error monitoring
- Set up CI/CD pipeline
- Configure security headers
- Disable debug mode

**Q30: What challenges did you face during development?**
**A:** 
- Implementing KNN algorithm in PHP (no ML libraries)
- Ensuring accurate GPA calculations
- Managing prediction triggers and updates
- Building dynamic menu system with self-referential structure
- Balancing automatic predictions with performance
- Testing ML algorithm with limited training data

---

## 📚 ADDITIONAL RESOURCES

- **Project Structure:** See `PROJECT_STRUCTURE.md`
- **MVC Guide:** See `docs/MVC_GUIDE.md`
- **KNN Guide:** See `docs/KNN_PREDICTION_GUIDE.md`
- **Software Design Document:** See `docs/SOFTWARE_DESIGN_DOCUMENT.md`
- **Phase 2 Implementation:** See `docs/PHASE2_IMPLEMENTATION.md`

---

## 🎓 PROJECT HIGHLIGHTS

✅ **Strict MVC Architecture** - Clean separation of concerns  
✅ **Machine Learning Integration** - KNN algorithm for predictions  
✅ **Role-Based Access Control** - Three distinct user roles  
✅ **Design Patterns** - Singleton, Factory, Strategy patterns  
✅ **Comprehensive Validation** - Client and server-side  
✅ **Dynamic Menu System** - Database-driven, hierarchical  
✅ **Automated Alerts** - Early warning system for at-risk students  
✅ **Responsive Design** - Works on all devices  
✅ **Unit Testing** - Test coverage for critical components  
✅ **Security Best Practices** - Password hashing, SQL injection prevention, XSS protection  

---

**Last Updated:** 2024  
**Version:** 1.0  
**License:** MIT

