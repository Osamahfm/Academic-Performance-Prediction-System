<?php
/**
 * Test Predictions Utility
 * Tests that predictions are different for different students
 */

// Register autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/ML/KNNPredictor.php';
require_once __DIR__ . '/../app/services/PredictionService.php';

use App\Services\PredictionService;
use App\Core\Database;

// Initialize database
Database::getInstance();

echo "=== Testing KNN Predictions ===\n\n";

try {
    $predictionService = new PredictionService();
    $db = Database::getInstance()->getConnection();
    
    // Get a few students with different profiles
    $sql = "SELECT s.id, s.student_id, s.gpa, s.attendance_rate, s.risk_level,
                   AVG(g.grade) as avg_grade,
                   COUNT(g.id) as assignments_completed
            FROM students s
            LEFT JOIN grades g ON s.id = g.student_id
            WHERE s.gpa IS NOT NULL 
            AND s.attendance_rate IS NOT NULL
            GROUP BY s.id, s.gpa, s.attendance_rate, s.risk_level
            HAVING COUNT(g.id) > 0
            LIMIT 5";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $students = $stmt->fetchAll();
    
    if (empty($students)) {
        echo "❌ No students found with complete data.\n";
        echo "Please add GPA, attendance, and grades to students first.\n";
        exit(1);
    }
    
    echo "Found " . count($students) . " students with complete data.\n\n";
    
    $predictions = [];
    foreach ($students as $student) {
        echo "Student: {$student['student_id']}\n";
        echo "  GPA: {$student['gpa']}, Attendance: {$student['attendance_rate']}%, Risk: {$student['risk_level']}\n";
        echo "  Avg Grade: " . number_format($student['avg_grade'] ?? 0, 2) . ", Assignments: {$student['assignments_completed']}\n";
        
        // Generate prediction
        $result = $predictionService->predictPerformance($student['id']);
        
        $predictedGrade = $result['predicted_grade'];
        $confidence = $result['confidence'];
        $riskLevel = $result['risk_level'];
        
        echo "  → Predicted Grade: " . number_format($predictedGrade, 2) . "%\n";
        echo "  → Confidence: " . number_format($confidence * 100, 1) . "%\n";
        echo "  → Risk Level: {$riskLevel}\n";
        echo "\n";
        
        $predictions[] = [
            'student_id' => $student['student_id'],
            'gpa' => $student['gpa'],
            'attendance' => $student['attendance_rate'],
            'avg_grade' => $student['avg_grade'] ?? 0,
            'predicted_grade' => $predictedGrade
        ];
    }
    
    // Check if predictions are different
    $grades = array_column($predictions, 'predicted_grade');
    $uniqueGrades = array_unique($grades);
    
    echo "=== Summary ===\n";
    echo "Total students tested: " . count($predictions) . "\n";
    echo "Unique predicted grades: " . count($uniqueGrades) . "\n";
    
    if (count($uniqueGrades) == 1) {
        echo "\n⚠️  WARNING: All predictions are the same!\n";
        echo "This might indicate:\n";
        echo "  1. All students have very similar features (GPA, attendance, grades)\n";
        echo "  2. Training data is not diverse enough\n";
        echo "  3. Need more training data\n\n";
        
        // Show the differences
        echo "Student differences:\n";
        foreach ($predictions as $p) {
            echo "  {$p['student_id']}: GPA={$p['gpa']}, Att={$p['attendance']}%, Avg={$p['avg_grade']}, Predicted={$p['predicted_grade']}\n";
        }
    } else {
        echo "\n✅ Good! Predictions are different for different students.\n";
        echo "\nPredicted grades range: " . number_format(min($grades), 2) . "% - " . number_format(max($grades), 2) . "%\n";
    }
    
    // Test course-specific predictions
    echo "\n=== Testing Course-Specific Predictions ===\n";
    
    $courseSql = "SELECT DISTINCT course_id FROM enrollments WHERE student_id IN (" . 
                 implode(',', array_column($students, 'id')) . ") LIMIT 1";
    $courseStmt = $db->query($courseSql);
    $course = $courseStmt->fetch();
    
    if ($course) {
        $courseId = $course['course_id'];
        $courseInfo = $db->prepare("SELECT course_name, course_code FROM courses WHERE id = ?");
        $courseInfo->execute([$courseId]);
        $courseData = $courseInfo->fetch();
        
        echo "Testing predictions for course: {$courseData['course_name']} ({$courseData['course_code']})\n\n";
        
        $coursePredictions = [];
        foreach ($students as $student) {
            // Check if student is enrolled
            $enrollCheck = $db->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND course_id = ?");
            $enrollCheck->execute([$student['id'], $courseId]);
            if (!$enrollCheck->fetch()) {
                continue;
            }
            
            $result = $predictionService->predictPerformance($student['id'], $courseId);
            $predictedGrade = $result['predicted_grade'];
            
            echo "  {$student['student_id']}: " . number_format($predictedGrade, 2) . "%\n";
            $coursePredictions[] = $predictedGrade;
        }
        
        if (!empty($coursePredictions)) {
            $uniqueCourseGrades = array_unique($coursePredictions);
            echo "\nUnique course predictions: " . count($uniqueCourseGrades) . " out of " . count($coursePredictions) . "\n";
            
            if (count($uniqueCourseGrades) == 1) {
                echo "⚠️  All course predictions are the same.\n";
            } else {
                echo "✅ Course predictions are different.\n";
            }
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== Test Complete ===\n";





