<?php
/**
 * KNN Status Check Utility
 * Checks if KNN is ready to work and provides setup instructions
 */

// Load configuration
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

echo "<!DOCTYPE html>
<html>
<head>
    <title>KNN Status Check - EduPredict</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c5aa0; }
        h2 { color: #1e3a8a; margin-top: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #bee5eb; }
        .btn { display: inline-block; padding: 10px 20px; background: #2c5aa0; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px 10px 0; }
        .btn:hover { background: #1e3a8a; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .status-item { padding: 10px; margin: 10px 0; border-left: 4px solid #ddd; background: #f8f9fa; }
        .status-item.ok { border-left-color: #28a745; }
        .status-item.warning { border-left-color: #ffc107; }
        .status-item.error { border-left-color: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .step { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #2c5aa0; }
        .step-number { display: inline-block; width: 30px; height: 30px; background: #2c5aa0; color: white; border-radius: 50%; text-align: center; line-height: 30px; font-weight: bold; margin-right: 10px; }
    </style>
</head>
<body>
    <div class='container'>";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "<h1>🤖 KNN Machine Learning Status Check</h1>";
    
    $allOk = true;
    $warnings = [];
    $errors = [];
    
    // Check 1: Training Data (Kaggle Dataset)
    echo "<h2>1. Training Data Check</h2>";
    $trainingSql = "SELECT COUNT(*) as count FROM students WHERE student_id LIKE 'TRAIN_%' AND gpa IS NOT NULL AND attendance_rate IS NOT NULL";
    $trainingStmt = $pdo->query($trainingSql);
    $trainingCount = $trainingStmt->fetch()['count'];
    
    if ($trainingCount > 0) {
        echo "<div class='status-item ok'>
                ✅ <strong>Kaggle Training Data Found:</strong> {$trainingCount} records
              </div>";
    } else {
        echo "<div class='status-item warning'>
                ⚠️ <strong>No Kaggle Training Data:</strong> Using actual student data as fallback
              </div>";
        $warnings[] = "No Kaggle dataset imported. Consider importing one for better accuracy.";
    }
    
    // Check 2: Students with Data
    echo "<h2>2. Student Data Check</h2>";
    $studentsSql = "SELECT COUNT(*) as count FROM students WHERE student_id NOT LIKE 'TRAIN_%'";
    $studentsStmt = $pdo->query($studentsSql);
    $studentsCount = $studentsStmt->fetch()['count'];
    
    if ($studentsCount > 0) {
        echo "<div class='status-item ok'>
                ✅ <strong>Students Found:</strong> {$studentsCount} students
              </div>";
    } else {
        echo "<div class='status-item error'>
                ❌ <strong>No Students:</strong> Add students first
              </div>";
        $errors[] = "No students in database. Add students to make predictions.";
        $allOk = false;
    }
    
    // Check 3: Students with GPA and Attendance
    $studentsWithDataSql = "SELECT COUNT(*) as count FROM students s 
                            WHERE s.student_id NOT LIKE 'TRAIN_%'
                            AND s.gpa IS NOT NULL 
                            AND s.attendance_rate IS NOT NULL";
    $studentsWithDataStmt = $pdo->query($studentsWithDataSql);
    $studentsWithDataCount = $studentsWithDataStmt->fetch()['count'];
    
    if ($studentsWithDataCount > 0) {
        echo "<div class='status-item ok'>
                ✅ <strong>Students with Complete Data:</strong> {$studentsWithDataCount} students have GPA and attendance
              </div>";
    } else {
        echo "<div class='status-item error'>
                ❌ <strong>No Students with Complete Data:</strong> Students need GPA and attendance rates
              </div>";
        $errors[] = "Students need GPA and attendance_rate values for predictions.";
        $allOk = false;
    }
    
    // Check 4: Grades Data
    echo "<h2>3. Grades Data Check</h2>";
    $gradesSql = "SELECT COUNT(*) as count FROM grades";
    $gradesStmt = $pdo->query($gradesSql);
    $gradesCount = $gradesStmt->fetch()['count'];
    
    if ($gradesCount > 0) {
        echo "<div class='status-item ok'>
                ✅ <strong>Grades Found:</strong> {$gradesCount} grade records
              </div>";
        
        // Check students with grades
        $studentsWithGradesSql = "SELECT COUNT(DISTINCT student_id) as count FROM grades";
        $studentsWithGradesStmt = $pdo->query($studentsWithGradesSql);
        $studentsWithGradesCount = $studentsWithGradesStmt->fetch()['count'];
        
        echo "<div class='status-item ok'>
                ✅ <strong>Students with Grades:</strong> {$studentsWithGradesCount} students have grades
              </div>";
    } else {
        echo "<div class='status-item warning'>
                ⚠️ <strong>No Grades Found:</strong> Add grades for better predictions
              </div>";
        $warnings[] = "No grades in database. Add grades to improve prediction accuracy.";
    }
    
    // Check 5: Courses
    echo "<h2>4. Courses Check</h2>";
    $coursesSql = "SELECT COUNT(*) as count FROM courses";
    $coursesStmt = $pdo->query($coursesSql);
    $coursesCount = $coursesStmt->fetch()['count'];
    
    if ($coursesCount > 0) {
        echo "<div class='status-item ok'>
                ✅ <strong>Courses Found:</strong> {$coursesCount} courses
              </div>";
    } else {
        echo "<div class='status-item warning'>
                ⚠️ <strong>No Courses:</strong> Create courses for course-specific predictions
              </div>";
    }
    
    // Check 6: Test KNN Prediction
    echo "<h2>5. KNN Algorithm Test</h2>";
    
    if ($studentsWithDataCount > 0 && $gradesCount > 0) {
        try {
            // Try to run a test prediction
            require_once __DIR__ . '/../app/services/PredictionService.php';
            $predictionService = new \App\Services\PredictionService();
            
            // Get first student with data
            $testStudentSql = "SELECT s.id FROM students s 
                              WHERE s.student_id NOT LIKE 'TRAIN_%'
                              AND s.gpa IS NOT NULL 
                              AND s.attendance_rate IS NOT NULL
                              LIMIT 1";
            $testStudentStmt = $pdo->query($testStudentSql);
            $testStudent = $testStudentStmt->fetch();
            
            if ($testStudent) {
                $prediction = $predictionService->predictPerformance($testStudent['id']);
                
                echo "<div class='status-item ok'>
                        ✅ <strong>KNN Test Successful!</strong>
                        <br>Test prediction for Student ID {$testStudent['id']}:
                        <ul style='margin: 10px 0 0 20px;'>
                            <li>Predicted Grade: " . number_format($prediction['predicted_grade'], 2) . "</li>
                            <li>Risk Level: " . ucfirst($prediction['risk_level']) . "</li>
                            <li>Confidence: " . number_format($prediction['confidence'] * 100, 1) . "%</li>
                        </ul>
                      </div>";
            }
        } catch (Exception $e) {
            echo "<div class='status-item error'>
                    ❌ <strong>KNN Test Failed:</strong> " . htmlspecialchars($e->getMessage()) . "
                  </div>";
            $errors[] = "KNN prediction test failed: " . $e->getMessage();
            $allOk = false;
        }
    } else {
        echo "<div class='status-item warning'>
                ⚠️ <strong>Cannot Test KNN:</strong> Need students with data and grades
              </div>";
    }
    
    // Summary
    echo "<h2>📊 Summary</h2>";
    
    if ($allOk && empty($warnings)) {
        echo "<div class='success'>
                <h3>✅ KNN is Ready to Work!</h3>
                <p>All checks passed. You can now use KNN predictions.</p>
                <a href='/projecty/public/index.php?controller=prediction&action=index' class='btn btn-success'>Go to Predictions Page</a>
              </div>";
    } elseif ($allOk && !empty($warnings)) {
        echo "<div class='warning'>
                <h3>⚠️ KNN Works but Could Be Better</h3>
                <p>KNN can work, but consider:</p>
                <ul>";
        foreach ($warnings as $warning) {
            echo "<li>" . htmlspecialchars($warning) . "</li>";
        }
        echo "</ul>
              </div>";
    } else {
        echo "<div class='error'>
                <h3>❌ KNN Needs Setup</h3>
                <p>Please fix these issues:</p>
                <ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>
              </div>";
    }
    
    // Setup Instructions
    echo "<h2>🚀 How to Make KNN Work</h2>";
    
    echo "<div class='step'>
            <span class='step-number'>1</span>
            <strong>Add Students with Data</strong>
            <p>Students need GPA and attendance_rate values.</p>
            <a href='/projecty/public/index.php?controller=crud&action=index&entity=user' class='btn'>Manage Users</a>
          </div>";
    
    echo "<div class='step'>
            <span class='step-number'>2</span>
            <strong>Add Grades to Students</strong>
            <p>Add grades for students to improve prediction accuracy.</p>
            <a href='/projecty/utilities/add-grades-to-students.php' class='btn'>Add Grades</a>
          </div>";
    
    echo "<div class='step'>
            <span class='step-number'>3</span>
            <strong>Import Training Data (Optional but Recommended)</strong>
            <p>Import a Kaggle dataset for better KNN accuracy.</p>
            <a href='/projecty/utilities/download-kaggle-dataset.php' class='btn'>Download Dataset</a>
            <a href='/projecty/utilities/import-kaggle-dataset.php' class='btn'>Import Dataset</a>
          </div>";
    
    echo "<div class='step'>
            <span class='step-number'>4</span>
            <strong>Run Predictions</strong>
            <p>Go to the predictions page and click 'Run Predictions'.</p>
            <a href='/projecty/public/index.php?controller=prediction&action=index' class='btn btn-success'>Run Predictions</a>
          </div>";
    
    // Show current students
    if ($studentsCount > 0) {
        echo "<h2>📋 Current Students</h2>";
        $studentsListSql = "SELECT s.id, s.student_id, s.gpa, s.attendance_rate, u.name, 
                           COUNT(g.id) as grade_count
                           FROM students s 
                           LEFT JOIN users u ON s.user_id = u.id
                           LEFT JOIN grades g ON s.id = g.student_id
                           WHERE s.student_id NOT LIKE 'TRAIN_%'
                           GROUP BY s.id, s.student_id, s.gpa, s.attendance_rate, u.name
                           ORDER BY s.id
                           LIMIT 10";
        $studentsListStmt = $pdo->query($studentsListSql);
        $studentsList = $studentsListStmt->fetchAll();
        
        if (!empty($studentsList)) {
            echo "<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>GPA</th>
                            <th>Attendance</th>
                            <th>Grades</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>";
            
            foreach ($studentsList as $student) {
                $hasData = ($student['gpa'] !== null && $student['attendance_rate'] !== null);
                $hasGrades = ($student['grade_count'] > 0);
                $status = $hasData && $hasGrades ? '✅ Ready' : ($hasData ? '⚠️ Needs Grades' : '❌ Needs Data');
                
                echo "<tr>
                        <td>{$student['id']}</td>
                        <td>" . htmlspecialchars($student['student_id']) . "</td>
                        <td>" . htmlspecialchars($student['name'] ?? 'N/A') . "</td>
                        <td>" . ($student['gpa'] !== null ? number_format($student['gpa'], 2) : 'N/A') . "</td>
                        <td>" . ($student['attendance_rate'] !== null ? number_format($student['attendance_rate'], 1) . '%' : 'N/A') . "</td>
                        <td>{$student['grade_count']}</td>
                        <td>{$status}</td>
                      </tr>";
            }
            
            echo "</tbody></table>";
        }
    }
    
    echo "<div style='margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;'>
            <a href='/projecty/public/index.php?controller=dashboard&action=index' class='btn'>Go to Dashboard</a>
            <a href='/projecty/public/index.php?controller=prediction&action=index' class='btn btn-success'>Test Predictions</a>
          </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
            <h3>❌ Error</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
}

echo "</div></body></html>";
?>







