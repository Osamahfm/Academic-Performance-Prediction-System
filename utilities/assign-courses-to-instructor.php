<?php
/**
 * Utility to assign courses to instructors
 * This helps fix the issue where courses exist but aren't assigned to instructors
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Courses to Instructor - EduPredict</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c5aa0;
            margin-bottom: 30px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-primary {
            background: #2c5aa0;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Assign Courses to Instructors</h1>

<?php
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
        $courseId = $_POST['course_id'] ?? null;
        $instructorId = $_POST['instructor_id'] ?? null;
        
        if ($courseId && $instructorId) {
            $sql = "UPDATE courses SET instructor_id = :instructor_id WHERE id = :course_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':instructor_id' => $instructorId,
                ':course_id' => $courseId
            ]);
            
            echo "<div class='success'>✅ Course assigned successfully!</div>";
        }
    }
    
    // Handle bulk assignment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_assign'])) {
        $instructorId = $_POST['bulk_instructor_id'] ?? null;
        
        if ($instructorId) {
            // Get all courses without instructor
            $sql = "UPDATE courses SET instructor_id = :instructor_id WHERE instructor_id IS NULL";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':instructor_id' => $instructorId]);
            $count = $stmt->rowCount();
            
            echo "<div class='success'>✅ Assigned {$count} unassigned courses to instructor!</div>";
        }
    }
    
    // Get all instructors
    $instructorsSql = "SELECT id, name, email FROM users WHERE role = 'instructor' ORDER BY name";
    $instructors = $pdo->query($instructorsSql)->fetchAll();
    
    // Get all courses
    $coursesSql = "SELECT c.id, c.course_code, c.course_name, c.instructor_id, u.name as instructor_name 
                   FROM courses c 
                   LEFT JOIN users u ON c.instructor_id = u.id 
                   ORDER BY c.course_code";
    $courses = $pdo->query($coursesSql)->fetchAll();
    
    // Get instructor for current session (if logged in)
    $currentInstructorId = null;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'instructor') {
        $currentInstructorId = $_SESSION['user_id'];
    }
    
    echo "<div class='info'>";
    echo "<h3>Quick Actions</h3>";
    echo "<p><strong>Current Instructor:</strong> " . ($currentInstructorId ? "ID {$currentInstructorId}" : "Not logged in as instructor") . "</p>";
    
    if ($currentInstructorId) {
        echo "<form method='POST' style='margin-top: 15px;'>";
        echo "<input type='hidden' name='bulk_instructor_id' value='{$currentInstructorId}'>";
        echo "<button type='submit' name='bulk_assign' class='btn btn-success'>Assign All Unassigned Courses to Me</button>";
        echo "</form>";
    }
    echo "</div>";
    
    // Show courses table
    echo "<h2>All Courses</h2>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Course Code</th>";
    echo "<th>Course Name</th>";
    echo "<th>Current Instructor</th>";
    echo "<th>Actions</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($courses as $course) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($course['course_code']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($course['course_name']) . "</td>";
        echo "<td>" . ($course['instructor_name'] ? htmlspecialchars($course['instructor_name']) : '<span style="color: #dc3545;">Unassigned</span>') . "</td>";
        echo "<td>";
        echo "<form method='POST' style='display: inline;'>";
        echo "<input type='hidden' name='course_id' value='{$course['id']}'>";
        echo "<select name='instructor_id' style='width: 200px; padding: 5px; margin-right: 5px;'>";
        echo "<option value=''>-- Select Instructor --</option>";
        foreach ($instructors as $instructor) {
            $selected = ($course['instructor_id'] == $instructor['id']) ? 'selected' : '';
            echo "<option value='{$instructor['id']}' {$selected}>" . htmlspecialchars($instructor['name']) . "</option>";
        }
        echo "</select>";
        echo "<button type='submit' name='assign' class='btn btn-primary'>Assign</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    
    // Show instructors and their courses
    echo "<h2>Instructors and Their Courses</h2>";
    foreach ($instructors as $instructor) {
        $instructorCoursesSql = "SELECT id, course_code, course_name FROM courses WHERE instructor_id = :instructor_id";
        $instructorCoursesStmt = $pdo->prepare($instructorCoursesSql);
        $instructorCoursesStmt->execute([':instructor_id' => $instructor['id']]);
        $instructorCourses = $instructorCoursesStmt->fetchAll();
        
        echo "<div style='margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;'>";
        echo "<h3>" . htmlspecialchars($instructor['name']) . " (ID: {$instructor['id']})</h3>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($instructor['email']) . "</p>";
        
        if (empty($instructorCourses)) {
            echo "<p style='color: #dc3545;'><strong>No courses assigned</strong></p>";
        } else {
            echo "<p><strong>Courses ({count($instructorCourses)}):</strong></p>";
            echo "<ul>";
            foreach ($instructorCourses as $course) {
                echo "<li>" . htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']) . "</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
            <a href="/projecty/public/grade/manage" class="btn btn-primary">← Back to Grade Management</a>
            <a href="/projecty/public/dashboard/instructor" class="btn btn-primary">← Back to Instructor Dashboard</a>
        </div>
    </div>
</body>
</html>

