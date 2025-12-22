<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Predictions - EduPredict</title>
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
        .prediction-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #2c5aa0;
        }
        .prediction-card.high-risk {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .prediction-card.medium-risk {
            border-left-color: #ffc107;
            background: #fffbf0;
        }
        .prediction-card.low-risk {
            border-left-color: #28a745;
            background: #f0fff4;
        }
        .prediction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .prediction-title {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }
        .confidence-badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .confidence-high {
            background: #28a745;
            color: white;
        }
        .confidence-medium {
            background: #ffc107;
            color: #333;
        }
        .confidence-low {
            background: #dc3545;
            color: white;
        }
        .prediction-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
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
        .risk-factors {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .risk-factors h4 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            color: #666;
        }
        .risk-factor-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .risk-factor-list li {
            padding: 5px 0;
            color: #dc3545;
            font-size: 0.85rem;
        }
        .risk-factor-list li:before {
            content: "⚠️ ";
            margin-right: 5px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
        }
        .run-prediction-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-brain"></i> Performance Predictions</h1>
            <div>
                <a href="/projecty/public/index.php?controller=dashboard&action=<?php echo $role ?? 'index'; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="run-prediction-btn">
            <button onclick="runPredictions()" class="btn btn-success">
                <i class="fas fa-play"></i> Run Predictions for All Students
            </button>
            <span id="predictionStatus" style="margin-left: 15px;"></span>
        </div>

        <div id="predictionsContainer">
            <div class="empty-state">
                <i class="fas fa-chart-line"></i>
                <h2>No Predictions Yet</h2>
                <p>Click "Run Predictions" to generate performance predictions using KNN machine learning algorithm.</p>
            </div>
        </div>
    </div>

    <script>
        function runPredictions() {
            const statusEl = document.getElementById('predictionStatus');
            statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Running predictions...';
            
            fetch('/projecty/public/index.php?controller=prediction&action=predictAll')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusEl.innerHTML = '<span style="color: green;"><i class="fas fa-check"></i> Predictions completed!</span>';
                        displayPredictions(data.data);
                    } else {
                        statusEl.innerHTML = '<span style="color: red;">Error: ' + data.error + '</span>';
                    }
                })
                .catch(error => {
                    statusEl.innerHTML = '<span style="color: red;">Error: ' + error.message + '</span>';
                });
        }

        function displayPredictions(predictions) {
            const container = document.getElementById('predictionsContainer');
            
            if (predictions.length === 0) {
                container.innerHTML = '<div class="empty-state"><i class="fas fa-info-circle"></i><h2>No Predictions Available</h2><p>No student data available for predictions.</p></div>';
                return;
            }
            
            let html = '';
            predictions.forEach(pred => {
                const riskClass = pred.risk_level === 'high' ? 'high-risk' : 
                                 pred.risk_level === 'medium' ? 'medium-risk' : 'low-risk';
                const confidenceClass = pred.confidence >= 0.7 ? 'confidence-high' :
                                       pred.confidence >= 0.5 ? 'confidence-medium' : 'confidence-low';
                
                html += `
                    <div class="prediction-card ${riskClass}">
                        <div class="prediction-header">
                            <div class="prediction-title">Student ID: ${pred.student_id}</div>
                            <span class="confidence-badge ${confidenceClass}">
                                Confidence: ${(pred.confidence * 100).toFixed(0)}%
                            </span>
                        </div>
                        <div class="prediction-details">
                            <div class="detail-item">
                                <div class="detail-label">Predicted Grade</div>
                                <div class="detail-value">${pred.predicted_grade.toFixed(2)}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Risk Level</div>
                                <div class="detail-value" style="text-transform: capitalize;">${pred.risk_level}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">GPA</div>
                                <div class="detail-value">${pred.features.gpa.toFixed(2)}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Attendance</div>
                                <div class="detail-value">${pred.features.attendance_rate.toFixed(1)}%</div>
                            </div>
                        </div>
                        ${pred.risk_factors && pred.risk_factors.length > 0 ? `
                            <div class="risk-factors">
                                <h4>Risk Factors:</h4>
                                <ul class="risk-factor-list">
                                    ${pred.risk_factors.map(factor => '<li>' + factor + '</li>').join('')}
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
    </script>
</body>
</html>



