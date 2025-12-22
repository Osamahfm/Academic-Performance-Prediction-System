<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerts - EduPredict</title>
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
        .btn-secondary {
            background: #6c757d;
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
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        .alert-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #2c5aa0;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }
        .alert-item.high {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .alert-item.medium {
            border-left-color: #ffc107;
            background: #fffbf0;
        }
        .alert-item.low {
            border-left-color: #28a745;
            background: #f0fff4;
        }
        .alert-content {
            flex: 1;
        }
        .alert-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .alert-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        .alert-icon.high {
            background: #dc3545;
        }
        .alert-icon.medium {
            background: #ffc107;
        }
        .alert-icon.low {
            background: #28a745;
        }
        .alert-title {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }
        .alert-message {
            color: #666;
            margin: 10px 0;
            line-height: 1.6;
        }
        .alert-meta {
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
            color: #999;
            margin-top: 10px;
        }
        .alert-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .badge-at_risk {
            background: #dc3545;
            color: white;
        }
        .badge-low_performance {
            background: #ffc107;
            color: #333;
        }
        .badge-attendance {
            background: #fd7e14;
            color: white;
        }
        .badge-grade_drop {
            background: #dc3545;
            color: white;
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
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-bell"></i> Student Alerts</h1>
            <div>
                <a href="/projecty/public/index.php?controller=dashboard&action=<?php echo $role ?? 'index'; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Alert <?php echo htmlspecialchars($_GET['success']); ?> successfully!
            </div>
        <?php endif; ?>

        <?php if (!empty($alerts)): ?>
            <?php foreach ($alerts as $alert): ?>
                <div class="alert-item <?php echo htmlspecialchars($alert['severity'] ?? 'medium'); ?>">
                    <div class="alert-content">
                        <div class="alert-header">
                            <div class="alert-icon <?php echo htmlspecialchars($alert['severity'] ?? 'medium'); ?>">
                                <?php
                                $icon = 'fas fa-exclamation-circle';
                                if ($alert['alert_type'] === 'at_risk') {
                                    $icon = 'fas fa-exclamation-triangle';
                                } elseif ($alert['alert_type'] === 'attendance') {
                                    $icon = 'fas fa-calendar-times';
                                } elseif ($alert['alert_type'] === 'grade_drop') {
                                    $icon = 'fas fa-chart-line-down';
                                }
                                ?>
                                <i class="<?php echo $icon; ?>"></i>
                            </div>
                            <div>
                                <div class="alert-title">
                                    <?php echo htmlspecialchars($alert['student_name'] ?? 'Student'); ?>
                                    <?php if (!empty($alert['course_name'])): ?>
                                        - <?php echo htmlspecialchars($alert['course_name']); ?>
                                    <?php endif; ?>
                                </div>
                                <span class="badge badge-<?php echo str_replace('_', '-', $alert['alert_type'] ?? 'low_performance'); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $alert['alert_type'] ?? 'low_performance')); ?>
                                </span>
                            </div>
                        </div>
                        <div class="alert-message">
                            <?php echo nl2br(htmlspecialchars($alert['message'] ?? 'No message')); ?>
                        </div>
                        <div class="alert-meta">
                            <span><i class="fas fa-clock"></i> <?php echo isset($alert['created_at']) ? date('M j, Y g:i A', strtotime($alert['created_at'])) : 'Recently'; ?></span>
                            <?php if (!empty($alert['student_id'])): ?>
                                <span><i class="fas fa-id-card"></i> ID: <?php echo htmlspecialchars($alert['student_id']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="alert-actions">
                        <a href="/projecty/public/index.php?controller=alert&action=resolve&id=<?php echo $alert['id']; ?>" class="btn btn-success" style="padding: 8px 15px; font-size: 0.85rem;">
                            <i class="fas fa-check"></i> Resolve
                        </a>
                        <a href="/projecty/public/index.php?controller=alert&action=dismiss&id=<?php echo $alert['id']; ?>" class="btn btn-warning" style="padding: 8px 15px; font-size: 0.85rem;">
                            <i class="fas fa-times"></i> Dismiss
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h2>No Active Alerts</h2>
                <p>All students are performing well. No alerts at this time.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>



