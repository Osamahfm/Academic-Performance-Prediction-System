<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is instructor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header('Location: login.php');
    exit;
}

$user_name = $_SESSION['name'];
$user_email = $_SESSION['email'];
$user_id = $_SESSION['user_id'];

// Get dashboard statistics
try {
    $pdo = getDBConnection();
    
    // Get instructor's courses
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE instructor_id = ?");
    $stmt->execute([$user_id]);
    $total_courses = $stmt->fetchColumn();
    
    // Get total students in instructor's courses
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT e.student_id) 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        WHERE c.instructor_id = ? AND e.status = 'active'
    ");
    $stmt->execute([$user_id]);
    $total_students = $stmt->fetchColumn();
    
    // Get at-risk students in instructor's courses
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT s.id) 
        FROM students s 
        JOIN enrollments e ON s.id = e.student_id 
        JOIN courses c ON e.course_id = c.id 
        WHERE c.instructor_id = ? AND s.risk_level IN ('medium', 'high') AND e.status = 'active'
    ");
    $stmt->execute([$user_id]);
    $at_risk_students = $stmt->fetchColumn();
    
    // Get recent alerts for instructor's students
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM alerts a 
        JOIN students s ON a.student_id = s.id 
        JOIN enrollments e ON s.id = e.student_id 
        JOIN courses c ON e.course_id = c.id 
        WHERE c.instructor_id = ? AND a.status = 'active'
    ");
    $stmt->execute([$user_id]);
    $active_alerts = $stmt->fetchColumn();
    
    // Get recent at-risk students
    $stmt = $pdo->prepare("
        SELECT s.student_id, s.risk_level, u.name, c.course_name
        FROM students s 
        JOIN users u ON s.user_id = u.id 
        JOIN enrollments e ON s.id = e.student_id 
        JOIN courses c ON e.course_id = c.id 
        WHERE c.instructor_id = ? AND s.risk_level IN ('medium', 'high') AND e.status = 'active'
        ORDER BY s.risk_level DESC, s.id DESC 
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $at_risk_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $total_courses = 0;
    $total_students = 0;
    $at_risk_students = 0;
    $active_alerts = 0;
    $at_risk_list = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard - EduPredict</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .dashboard-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-left {
            display: flex;
            align-items: center;
        }
        
        .header-left i {
            font-size: 2rem;
            color: #2c5aa0;
            margin-right: 15px;
        }
        
        .header-left h1 {
            color: #333;
            margin: 0;
            font-size: 1.8rem;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #2c5aa0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        .dashboard-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2c5aa0, #1e3a8a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c5aa0;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .chart-card h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 1.3rem;
        }
        
        .at-risk-students {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .at-risk-students h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 1.3rem;
        }
        
        .student-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .student-item:last-child {
            border-bottom: none;
        }
        
        .student-avatar {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #2c5aa0;
            font-weight: bold;
        }
        
        .student-content {
            flex: 1;
        }
        
        .student-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }
        
        .student-course {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .risk-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .risk-high {
            background: #f8d7da;
            color: #721c24;
        }
        
        .risk-medium {
            background: #fff3cd;
            color: #856404;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .action-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #2c5aa0, #1e3a8a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.2rem;
        }
        
        .action-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .action-desc {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h1>Instructor Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                        </div>
                        <div>
                            <div style="font-weight: 500;"><?php echo $user_name; ?></div>
                            <div style="font-size: 0.9rem; color: #666;"><?php echo $user_email; ?></div>
                        </div>
                    </div>
                    <a href="index.php" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; text-decoration: none; transition: all 0.3s ease; margin-right: 10px;">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($total_courses); ?></div>
                    <div class="stat-label">My Courses</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($total_students); ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($at_risk_students); ?></div>
                    <div class="stat-label">At-Risk Students</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($active_alerts); ?></div>
                    <div class="stat-label">Active Alerts</div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="chart-card">
                    <h3>Student Performance Overview</h3>
                    <canvas id="performanceChart" width="400" height="200"></canvas>
                </div>
                
                <div class="at-risk-students">
                    <h3>At-Risk Students</h3>
                    <?php if (empty($at_risk_list)): ?>
                        <div class="student-item">
                            <div class="student-avatar">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="student-content">
                                <div class="student-name">No at-risk students</div>
                                <div class="student-course">All students are performing well</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($at_risk_list as $student): ?>
                            <div class="student-item">
                                <div class="student-avatar">
                                    <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                                </div>
                                <div class="student-content">
                                    <div class="student-name"><?php echo htmlspecialchars($student['name']); ?></div>
                                    <div class="student-course"><?php echo htmlspecialchars($student['course_name']); ?></div>
                                    <div class="risk-badge risk-<?php echo $student['risk_level']; ?>">
                                        <?php echo ucfirst($student['risk_level']); ?> Risk
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="quick-actions">
                <div class="action-card" onclick="window.location.href='my-courses.php'">
                    <div class="action-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="action-title">My Courses</div>
                    <div class="action-desc">View and manage your courses</div>
                </div>
                
                <div class="action-card" onclick="window.location.href='student-predictions.php'">
                    <div class="action-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="action-title">Student Predictions</div>
                    <div class="action-desc">View performance predictions</div>
                </div>
                
                <div class="action-card" onclick="window.location.href='alerts.php'">
                    <div class="action-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="action-title">View Alerts</div>
                    <div class="action-desc">Check student alerts and notifications</div>
                </div>
                
                <div class="action-card" onclick="window.location.href='discussion-forum.php'">
                    <div class="action-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="action-title">Discussion Forum</div>
                    <div class="action-desc">Discuss with other educators</div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Performance Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Excellent (A)', 'Good (B)', 'Average (C)', 'Below Average (D)', 'Failing (F)'],
                datasets: [{
                    data: [25, 35, 20, 15, 5],
                    backgroundColor: [
                        '#28a745',
                        '#2c5aa0',
                        '#ffc107',
                        '#fd7e14',
                        '#dc3545'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>
