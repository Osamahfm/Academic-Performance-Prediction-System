<?php
/**
 * Import Kaggle Academic Performance Dataset
 * This script imports training data from a CSV file to train the KNN model
 */

// Load configuration
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

use App\Core\Database;

echo "<!DOCTYPE html>
<html>
<head>
    <title>Import Kaggle Dataset - EduPredict</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c5aa0; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #bee5eb; }
        .btn { display: inline-block; padding: 10px 20px; background: #2c5aa0; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #1e3a8a; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class='container'>";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "<h1>📊 Import Kaggle Academic Performance Dataset</h1>";
    
    // Check if CSV file exists
    $csvFile = __DIR__ . '/dataset/student_performance.csv';
    $datasetDir = __DIR__ . '/dataset';
    
    if (!file_exists($csvFile)) {
        echo "<div class='info'>
                <h3>📥 Dataset Not Found</h3>
                <p><strong>File expected:</strong> {$csvFile}</p>
                <h4>How to get the dataset:</h4>
                <ol>
                    <li>Download a student performance dataset from Kaggle (e.g., 'Student Performance Dataset')</li>
                    <li>Save it as <code>student_performance.csv</code></li>
                    <li>Place it in: <code>projecty/utilities/dataset/</code> folder</li>
                    <li>Refresh this page</li>
                </ol>
                <h4>Recommended Kaggle Datasets:</h4>
                <ul>
                    <li><strong>Student Performance Dataset</strong> - Contains GPA, attendance, grades</li>
                    <li><strong>Students' Academic Performance Dataset</strong> - Academic records</li>
                    <li><strong>Student Grades Dataset</strong> - Grade data with performance metrics</li>
                </ul>
                <h4>Required CSV Columns:</h4>
                <ul>
                    <li><code>gpa</code> or <code>GPA</code> - Grade Point Average (0-4.0)</li>
                    <li><code>attendance</code> or <code>attendance_rate</code> - Attendance percentage (0-100)</li>
                    <li><code>avg_grade</code> or <code>average_grade</code> - Average grade (0-100)</li>
                    <li><code>assignments</code> or <code>assignments_completed</code> - Number of completed assignments</li>
                    <li><code>risk_level</code> or <code>performance</code> - Risk level (low/medium/high) or performance level</li>
                </ul>
                <p><strong>Note:</strong> Column names are flexible - the script will try to match common variations.</p>
              </div>";
        
        // Create dataset directory if it doesn't exist
        if (!is_dir($datasetDir)) {
            mkdir($datasetDir, 0777, true);
            echo "<div class='success'>✅ Created dataset directory: {$datasetDir}</div>";
        }
        
        echo "</div></body></html>";
        exit;
    }
    
    echo "<div class='info'>📂 Found dataset file: student_performance.csv</div>";
    
    // Read CSV file
    $file = fopen($csvFile, 'r');
    if (!$file) {
        throw new Exception("Cannot open CSV file");
    }
    
    // Read header
    $headers = fgetcsv($file);
    if (!$headers) {
        throw new Exception("CSV file is empty or invalid");
    }
    
    // Normalize header names (case-insensitive matching)
    $normalizedHeaders = [];
    foreach ($headers as $header) {
        $normalized = strtolower(trim($header));
        $normalizedHeaders[$normalized] = $header;
    }
    
    // Map common column names
    $columnMap = [
        'gpa' => ['gpa', 'grade_point_average', 'cgpa'],
        'attendance' => ['attendance', 'attendance_rate', 'attendance_percentage', 'attendance%'],
        'avg_grade' => ['avg_grade', 'average_grade', 'mean_grade', 'overall_grade'],
        'assignments' => ['assignments', 'assignments_completed', 'completed_assignments', 'num_assignments'],
        'risk_level' => ['risk_level', 'risk', 'performance', 'performance_level', 'status']
    ];
    
    $mappedColumns = [];
    foreach ($columnMap as $target => $variations) {
        foreach ($variations as $variation) {
            if (isset($normalizedHeaders[$variation])) {
                $mappedColumns[$target] = $normalizedHeaders[$variation];
                break;
            }
        }
    }
    
    echo "<div class='info'>
            <h3>📋 Column Mapping:</h3>
            <ul>";
    foreach ($mappedColumns as $target => $source) {
        echo "<li><strong>{$target}:</strong> {$source}</li>";
    }
    echo "</ul></div>";
    
    if (empty($mappedColumns)) {
        throw new Exception("Could not map CSV columns. Please check column names.");
    }
    
    // Import data
    $imported = 0;
    $skipped = 0;
    $errors = [];
    
    echo "<h3>📥 Importing Data...</h3>";
    
    while (($row = fgetcsv($file)) !== false) {
        if (count($row) < count($headers)) {
            continue; // Skip incomplete rows
        }
        
        // Extract values based on mapped columns
        $data = [];
        foreach ($mappedColumns as $target => $sourceColumn) {
            $index = array_search($sourceColumn, $headers);
            if ($index !== false && isset($row[$index])) {
                $data[$target] = trim($row[$index]);
            }
        }
        
        // Skip if essential data is missing
        if (empty($data['gpa']) || empty($data['attendance'])) {
            $skipped++;
            continue;
        }
        
        // Normalize and validate data
        $gpa = (float)$data['gpa'];
        $attendance = (float)$data['attendance'];
        $avgGrade = isset($data['avg_grade']) ? (float)$data['avg_grade'] : ($gpa * 25); // Estimate if missing
        $assignments = isset($data['assignments']) ? (int)$data['assignments'] : 5; // Default if missing
        
        // Normalize risk level
        $riskLevel = 'medium';
        if (isset($data['risk_level'])) {
            $risk = strtolower($data['risk_level']);
            if (strpos($risk, 'high') !== false || strpos($risk, 'at-risk') !== false || strpos($risk, 'poor') !== false) {
                $riskLevel = 'high';
            } elseif (strpos($risk, 'low') !== false || strpos($risk, 'good') !== false || strpos($risk, 'excellent') !== false) {
                $riskLevel = 'low';
            }
        } else {
            // Infer risk level from GPA
            if ($gpa < 2.0) {
                $riskLevel = 'high';
            } elseif ($gpa >= 3.0) {
                $riskLevel = 'low';
            }
        }
        
        // Validate ranges
        if ($gpa < 0 || $gpa > 4.0) continue;
        if ($attendance < 0 || $attendance > 100) continue;
        if ($avgGrade < 0 || $avgGrade > 100) continue;
        
        // Insert into students table as training data
        // We'll create synthetic student records for training
        try {
            $trainId = 'TRAIN_' . str_pad($imported + 1, 6, '0', STR_PAD_LEFT);
            
            // Check if training record already exists
            $checkSql = "SELECT id FROM students WHERE student_id = :train_id";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([':train_id' => $trainId]);
            $existing = $checkStmt->fetch();
            
            if ($existing) {
                // Update existing record
                $sql = "UPDATE students SET gpa = :gpa, attendance_rate = :attendance, risk_level = :risk 
                        WHERE student_id = :train_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':train_id' => $trainId,
                    ':gpa' => $gpa,
                    ':attendance' => $attendance,
                    ':risk' => $riskLevel
                ]);
                $studentId = $existing['id'];
            } else {
                // Insert new record
                $sql = "INSERT INTO students (user_id, student_id, gpa, attendance_rate, risk_level) 
                        VALUES (NULL, :train_id, :gpa, :attendance, :risk)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':train_id' => $trainId,
                    ':gpa' => $gpa,
                    ':attendance' => $attendance,
                    ':risk' => $riskLevel
                ]);
                $studentId = $pdo->lastInsertId();
            }
            
            // Insert synthetic grades for training (ensure course 1 exists)
            if ($avgGrade > 0 && $studentId) {
                // Check if course 1 exists, create if not
                $courseCheck = $pdo->query("SELECT id FROM courses WHERE id = 1");
                if (!$courseCheck->fetch()) {
                    $pdo->exec("INSERT INTO courses (course_code, course_name, instructor_id) VALUES ('TRAIN001', 'Training Course', NULL)");
                }
                
                // Insert grade if not exists
                $gradeCheckSql = "SELECT id FROM grades WHERE student_id = :student_id AND course_id = 1 LIMIT 1";
                $gradeCheckStmt = $pdo->prepare($gradeCheckSql);
                $gradeCheckStmt->execute([':student_id' => $studentId]);
                
                if (!$gradeCheckStmt->fetch()) {
                    $gradeSql = "INSERT INTO grades (student_id, course_id, assignment_type, grade, max_grade) 
                                VALUES (:student_id, 1, 'exam', :grade, 100)";
                    $gradeStmt = $pdo->prepare($gradeSql);
                    $gradeStmt->execute([
                        ':student_id' => $studentId,
                        ':grade' => $avgGrade
                    ]);
                }
            }
            
            $imported++;
        } catch (PDOException $e) {
            $errors[] = "Row " . ($imported + $skipped + 1) . ": " . $e->getMessage();
        }
    }
    
    fclose($file);
    
    echo "<div class='success'>
            <h3>✅ Import Complete!</h3>
            <p><strong>Imported:</strong> {$imported} training records</p>
            <p><strong>Skipped:</strong> {$skipped} invalid rows</p>
            <p><strong>Errors:</strong> " . count($errors) . "</p>
          </div>";
    
    if (!empty($errors)) {
        echo "<div class='error'>
                <h4>Errors:</h4>
                <ul>";
        foreach (array_slice($errors, 0, 10) as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        if (count($errors) > 10) {
            echo "<li>... and " . (count($errors) - 10) . " more errors</li>";
        }
        echo "</ul></div>";
    }
    
    echo "<div class='info'>
            <h3>🎯 Next Steps:</h3>
            <ol>
                <li>The training data has been imported into the database</li>
                <li>Go to <a href='/projecty/public/index.php?controller=prediction&action=index'>Predictions Page</a></li>
                <li>Click 'Run Predictions' to use the KNN model with this training data</li>
            </ol>
          </div>";
    
    echo "<a href='/projecty/public/index.php?controller=prediction&action=index' class='btn'>Go to Predictions</a>";
    
} catch (Exception $e) {
    echo "<div class='error'>
            <h3>❌ Error</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
          </div>";
}

echo "</div></body></html>";
?>

