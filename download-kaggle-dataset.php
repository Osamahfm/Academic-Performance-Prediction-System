<?php
/**
 * Download Kaggle Dataset Helper
 * Provides instructions and links to download datasets from Kaggle
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Download Kaggle Dataset - EduPredict</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c5aa0; }
        h2 { color: #1e3a8a; margin-top: 30px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #bee5eb; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #c3e6cb; }
        .dataset-card { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #2c5aa0; }
        .dataset-card h3 { margin-top: 0; color: #2c5aa0; }
        .btn { display: inline-block; padding: 10px 20px; background: #2c5aa0; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px 10px 0; }
        .btn:hover { background: #1e3a8a; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        ul { line-height: 1.8; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📥 Download Kaggle Dataset for KNN Training</h1>
        
        <div class='info'>
            <h3>📋 Quick Start</h3>
            <ol>
                <li>Choose a dataset from the list below</li>
                <li>Download the CSV file from Kaggle</li>
                <li>Rename it to <code>student_performance.csv</code></li>
                <li>Place it in: <code>projecty/utilities/dataset/</code></li>
                <li>Run the <a href='import-kaggle-dataset.php'>Import Script</a></li>
            </ol>
        </div>
        
        <h2>🎯 Recommended Kaggle Datasets</h2>
        
        <div class='dataset-card'>
            <h3>1. Students Performance in Exams</h3>
            <p><strong>Kaggle URL:</strong> <a href='https://www.kaggle.com/datasets/spscientist/students-performance-in-exams' target='_blank'>kaggle.com/datasets/spscientist/students-performance-in-exams</a></p>
            <p><strong>Records:</strong> 1,000 students</p>
            <p><strong>Features:</strong> Math, Reading, Writing scores, Gender, Race, Parent education, Lunch type, Test preparation</p>
            <p><strong>How to use:</strong> Calculate GPA from scores, use attendance data if available, or generate synthetic attendance</p>
            <a href='https://www.kaggle.com/datasets/spscientist/students-performance-in-exams' target='_blank' class='btn'>Download Dataset</a>
        </div>
        
        <div class='dataset-card'>
            <h3>2. Student Performance and Learning Behavior</h3>
            <p><strong>Kaggle URL:</strong> <a href='https://www.kaggle.com/datasets/aljarah/xAPI-Edu-Data' target='_blank'>kaggle.com/datasets/aljarah/xAPI-Edu-Data</a></p>
            <p><strong>Records:</strong> 480 students</p>
            <p><strong>Features:</strong> Student performance, attendance, participation, grades</p>
            <p><strong>How to use:</strong> Direct mapping to GPA, attendance, and grades</p>
            <a href='https://www.kaggle.com/datasets/aljarah/xAPI-Edu-Data' target='_blank' class='btn'>Download Dataset</a>
        </div>
        
        <div class='dataset-card'>
            <h3>3. College Student Performance and Placement</h3>
            <p><strong>Kaggle URL:</strong> <a href='https://www.kaggle.com/datasets/tejashvi14/college-student-performance-and-placement-data' target='_blank'>kaggle.com/datasets/tejashvi14/college-student-performance-and-placement-data</a></p>
            <p><strong>Records:</strong> 10,000+ students</p>
            <p><strong>Features:</strong> GPA, IQ scores, previous semester results, academic performance ratings</p>
            <p><strong>How to use:</strong> Direct use of GPA and performance data</p>
            <a href='https://www.kaggle.com/datasets/tejashvi14/college-student-performance-and-placement-data' target='_blank' class='btn'>Download Dataset</a>
        </div>
        
        <h2>📝 CSV Format Requirements</h2>
        
        <div class='info'>
            <h3>Required Columns (flexible naming):</h3>
            <ul>
                <li><code>gpa</code> or <code>GPA</code> - Grade Point Average (0-4.0)</li>
                <li><code>attendance</code> or <code>attendance_rate</code> - Attendance percentage (0-100)</li>
                <li><code>avg_grade</code> or <code>average_grade</code> - Average grade (0-100) [Optional]</li>
                <li><code>assignments</code> or <code>assignments_completed</code> - Number of assignments [Optional]</li>
                <li><code>risk_level</code> or <code>performance</code> - Risk level (low/medium/high) [Optional]</li>
            </ul>
            
            <h3>Example CSV Format:</h3>
            <pre style='background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;'>
gpa,attendance_rate,avg_grade,assignments_completed,risk_level
3.5,95.0,87.5,12,low
2.1,65.0,52.5,5,high
3.0,85.0,75.0,10,medium
2.8,78.0,70.0,8,medium
3.7,98.0,92.5,15,low
            </pre>
        </div>
        
        <h2>🔧 Alternative: Generate Sample Data</h2>
        
        <div class='success'>
            <p>Don't have a Kaggle dataset? No problem!</p>
            <p>We can generate synthetic training data with 200 samples.</p>
            <a href='generate-sample-dataset.php' class='btn btn-secondary'>Generate Sample Dataset</a>
        </div>
        
        <h2>📊 After Download</h2>
        
        <div class='info'>
            <ol>
                <li>Save the CSV file as <code>student_performance.csv</code></li>
                <li>Place it in: <code>projecty/utilities/dataset/</code> folder</li>
                <li>Run the import script: <a href='import-kaggle-dataset.php'>Import Dataset</a></li>
                <li>Go to Predictions page and click 'Run Predictions'</li>
            </ol>
        </div>
        
        <div style='margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;'>
            <a href='import-kaggle-dataset.php' class='btn'>Import Dataset</a>
            <a href='/projecty/public/index.php?controller=prediction&action=index' class='btn btn-secondary'>Go to Predictions</a>
        </div>
    </div>
</body>
</html>";
?>







