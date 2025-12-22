<?php
/**
 * Generate Sample Academic Performance Dataset
 * Creates a CSV file with synthetic student performance data for KNN training
 */

$datasetDir = __DIR__ . '/dataset';
if (!is_dir($datasetDir)) {
    mkdir($datasetDir, 0777, true);
}

$csvFile = $datasetDir . '/student_performance.csv';

// Generate synthetic training data
$data = [];
$data[] = ['gpa', 'attendance_rate', 'avg_grade', 'assignments_completed', 'risk_level']; // Header

// Generate 200 sample records
for ($i = 0; $i < 200; $i++) {
    // Generate GPA (0-4.0)
    $gpa = round(mt_rand(150, 400) / 100, 2);
    
    // Generate attendance (50-100%)
    $attendance = round(mt_rand(5000, 10000) / 100, 1);
    
    // Generate average grade (correlated with GPA)
    $avgGrade = round(($gpa * 25) + mt_rand(-10, 10), 2);
    if ($avgGrade < 0) $avgGrade = 0;
    if ($avgGrade > 100) $avgGrade = 100;
    
    // Generate assignments completed (3-15)
    $assignments = mt_rand(3, 15);
    
    // Determine risk level based on GPA and attendance
    $riskLevel = 'medium';
    if ($gpa < 2.0 || $attendance < 70) {
        $riskLevel = 'high';
    } elseif ($gpa >= 3.0 && $attendance >= 85) {
        $riskLevel = 'low';
    }
    
    $data[] = [$gpa, $attendance, $avgGrade, $assignments, $riskLevel];
}

// Write to CSV
$file = fopen($csvFile, 'w');
if ($file) {
    foreach ($data as $row) {
        fputcsv($file, $row);
    }
    fclose($file);
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Dataset Generated - EduPredict</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #c3e6cb; }
            .btn { display: inline-block; padding: 10px 20px; background: #2c5aa0; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>✅ Sample Dataset Generated</h1>
            <div class='success'>
                <p><strong>File created:</strong> {$csvFile}</p>
                <p><strong>Records:</strong> 200 training samples</p>
                <p><strong>Columns:</strong> GPA, Attendance Rate, Avg Grade, Assignments Completed, Risk Level</p>
            </div>
            <p>This synthetic dataset will be used to train the KNN model.</p>
            <a href='/projecty/utilities/import-kaggle-dataset.php' class='btn'>Import Dataset Now</a>
            <a href='/projecty/public/index.php?controller=prediction&action=index' class='btn' style='margin-left: 10px;'>Go to Predictions</a>
        </div>
    </body>
    </html>";
} else {
    echo "Error: Could not create CSV file";
}
?>



