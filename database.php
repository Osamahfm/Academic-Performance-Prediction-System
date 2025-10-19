<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'edupredict');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        // More user-friendly error message
        if (strpos($e->getMessage(), '2002') !== false) {
            die("
                <div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
                    <h3>❌ MySQL Connection Failed</h3>
                    <p><strong>Error:</strong> Cannot connect to MySQL server</p>
                    <h4>To fix this issue:</h4>
                    <ol>
                        <li>Open XAMPP Control Panel</li>
                        <li>Click 'Start' next to MySQL</li>
                        <li>Wait for MySQL to show 'Running' status</li>
                        <li>Refresh this page</li>
                    </ol>
                    <p><a href='start-xampp.php'>Check XAMPP Status</a> | <a href='test-connection.php'>Test Connection</a></p>
                </div>
            ");
        } else {
            die("Database connection error: " . $e->getMessage());
        }
    }
}

// Create database if it doesn't exist
function createDatabase() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
        $pdo->exec($sql);
        
        return true;
    } catch(PDOException $e) {
        die("Database creation failed: " . $e->getMessage());
    }
}

// Initialize database tables
function initializeDatabase() {
    $pdo = getDBConnection();
    
    // Users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'instructor', 'student') NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Students table
    $sql = "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        student_id VARCHAR(20) UNIQUE NOT NULL,
        gpa DECIMAL(3,2) DEFAULT 0.00,
        attendance_rate DECIMAL(5,2) DEFAULT 0.00,
        risk_level ENUM('low', 'medium', 'high') DEFAULT 'low',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Courses table
    $sql = "CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_code VARCHAR(20) NOT NULL,
        course_name VARCHAR(100) NOT NULL,
        instructor_id INT,
        credits INT DEFAULT 3,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    
    // Enrollments table
    $sql = "CREATE TABLE IF NOT EXISTS enrollments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        course_id INT,
        enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('active', 'completed', 'dropped') DEFAULT 'active',
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Grades table
    $sql = "CREATE TABLE IF NOT EXISTS grades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        course_id INT,
        assignment_type ENUM('quiz', 'exam', 'assignment', 'project') NOT NULL,
        grade DECIMAL(5,2) NOT NULL,
        max_grade DECIMAL(5,2) DEFAULT 100.00,
        date_recorded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Predictions table
    $sql = "CREATE TABLE IF NOT EXISTS predictions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        course_id INT,
        predicted_grade DECIMAL(5,2),
        confidence_score DECIMAL(5,2),
        risk_factors TEXT,
        prediction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Alerts table
    $sql = "CREATE TABLE IF NOT EXISTS alerts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        alert_type ENUM('at_risk', 'low_performance', 'attendance', 'grade_drop') NOT NULL,
        message TEXT NOT NULL,
        severity ENUM('low', 'medium', 'high') DEFAULT 'medium',
        status ENUM('active', 'resolved', 'dismissed') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Feedback table
    $sql = "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        feedback_type ENUM('prediction_accuracy', 'system_usability', 'feature_request', 'bug_report') NOT NULL,
        subject VARCHAR(200),
        message TEXT NOT NULL,
        rating INT CHECK (rating >= 1 AND rating <= 5),
        status ENUM('new', 'reviewed', 'resolved') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);
    
    return true;
}

// Insert default data
function insertDefaultData() {
    $pdo = getDBConnection();
    
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@edupredict.edu']);
    
    if ($stmt->fetchColumn() == 0) {
        // Insert default users
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@edupredict.edu',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ],
            [
                'name' => 'Dr. Sarah Smith',
                'email' => 'instructor@edupredict.edu',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'role' => 'instructor'
            ],
            [
                'name' => 'John Doe',
                'email' => 'student@edupredict.edu',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'student'
            ]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        
        foreach ($users as $user) {
            $stmt->execute([$user['name'], $user['email'], $user['password'], $user['role']]);
        }
        
        // Insert sample courses
        $courses = [
            ['CS101', 'Introduction to Computer Science', 2],
            ['MATH201', 'Calculus I', 2],
            ['ENG101', 'English Composition', 2],
            ['PHYS101', 'Physics I', 2]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_name, instructor_id) VALUES (?, ?, ?)");
        
        foreach ($courses as $course) {
            $stmt->execute($course);
        }
        
        // Insert sample student data
        $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'student'");
        $stmt->execute();
        $student_user_id = $stmt->fetchColumn();
        
        if ($student_user_id) {
            $stmt = $pdo->prepare("INSERT INTO students (user_id, student_id, gpa, attendance_rate, risk_level) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$student_user_id, 'STU001', 3.45, 92.5, 'low']);
        }
    }
    
    return true;
}

// Initialize everything
function setupDatabase() {
    createDatabase();
    initializeDatabase();
    insertDefaultData();
    return true;
}
?>
