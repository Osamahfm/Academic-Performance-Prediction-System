<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Train KNN Model - Admin - EduPredict</title>
    <link rel="stylesheet" href="/projecty/public/assets/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .header h1 {
            margin: 0;
            color: #2c5aa0;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background: #2c5aa0;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
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
            border-radius: 10px;
            text-align: center;
            border: 2px solid transparent;
        }
        .stat-card.good {
            border-color: #28a745;
        }
        .stat-card.warning {
            border-color: #ffc107;
        }
        .stat-card.danger {
            border-color: #dc3545;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #155724;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .error-box {
            background: #f8d7da;
            border-left: 4px solid #721c24;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #856404;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .training-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .training-section h2 {
            margin-top: 0;
            color: #2c5aa0;
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
        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-low { background: #28a745; color: white; }
        .badge-medium { background: #ffc107; color: #333; }
        .badge-high { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-brain"></i> Train KNN Model</h1>
            <div>
                <a href="/projecty/public/dashboard/admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if ($trainingResults): ?>
            <?php if ($trainingResults['success']): ?>
                <div class="success-box">
                    <h3>✅ Training Complete!</h3>
                    <p><strong>Students Trained:</strong> <?php echo $trainingResults['trained']; ?></p>
                    <?php if ($trainingResults['errors'] > 0): ?>
                        <p><strong>Errors:</strong> <?php echo $trainingResults['errors']; ?></p>
                        <?php if (!empty($trainingResults['errorMessages'])): ?>
                            <details style="margin-top: 10px;">
                                <summary style="cursor: pointer; color: #856404;">Show Error Details</summary>
                                <ul style="margin-top: 10px;">
                                    <?php foreach (array_slice($trainingResults['errorMessages'], 0, 10) as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </details>
                        <?php endif; ?>
                    <?php endif; ?>
                    <p style="margin-top: 10px;"><a href="/projecty/public/predictions" class="btn btn-success">View Predictions →</a></p>
                </div>
            <?php else: ?>
                <div class="error-box">
                    <h3>❌ Training Failed</h3>
                    <p><?php echo htmlspecialchars($trainingResults['error'] ?? 'Unknown error'); ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total_students'] ?? 0; ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['students_with_data'] ?? 0; ?></div>
                <div class="stat-label">With GPA & Attendance</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['students_with_grades'] ?? 0; ?></div>
                <div class="stat-label">With Grades</div>
            </div>
            <div class="stat-card <?php 
                $ready = $stats['training_ready'] ?? 0;
                echo $ready >= 10 ? 'good' : ($ready >= 5 ? 'warning' : 'danger');
            ?>">
                <div class="stat-value"><?php echo $ready; ?></div>
                <div class="stat-label">Training Ready</div>
            </div>
        </div>

        <!-- Training Status -->
        <?php 
        $ready = $stats['training_ready'] ?? 0;
        if ($ready < 5): 
        ?>
            <div class="warning-box">
                <h3>⚠️ Insufficient Training Data</h3>
                <p>You need at least <strong>5 students</strong> with complete data (GPA, attendance, and grades) for KNN to work properly.</p>
                <p><strong>Current:</strong> <?php echo $ready; ?> students ready</p>
                <p><strong>Actions needed:</strong></p>
                <ul>
                    <li><a href="/projecty/utilities/manage-student-gpa.php">Add GPA & Attendance</a> to students</li>
                    <li><a href="/projecty/public/enrollment">Enroll students in courses</a></li>
                    <li><a href="/projecty/public/grade/manage">Add grades</a> to students</li>
                </ul>
            </div>
        <?php elseif ($ready < 10): ?>
            <div class="info-box">
                <h3>ℹ️ Limited Training Data</h3>
                <p>You have <strong><?php echo $ready; ?> students</strong> ready for training. More data (10+) will improve prediction accuracy.</p>
            </div>
        <?php else: ?>
            <div class="success-box">
                <h3>✅ Sufficient Training Data</h3>
                <p>You have <strong><?php echo $ready; ?> students</strong> ready for training. The model should work well!</p>
            </div>
        <?php endif; ?>

        <!-- Training Section -->
        <div class="training-section">
            <h2>🚀 Train Model</h2>
            <p>Click the button below to run predictions for all students and update the KNN model. This will:</p>
            <ul>
                <li>Generate predictions for all students with complete data</li>
                <li>Update risk levels based on current performance</li>
                <li>Create alerts for at-risk students</li>
                <li>Save predictions to the database</li>
            </ul>
            
            <form method="POST" style="margin-top: 20px;">
                <button type="submit" name="train_model" class="btn btn-success" style="font-size: 16px; padding: 15px 30px;">
                    <i class="fas fa-brain"></i> Train KNN Model for All Students
                </button>
            </form>
            
            <p style="margin-top: 15px; color: #666; font-size: 0.9rem;">
                <strong>Note:</strong> Training uses all available student data. The more data you have, the more accurate predictions will be.
            </p>
        </div>

        <!-- Sample Training Data -->
        <?php if (!empty($trainingData)): ?>
            <div class="training-section">
                <h2>📊 Sample Training Data</h2>
                <p>These are examples of students that will be used for training:</p>
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>GPA</th>
                            <th>Attendance</th>
                            <th>Avg Grade</th>
                            <th>Assignments</th>
                            <th>Risk Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trainingData as $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['student_id']); ?></td>
                                <td><?php echo number_format($data['gpa'], 2); ?></td>
                                <td><?php echo number_format($data['attendance_rate'], 1); ?>%</td>
                                <td><?php echo number_format($data['avg_grade'] ?? 0, 2); ?></td>
                                <td><?php echo $data['assignments_completed'] ?? 0; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($data['risk_level'] ?? 'medium'); ?>">
                                        <?php echo ucfirst($data['risk_level'] ?? 'Medium'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- How It Works -->
        <div class="info-box" style="margin-top: 30px;">
            <h3>📚 How KNN Training Works</h3>
            <ol>
                <li><strong>Training Data:</strong> The model uses students with GPA, attendance, average grades, and assignment counts</li>
                <li><strong>Features:</strong> Each student is represented by [GPA, Attendance Rate, Average Grade, Assignments Completed]</li>
                <li><strong>Prediction:</strong> When predicting for a new student, KNN finds the 5 most similar students (nearest neighbors)</li>
                <li><strong>Result:</strong> The model predicts risk level and grade based on the majority vote of neighbors</li>
            </ol>
            <p style="margin-top: 10px;"><strong>Note:</strong> KNN doesn't require traditional 'training' - it uses all available data each time. Running 'Train Model' updates predictions for all students.</p>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
            <a href="/projecty/public/dashboard/admin" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="/projecty/public/predictions" class="btn btn-primary">View Predictions →</a>
            <a href="/projecty/utilities/manage-student-gpa.php" class="btn btn-success">Manage GPA →</a>
        </div>
    </div>
</body>
</html>





