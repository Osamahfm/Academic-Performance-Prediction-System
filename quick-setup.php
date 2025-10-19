<?php
echo "<!DOCTYPE html>
<html>
<head>
    <title>EduPredict Quick Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .btn { background: #2c5aa0; color: white; padding: 12px 24px; border: none; border-radius: 5px; text-decoration: none; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #1e3a8a; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🎓 EduPredict Quick Setup</h1>";

// Test MySQL connection
echo "<h2>Step 1: Testing MySQL Connection</h2>";
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ MySQL connection successful!</div>";
} catch (PDOException $e) {
    echo "<div class='error'>❌ MySQL connection failed: " . $e->getMessage() . "</div>";
    echo "<p>Please start MySQL in XAMPP Control Panel first.</p>";
    exit;
}

// Create database
echo "<h2>Step 2: Creating Database</h2>";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS edupredict");
    echo "<div class='success'>✅ Database 'edupredict' created successfully!</div>";
} catch (PDOException $e) {
    echo "<div class='error'>❌ Database creation failed: " . $e->getMessage() . "</div>";
}

// Connect to the database
try {
    $pdo = new PDO("mysql:host=localhost;dbname=edupredict", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✅ Connected to edupredict database!</div>";
} catch (PDOException $e) {
    echo "<div class='error'>❌ Database connection failed: " . $e->getMessage() . "</div>";
    exit;
}

// Create users table
echo "<h2>Step 3: Creating Tables</h2>";
$tables = [
    "users" => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'instructor', 'student') NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "students" => "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        student_id VARCHAR(20) UNIQUE NOT NULL,
        gpa DECIMAL(3,2) DEFAULT 0.00,
        attendance_rate DECIMAL(5,2) DEFAULT 0.00,
        risk_level ENUM('low', 'medium', 'high') DEFAULT 'low',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    "courses" => "CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_code VARCHAR(20) NOT NULL,
        course_name VARCHAR(100) NOT NULL,
        instructor_id INT,
        credits INT DEFAULT 3,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL
    )",
    "enrollments" => "CREATE TABLE IF NOT EXISTS enrollments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        course_id INT,
        enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('active', 'completed', 'dropped') DEFAULT 'active',
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )",
    "grades" => "CREATE TABLE IF NOT EXISTS grades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        course_id INT,
        assignment_type ENUM('quiz', 'exam', 'assignment', 'project') NOT NULL,
        grade DECIMAL(5,2) NOT NULL,
        max_grade DECIMAL(5,2) DEFAULT 100.00,
        date_recorded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )",
    "predictions" => "CREATE TABLE IF NOT EXISTS predictions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        course_id INT,
        predicted_grade DECIMAL(5,2),
        confidence_score DECIMAL(5,2),
        risk_factors TEXT,
        prediction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )",
    "alerts" => "CREATE TABLE IF NOT EXISTS alerts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT,
        alert_type ENUM('at_risk', 'low_performance', 'attendance', 'grade_drop') NOT NULL,
        message TEXT NOT NULL,
        severity ENUM('low', 'medium', 'high') DEFAULT 'medium',
        status ENUM('active', 'resolved', 'dismissed') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )",
    "feedback" => "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        feedback_type ENUM('prediction_accuracy', 'system_usability', 'feature_request', 'bug_report') NOT NULL,
        subject VARCHAR(200),
        message TEXT NOT NULL,
        rating INT CHECK (rating >= 1 AND rating <= 5),
        status ENUM('new', 'reviewed', 'resolved') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )"
];

foreach ($tables as $tableName => $sql) {
    try {
        $pdo->exec($sql);
        echo "<div class='success'>✅ Table '$tableName' created successfully!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Table '$tableName' creation failed: " . $e->getMessage() . "</div>";
    }
}

// Insert default users
echo "<h2>Step 4: Inserting Default Users</h2>";
$users = [
    ['Admin User', 'admin@edupredict.edu', password_hash('admin123', PASSWORD_DEFAULT), 'admin'],
    ['Dr. Sarah Smith', 'instructor@edupredict.edu', password_hash('instructor123', PASSWORD_DEFAULT), 'instructor'],
    ['John Doe', 'student@edupredict.edu', password_hash('student123', PASSWORD_DEFAULT), 'student']
];

$stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
foreach ($users as $user) {
    try {
        $stmt->execute($user);
        echo "<div class='success'>✅ User '{$user[0]}' inserted successfully!</div>";
    } catch (PDOException $e) {
        echo "<div class='info'>ℹ️ User '{$user[0]}' already exists or error: " . $e->getMessage() . "</div>";
    }
}

// Insert sample courses
echo "<h2>Step 5: Inserting Sample Data</h2>";
$courses = [
    ['CS101', 'Introduction to Computer Science', 2],
    ['MATH201', 'Calculus I', 2],
    ['ENG101', 'English Composition', 2],
    ['PHYS101', 'Physics I', 2]
];

$stmt = $pdo->prepare("INSERT IGNORE INTO courses (course_code, course_name, instructor_id) VALUES (?, ?, ?)");
foreach ($courses as $course) {
    try {
        $stmt->execute($course);
        echo "<div class='success'>✅ Course '{$course[0]}' inserted successfully!</div>";
    } catch (PDOException $e) {
        echo "<div class='info'>ℹ️ Course '{$course[0]}' already exists or error: " . $e->getMessage() . "</div>";
    }
}

echo "<h2>🎉 Setup Complete!</h2>";
echo "<div class='success'>
    <h3>Your EduPredict system is ready!</h3>
    <p><strong>Demo Login Credentials:</strong></p>
    <ul>
        <li><strong>Admin:</strong> admin@edupredict.edu / admin123</li>
        <li><strong>Instructor:</strong> instructor@edupredict.edu / instructor123</li>
        <li><strong>Student:</strong> student@edupredict.edu / student123</li>
    </ul>
</div>";

echo "<div style='text-align: center; margin-top: 30px;'>
    <a href='login.php' class='btn'>🔐 Go to Login</a>
    <a href='index.php' class='btn'>🏠 Go to Homepage</a>
</div>";

echo "</div></body></html>";
?>

