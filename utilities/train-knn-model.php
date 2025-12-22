<?php
/**
 * Utility to train KNN model and check training data
 * This helps ensure the model has enough data to make accurate predictions
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

// Ensure autoloader is registered
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Database;
use App\Services\PredictionService;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Train KNN Model - EduPredict</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
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
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #2c5aa0;
        }
        .stat-label {
            color: #666;
            margin-top: 5px;
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
            padding: 10px 20px;
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
        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-good { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Train KNN Model</h1>

<?php
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Handle training request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['train_model'])) {
        try {
            $predictionService = new PredictionService();
            
            // Get all students
            $students = $pdo->query("SELECT id FROM students WHERE gpa IS NOT NULL AND attendance_rate IS NOT NULL")->fetchAll();
            
            $trained = 0;
            $errors = 0;
            
            foreach ($students as $student) {
                try {
                    $predictionService->predictPerformance($student['id']);
                    $trained++;
                } catch (Exception $e) {
                    $errors++;
                }
            }
            
            echo "<div class='success'>";
            echo "<h3>✅ Training Complete!</h3>";
            echo "<p><strong>Students Trained:</strong> {$trained}</p>";
            if ($errors > 0) {
                echo "<p><strong>Errors:</strong> {$errors}</p>";
            }
            echo "</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Training Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    // Analyze training data
    $sql = "SELECT 
                COUNT(DISTINCT s.id) as total_students,
                COUNT(DISTINCT CASE WHEN s.gpa IS NOT NULL AND s.attendance_rate IS NOT NULL THEN s.id END) as students_with_data,
                COUNT(DISTINCT CASE WHEN EXISTS(SELECT 1 FROM grades g WHERE g.student_id = s.id) THEN s.id END) as students_with_grades,
                COUNT(DISTINCT CASE WHEN s.gpa IS NOT NULL AND s.attendance_rate IS NOT NULL 
                                    AND EXISTS(SELECT 1 FROM grades g WHERE g.student_id = s.id) THEN s.id END) as training_ready,
                AVG(s.gpa) as avg_gpa,
                AVG(s.attendance_rate) as avg_attendance,
                COUNT(DISTINCT g.id) as total_grades
            FROM students s
            LEFT JOIN grades g ON s.id = g.student_id";
    
    $stats = $pdo->query($sql)->fetch();
    
    // Get training data sample
    $trainingSql = "SELECT s.id, s.student_id, s.gpa, s.attendance_rate, s.risk_level,
                       AVG(g.grade) as avg_grade,
                       COUNT(g.id) as assignments_completed
                FROM students s
                LEFT JOIN grades g ON s.id = g.student_id
                WHERE s.gpa IS NOT NULL 
                AND s.attendance_rate IS NOT NULL
                GROUP BY s.id, s.gpa, s.attendance_rate, s.risk_level
                HAVING COUNT(g.id) > 0
                LIMIT 10";
    
    $trainingData = $pdo->query($trainingSql)->fetchAll();
    
    // Display statistics
    echo "<div class='stats-grid'>";
    echo "<div class='stat-card'>";
    echo "<div class='stat-value'>" . ($stats['total_students'] ?? 0) . "</div>";
    echo "<div class='stat-label'>Total Students</div>";
    echo "</div>";
    
    echo "<div class='stat-card'>";
    echo "<div class='stat-value'>" . ($stats['students_with_data'] ?? 0) . "</div>";
    echo "<div class='stat-label'>With GPA & Attendance</div>";
    echo "</div>";
    
    echo "<div class='stat-card'>";
    echo "<div class='stat-value'>" . ($stats['students_with_grades'] ?? 0) . "</div>";
    echo "<div class='stat-label'>With Grades</div>";
    echo "</div>";
    
    echo "<div class='stat-card'>";
    $ready = $stats['training_ready'] ?? 0;
    $badgeClass = $ready >= 10 ? 'badge-good' : ($ready >= 5 ? 'badge-warning' : 'badge-danger');
    echo "<div class='stat-value'><span class='badge {$badgeClass}'>{$ready}</span></div>";
    echo "<div class='stat-label'>Training Ready</div>";
    echo "</div>";
    echo "</div>";
    
    // Training status
    if ($ready < 5) {
        echo "<div class='error'>";
        echo "<h3>⚠️ Insufficient Training Data</h3>";
        echo "<p>You need at least 5 students with GPA, attendance, and grades for KNN to work properly.</p>";
        echo "<p><strong>Current:</strong> {$ready} students ready</p>";
        echo "<p><strong>Actions needed:</strong></p>";
        echo "<ul>";
        echo "<li><a href='/projecty/utilities/manage-student-gpa.php'>Add GPA & Attendance</a> to students</li>";
        echo "<li><a href='/projecty/public/enrollment'>Enroll students in courses</a></li>";
        echo "<li><a href='/projecty/public/grade/manage'>Add grades</a> to students</li>";
        echo "</ul>";
        echo "</div>";
    } elseif ($ready < 10) {
        echo "<div class='warning'>";
        echo "<h3>⚠️ Limited Training Data</h3>";
        echo "<p>You have {$ready} students ready for training. More data (10+) will improve prediction accuracy.</p>";
        echo "</div>";
    } else {
        echo "<div class='success'>";
        echo "<h3>✅ Sufficient Training Data</h3>";
        echo "<p>You have {$ready} students ready for training. The model should work well!</p>";
        echo "</div>";
    }
    
    // Training form
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>Train Model</h3>";
    echo "<p>Click the button below to run predictions for all students and update the model.</p>";
    echo "<form method='POST'>";
    echo "<button type='submit' name='train_model' class='btn btn-success'>🚀 Train KNN Model</button>";
    echo "</form>";
    echo "</div>";
    
    // Show sample training data
    if (!empty($trainingData)) {
        echo "<h2>Sample Training Data</h2>";
        echo "<p>These are examples of students that will be used for training:</p>";
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Student ID</th>";
        echo "<th>GPA</th>";
        echo "<th>Attendance</th>";
        echo "<th>Avg Grade</th>";
        echo "<th>Assignments</th>";
        echo "<th>Risk Level</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($trainingData as $data) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($data['student_id']) . "</td>";
            echo "<td>" . number_format($data['gpa'], 2) . "</td>";
            echo "<td>" . number_format($data['attendance_rate'], 1) . "%</td>";
            echo "<td>" . number_format($data['avg_grade'] ?? 0, 2) . "</td>";
            echo "<td>" . ($data['assignments_completed'] ?? 0) . "</td>";
            echo "<td><span class='badge badge-" . strtolower($data['risk_level'] ?? 'medium') . "'>" . ucfirst($data['risk_level'] ?? 'Medium') . "</span></td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
    }
    
    // How KNN works
    echo "<div class='info' style='margin-top: 30px;'>";
    echo "<h3>📚 How KNN Training Works</h3>";
    echo "<ol>";
    echo "<li><strong>Training Data:</strong> The model uses students with GPA, attendance, average grades, and assignment counts</li>";
    echo "<li><strong>Features:</strong> Each student is represented by [GPA, Attendance Rate, Average Grade, Assignments Completed]</li>";
    echo "<li><strong>Prediction:</strong> When predicting for a new student, KNN finds the 5 most similar students (nearest neighbors)</li>";
    echo "<li><strong>Result:</strong> The model predicts risk level and grade based on the majority vote of neighbors</li>";
    echo "</ol>";
    echo "<p><strong>Note:</strong> KNN doesn't require traditional 'training' - it uses all available data each time. Running 'Train Model' updates predictions for all students.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
            <a href="/projecty/public/dashboard/admin" class="btn btn-primary">← Back to Dashboard</a>
            <a href="/projecty/utilities/manage-student-gpa.php" class="btn btn-success">Manage GPA & Attendance →</a>
            <a href="/projecty/public/predictions" class="btn btn-success">View Predictions →</a>
        </div>
    </div>
</body>
</html>

