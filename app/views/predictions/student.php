<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Predictions - EduPredict</title>
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
            max-width: 1000px;
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
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .prediction-summary {
            background: linear-gradient(135deg, #2c5aa0, #1e3a8a);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .prediction-summary h2 {
            margin: 0 0 20px 0;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .summary-stat {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
        }
        .summary-stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .prediction-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #2c5aa0;
        }
        .prediction-card h3 {
            margin: 0 0 15px 0;
            color: #333;
        }
        .prediction-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .detail-item {
            background: white;
            padding: 12px;
            border-radius: 8px;
        }
        .detail-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .performance-message {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        .performance-message.warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        .performance-message.danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        .performance-message h2 {
            margin: 0 0 10px 0;
            font-size: 1.5rem;
        }
        .performance-message p {
            margin: 0;
            font-size: 1.1rem;
            opacity: 0.95;
        }
        .prediction-interpretation {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            border-left: 4px solid #2c5aa0;
        }
        .prediction-interpretation h4 {
            margin: 0 0 10px 0;
            color: #2c5aa0;
        }
        .prediction-interpretation ul {
            margin: 10px 0 0 0;
            padding-left: 20px;
        }
        .prediction-interpretation li {
            margin: 8px 0;
            color: #555;
        }
        .grade-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-left: 10px;
        }
        .grade-badge.excellent { background: #28a745; color: white; }
        .grade-badge.good { background: #2c5aa0; color: white; }
        .grade-badge.average { background: #ffc107; color: #333; }
        .grade-badge.below { background: #fd7e14; color: white; }
        .grade-badge.failing { background: #dc3545; color: white; }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> My Performance Predictions</h1>
            <div>
                <a href="/projecty/public/index.php?controller=prediction&action=index&refresh=1" class="btn btn-success" style="margin-right: 10px;">
                    <i class="fas fa-sync-alt"></i> Refresh Predictions
                </a>
                <a href="/projecty/public/index.php?controller=dashboard&action=student" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if ($student): ?>
            <div class="prediction-summary">
                <h2>Your Performance Overview</h2>
                <div class="summary-stats">
                    <div class="summary-stat">
                        <div class="summary-stat-value"><?php echo number_format($student['gpa'] ?? 0, 2); ?></div>
                        <div class="summary-stat-label">Current GPA</div>
                    </div>
                    <div class="summary-stat">
                        <div class="summary-stat-value"><?php echo number_format($student['attendance_rate'] ?? 0, 0); ?>%</div>
                        <div class="summary-stat-label">Attendance</div>
                    </div>
                    <div class="summary-stat">
                        <div class="summary-stat-value" style="text-transform: capitalize;"><?php echo htmlspecialchars($student['risk_level'] ?? 'Low'); ?></div>
                        <div class="summary-stat-label">Risk Level</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php 
        // Get overall prediction for performance message
        $overallPrediction = null;
        if (!empty($predictions)) {
            foreach ($predictions as $pred) {
                if (empty($pred['course_name'])) {
                    $overallPrediction = $pred;
                    break;
                }
            }
        }
        
        // Determine performance message
        if ($overallPrediction):
            $predictedGrade = $overallPrediction['predicted_grade'] ?? 0;
            $riskLevel = strtolower($overallPrediction['risk_level'] ?? 'low');
            
            if ($predictedGrade >= 90):
                $messageClass = '';
                $messageTitle = '🎉 Excellent Performance Predicted!';
                $messageText = "Based on your current academic data, you're predicted to achieve an excellent grade (A). Keep up the great work!";
            elseif ($predictedGrade >= 80):
                $messageClass = '';
                $messageTitle = '👍 Good Performance Predicted';
                $messageText = "You're predicted to achieve a good grade (B). Continue maintaining your current performance!";
            elseif ($predictedGrade >= 70):
                $messageClass = 'warning';
                $messageTitle = '⚠️ Average Performance Predicted';
                $messageText = "You're predicted to achieve an average grade (C). Consider improving your study habits to boost your performance.";
            elseif ($predictedGrade >= 60):
                $messageClass = 'warning';
                $messageTitle = '⚠️ Below Average Performance';
                $messageText = "You're predicted to achieve a below average grade (D). Focus on improving attendance and completing assignments.";
            else:
                $messageClass = 'danger';
                $messageTitle = '🚨 At Risk of Failing';
                $messageText = "You're at risk of failing. Please contact your instructor and focus on improving your performance immediately.";
            endif;
        ?>
            <div class="performance-message <?php echo $messageClass; ?>">
                <h2><?php echo $messageTitle; ?></h2>
                <p><?php echo $messageText; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($predictions)): ?>
            <?php foreach ($predictions as $prediction): ?>
                <?php
                // Skip overall prediction in the list (already shown in message)
                if (empty($prediction['course_name'])) {
                    continue;
                }
                
                $predictedGrade = $prediction['predicted_grade'] ?? 0;
                $gradeLetter = '';
                $gradeClass = '';
                
                if ($predictedGrade >= 90) {
                    $gradeLetter = 'A (Excellent)';
                    $gradeClass = 'excellent';
                } elseif ($predictedGrade >= 80) {
                    $gradeLetter = 'B (Good)';
                    $gradeClass = 'good';
                } elseif ($predictedGrade >= 70) {
                    $gradeLetter = 'C (Average)';
                    $gradeClass = 'average';
                } elseif ($predictedGrade >= 60) {
                    $gradeLetter = 'D (Below Average)';
                    $gradeClass = 'below';
                } else {
                    $gradeLetter = 'F (Failing)';
                    $gradeClass = 'failing';
                }
                ?>
                <div class="prediction-card">
                    <h3>
                        <?php echo htmlspecialchars($prediction['course_name']); ?>
                        <?php if (!empty($prediction['course_code'])): ?>
                            <span style="font-size: 0.9rem; color: #666; font-weight: normal;">(<?php echo htmlspecialchars($prediction['course_code']); ?>)</span>
                        <?php endif; ?>
                        <span class="grade-badge <?php echo $gradeClass; ?>"><?php echo $gradeLetter; ?></span>
                    </h3>
                    <div class="prediction-details">
                        <div class="detail-item">
                            <div class="detail-label">Predicted Grade</div>
                            <div class="detail-value">
                                <?php echo number_format($predictedGrade, 2); ?>%
                                <span class="grade-badge <?php echo $gradeClass; ?>" style="font-size: 0.8rem; padding: 4px 10px; margin-left: 8px;">
                                    <?php echo substr($gradeLetter, 0, 1); ?>
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Confidence Score</div>
                            <div class="detail-value"><?php echo number_format(($prediction['confidence_score'] ?? 0) * 100, 0); ?>%</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Prediction Date</div>
                            <div class="detail-value" style="font-size: 0.9rem;">
                                <?php echo isset($prediction['prediction_date']) ? date('M j, Y', strtotime($prediction['prediction_date'])) : 'N/A'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Performance Interpretation -->
                    <div class="prediction-interpretation">
                        <h4><i class="fas fa-info-circle"></i> What This Means:</h4>
                        <p style="margin: 0 0 10px 0; color: #555;">
                            Based on your current performance in <strong><?php echo htmlspecialchars($prediction['course_name']); ?></strong>, 
                            you're predicted to achieve a grade of <strong><?php echo number_format($predictedGrade, 1); ?>%</strong> 
                            (<?php echo $gradeLetter; ?>).
                        </p>
                        <?php if (($prediction['confidence_score'] ?? 0) < 0.5): ?>
                            <p style="margin: 10px 0; color: #856404; font-size: 0.9rem;">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Note:</strong> This prediction has lower confidence. More assignment data will improve accuracy.
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($prediction['risk_factors'])): ?>
                        <?php 
                        $riskFactors = json_decode($prediction['risk_factors'], true);
                        if (is_array($riskFactors) && !empty($riskFactors)):
                        ?>
                            <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
                                <h4 style="margin: 0 0 10px 0; font-size: 0.9rem; color: #856404;">
                                    <i class="fas fa-exclamation-triangle"></i> Areas to Improve:
                                </h4>
                                <ul style="list-style: none; padding: 0; margin: 0;">
                                    <?php foreach ($riskFactors as $factor): ?>
                                        <li style="padding: 5px 0; color: #856404; font-size: 0.85rem;">
                                            <i class="fas fa-arrow-right" style="margin-right: 5px;"></i>
                                            <?php echo htmlspecialchars($factor); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <!-- Recommendations -->
                    <?php if ($predictedGrade < 80): ?>
                        <div class="prediction-interpretation" style="margin-top: 15px; background: #e7f3ff; border-left-color: #2c5aa0;">
                            <h4 style="color: #2c5aa0;"><i class="fas fa-lightbulb"></i> Recommendations:</h4>
                            <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #555;">
                                <?php if ($predictedGrade < 70): ?>
                                    <li>Attend all classes regularly to improve attendance</li>
                                    <li>Complete all assignments on time</li>
                                    <li>Seek help from your instructor or tutoring services</li>
                                    <li>Review course materials regularly</li>
                                <?php else: ?>
                                    <li>Maintain your current attendance rate</li>
                                    <li>Complete all remaining assignments</li>
                                    <li>Review course materials to strengthen understanding</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php elseif ($predictedGrade >= 90): ?>
                        <div class="prediction-interpretation" style="margin-top: 15px; background: #d4edda; border-left-color: #28a745;">
                            <h4 style="color: #155724;"><i class="fas fa-check-circle"></i> Keep It Up!</h4>
                            <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #155724;">
                                <li>Continue maintaining excellent attendance</li>
                                <li>Keep completing assignments on time</li>
                                <li>Stay engaged in class discussions</li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <!-- Overall Prediction Card -->
            <?php if ($overallPrediction): ?>
                <div class="prediction-card" style="border-left-color: #2c5aa0; background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                    <h3>
                        <i class="fas fa-chart-pie"></i> Overall Academic Performance Prediction
                    </h3>
                    <div class="prediction-details">
                        <div class="detail-item">
                            <div class="detail-label">Overall Predicted Grade</div>
                            <div class="detail-value">
                                <?php echo number_format($overallPrediction['predicted_grade'] ?? 0, 2); ?>%
                                <?php
                                $overallGrade = $overallPrediction['predicted_grade'] ?? 0;
                                $overallLetter = '';
                                $overallClass = '';
                                if ($overallGrade >= 90) {
                                    $overallLetter = 'A';
                                    $overallClass = 'excellent';
                                } elseif ($overallGrade >= 80) {
                                    $overallLetter = 'B';
                                    $overallClass = 'good';
                                } elseif ($overallGrade >= 70) {
                                    $overallLetter = 'C';
                                    $overallClass = 'average';
                                } elseif ($overallGrade >= 60) {
                                    $overallLetter = 'D';
                                    $overallClass = 'below';
                                } else {
                                    $overallLetter = 'F';
                                    $overallClass = 'failing';
                                }
                                ?>
                                <span class="grade-badge <?php echo $overallClass; ?>" style="font-size: 0.8rem; padding: 4px 10px; margin-left: 8px;">
                                    <?php echo $overallLetter; ?>
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Confidence Score</div>
                            <div class="detail-value"><?php echo number_format(($overallPrediction['confidence_score'] ?? 0) * 100, 0); ?>%</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Prediction Date</div>
                            <div class="detail-value" style="font-size: 0.9rem;">
                                <?php echo isset($overallPrediction['prediction_date']) ? date('M j, Y', strtotime($overallPrediction['prediction_date'])) : 'N/A'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="prediction-interpretation" style="margin-top: 15px;">
                        <h4><i class="fas fa-info-circle"></i> Overall Performance Summary:</h4>
                        <p style="margin: 0; color: #555;">
                            This is your overall academic performance prediction across all enrolled courses. 
                            It's based on your current GPA (<?php echo number_format($student['gpa'] ?? 0, 2); ?>), 
                            attendance rate (<?php echo number_format($student['attendance_rate'] ?? 0, 0); ?>%), 
                            and assignment completion.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-info-circle"></i>
                <h2>No Predictions Available</h2>
                <p>Predictions will be generated automatically based on your academic performance.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

