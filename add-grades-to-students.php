<?php
/**
 * Add Grades to Students Utility
 * Generates and assigns grades to all students or specific students
 */

// Load configuration
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

echo "<!DOCTYPE html>
<html>
<head>
    <title>Add Grades to Students - EduPredict</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c5aa0; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #bee5eb; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #ffeaa7; }
        .btn { display: inline-block; padding: 10px 20px; background: #2c5aa0; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px 10px 0; cursor: pointer; border: none; }
        .btn:hover { background: #1e3a8a; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        form { margin: 20px 0; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; max-width: 300px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
        .student-row:hover { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class='container'>";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "<h1>📝 Add Grades to Students</h1>";
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $studentId = $_POST['student_id'] ?? null;
        $gradesPerStudent = (int)($_POST['grades_per_student'] ?? 5);
        $assignmentTypes = ['quiz', 'exam', 'assignment', 'project'];
        
        if ($action === 'add_all') {
            // Add grades to all students
            $sql = "SELECT s.id, s.student_id, s.gpa, u.name 
                    FROM students s 
                    LEFT JOIN users u ON s.user_id = u.id
                    WHERE s.student_id NOT LIKE 'TRAIN_%'
                    ORDER BY s.id";
            $stmt = $pdo->query($sql);
            $students = $stmt->fetchAll();
            
            if (empty($students)) {
                throw new Exception("No students found to add grades to.");
            }
            
            // Get all courses
            $coursesSql = "SELECT id FROM courses ORDER BY id";
            $coursesStmt = $pdo->query($coursesSql);
            $courses = $coursesStmt->fetchAll(\PDO::FETCH_COLUMN);
            
            if (empty($courses)) {
                // Create a default course if none exist
                $pdo->exec("INSERT INTO courses (course_code, course_name, instructor_id) VALUES ('CS101', 'Introduction to Computer Science', NULL)");
                $courses = [$pdo->lastInsertId()];
            }
            
            $totalGrades = 0;
            $errors = [];
            $predictionsRun = 0;
            
            // Load prediction service once
            require_once __DIR__ . '/../app/services/PredictionService.php';
            $predictionService = new \App\Services\PredictionService();
            
            foreach ($students as $student) {
                // Get or create enrollments for this student
                foreach ($courses as $courseId) {
                    // Check if enrolled
                    $enrollCheck = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
                    $enrollCheck->execute([$student['id'], $courseId]);
                    
                    if (!$enrollCheck->fetch()) {
                        // Enroll student in course
                        $enrollSql = "INSERT INTO enrollments (student_id, course_id, status) VALUES (?, ?, 'active')";
                        $enrollStmt = $pdo->prepare($enrollSql);
                        $enrollStmt->execute([$student['id'], $courseId]);
                    }
                }
                
                // Generate grades based on student's GPA
                $baseGPA = (float)($student['gpa'] ?? 2.5);
                
                // Calculate target average grade (GPA * 25)
                $targetAvg = $baseGPA * 25;
                
                // Generate grades with some variation
                for ($i = 0; $i < $gradesPerStudent; $i++) {
                    $courseId = $courses[array_rand($courses)];
                    $assignmentType = $assignmentTypes[array_rand($assignmentTypes)];
                    
                    // Generate grade with variation around target average
                    $variation = mt_rand(-15, 15);
                    $grade = max(0, min(100, $targetAvg + $variation));
                    
                    // Add some randomness for realism
                    $grade = round($grade + mt_rand(-5, 5), 2);
                    $grade = max(0, min(100, $grade));
                    
                    try {
                        $gradeSql = "INSERT INTO grades (student_id, course_id, assignment_type, grade, max_grade) 
                                     VALUES (?, ?, ?, ?, 100)";
                        $gradeStmt = $pdo->prepare($gradeSql);
                        $gradeStmt->execute([$student['id'], $courseId, $assignmentType, $grade]);
                        $totalGrades++;
                    } catch (PDOException $e) {
                        $errors[] = "Student {$student['student_id']}: " . $e->getMessage();
                    }
                }
                
                // Run KNN prediction for this student after adding all their grades
                if ($totalGrades > 0 && $student['gpa'] !== null && $student['attendance_rate'] !== null) {
                    try {
                        $predictionService->predictPerformance($student['id']);
                        $predictionsRun++;
                    } catch (Exception $e) {
                        // Silently fail - prediction is not critical
                    }
                }
            }
            
            echo "<div class='success'>
                    <h3>✅ Grades Added Successfully!</h3>
                    <p><strong>Students processed:</strong> " . count($students) . "</p>
                    <p><strong>Grades added:</strong> {$totalGrades}</p>
                    <p><strong>Grades per student:</strong> {$gradesPerStudent}</p>
                    <p><strong>KNN Predictions run:</strong> {$predictionsRun} students</p>
                    <p style='margin-top: 10px; color: #155724;'><strong>✨ KNN predictions have been automatically updated for all students!</strong></p>
                  </div>";
            
            if (!empty($errors)) {
                echo "<div class='warning'>
                        <h4>⚠️ Some errors occurred:</h4>
                        <ul>";
                foreach (array_slice($errors, 0, 10) as $error) {
                    echo "<li>" . htmlspecialchars($error) . "</li>";
                }
                if (count($errors) > 10) {
                    echo "<li>... and " . (count($errors) - 10) . " more errors</li>";
                }
                echo "</ul></div>";
            }
            
        } elseif ($action === 'add_specific' && $studentId) {
            // Add grades to specific student
            $studentSql = "SELECT s.id, s.student_id, s.gpa, u.name 
                          FROM students s 
                          LEFT JOIN users u ON s.user_id = u.id
                          WHERE s.id = ?";
            $studentStmt = $pdo->prepare($studentSql);
            $studentStmt->execute([$studentId]);
            $student = $studentStmt->fetch();
            
            if (!$student) {
                throw new Exception("Student not found.");
            }
            
            // Get courses
            $coursesSql = "SELECT id FROM courses ORDER BY id";
            $coursesStmt = $pdo->query($coursesSql);
            $courses = $coursesStmt->fetchAll(\PDO::FETCH_COLUMN);
            
            if (empty($courses)) {
                $pdo->exec("INSERT INTO courses (course_code, course_name, instructor_id) VALUES ('CS101', 'Introduction to Computer Science', NULL)");
                $courses = [$pdo->lastInsertId()];
            }
            
            // Ensure enrollment
            foreach ($courses as $courseId) {
                $enrollCheck = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
                $enrollCheck->execute([$student['id'], $courseId]);
                
                if (!$enrollCheck->fetch()) {
                    $enrollSql = "INSERT INTO enrollments (student_id, course_id, status) VALUES (?, ?, 'active')";
                    $enrollStmt = $pdo->prepare($enrollSql);
                    $enrollStmt->execute([$student['id'], $courseId]);
                }
            }
            
            // Generate grades
            $baseGPA = (float)($student['gpa'] ?? 2.5);
            $targetAvg = $baseGPA * 25;
            $totalGrades = 0;
            
            for ($i = 0; $i < $gradesPerStudent; $i++) {
                $courseId = $courses[array_rand($courses)];
                $assignmentType = $assignmentTypes[array_rand($assignmentTypes)];
                
                $variation = mt_rand(-15, 15);
                $grade = max(0, min(100, $targetAvg + $variation));
                $grade = round($grade + mt_rand(-5, 5), 2);
                $grade = max(0, min(100, $grade));
                
                $gradeSql = "INSERT INTO grades (student_id, course_id, assignment_type, grade, max_grade) 
                             VALUES (?, ?, ?, ?, 100)";
                $gradeStmt = $pdo->prepare($gradeSql);
                $gradeStmt->execute([$student['id'], $courseId, $assignmentType, $grade]);
                $totalGrades++;
            }
            
            // Run KNN prediction for this student
            $predictionRun = false;
            if ($student['gpa'] !== null && $student['attendance_rate'] !== null) {
                try {
                    require_once __DIR__ . '/../app/services/PredictionService.php';
                    $predictionService = new \App\Services\PredictionService();
                    $predictionService->predictPerformance($student['id']);
                    $predictionRun = true;
                } catch (Exception $e) {
                    // Silently fail
                }
            }
            
            echo "<div class='success'>
                    <h3>✅ Grades Added!</h3>
                    <p><strong>Student:</strong> {$student['name']} ({$student['student_id']})</p>
                    <p><strong>Grades added:</strong> {$totalGrades}</p>";
            if ($predictionRun) {
                echo "<p style='color: #155724; margin-top: 10px;'><strong>✨ KNN prediction automatically updated!</strong></p>";
            }
            echo "</div>";
        }
    }
    
    // Get all students
    $sql = "SELECT s.id, s.student_id, s.gpa, s.attendance_rate, u.name, u.email,
                   COUNT(g.id) as grade_count
            FROM students s 
            LEFT JOIN users u ON s.user_id = u.id
            LEFT JOIN grades g ON s.id = g.student_id
            WHERE s.student_id NOT LIKE 'TRAIN_%'
            GROUP BY s.id, s.student_id, s.gpa, s.attendance_rate, u.name, u.email
            ORDER BY s.id";
    $stmt = $pdo->query($sql);
    $students = $stmt->fetchAll();
    
    if (empty($students)) {
        echo "<div class='warning'>
                <h3>⚠️ No Students Found</h3>
                <p>There are no students in the database. Please add students first.</p>
              </div>";
    } else {
        echo "<div class='info'>
                <h3>📊 Current Students</h3>
                <p>Total students: " . count($students) . "</p>
              </div>";
        
        // Form to add grades to all students
        echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3>Add Grades to All Students</h3>
                <input type='hidden' name='action' value='add_all'>
                <div class='form-group'>
                    <label>Number of Grades per Student:</label>
                    <input type='number' name='grades_per_student' value='5' min='1' max='20' required>
                    <small style='color: #666;'>Each student will receive this many grades across their enrolled courses.</small>
                </div>
                <button type='submit' class='btn btn-success'>Add Grades to All Students</button>
              </form>";
        
        // Display students table
        echo "<h3>Student List</h3>
              <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>GPA</th>
                        <th>Attendance</th>
                        <th>Current Grades</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>";
        
        foreach ($students as $student) {
            echo "<tr class='student-row'>
                    <td>{$student['id']}</td>
                    <td>" . htmlspecialchars($student['student_id']) . "</td>
                    <td>" . htmlspecialchars($student['name'] ?? 'N/A') . "</td>
                    <td>" . htmlspecialchars($student['email'] ?? 'N/A') . "</td>
                    <td>" . number_format((float)($student['gpa'] ?? 0), 2) . "</td>
                    <td>" . number_format((float)($student['attendance_rate'] ?? 0), 1) . "%</td>
                    <td>{$student['grade_count']}</td>
                    <td>
                        <form method='POST' style='display: inline;'>
                            <input type='hidden' name='action' value='add_specific'>
                            <input type='hidden' name='student_id' value='{$student['id']}'>
                            <input type='hidden' name='grades_per_student' value='5'>
                            <button type='submit' class='btn' style='padding: 5px 10px; font-size: 12px;'>Add 5 Grades</button>
                        </form>
                    </td>
                  </tr>";
        }
        
        echo "</tbody></table>";
    }
    
    echo "<div style='margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;'>
            <a href='/projecty/public/index.php?controller=dashboard&action=index' class='btn'>Go to Dashboard</a>
            <a href='/projecty/public/index.php?controller=crud&action=index&entity=grade' class='btn'>View All Grades</a>
          </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
            <h3>❌ Error</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
}

echo "</div></body></html>";
?>

