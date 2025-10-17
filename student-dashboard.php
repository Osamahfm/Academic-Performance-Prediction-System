<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

$user_name = $_SESSION['name'];
$user_email = $_SESSION['email'];
$user_id = $_SESSION['user_id'];

// Get student data
try {
    $pdo = getDBConnection();
    
    // Get student info
    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get enrolled courses
    $stmt = $pdo->prepare("
        SELECT c.course_name, c.course_code, e.enrollment_date
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        WHERE e.student_id = ? AND e.status = 'active'
    ");
    $stmt->execute([$student['id'] ?? 0]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent grades
    $stmt = $pdo->prepare("
        SELECT g.grade, g.max_grade, g.assignment_type, c.course_name, g.date_recorded
        FROM grades g 
        JOIN courses c ON g.course_id = c.id 
        WHERE g.student_id = ? 
        ORDER BY g.date_recorded DESC 
        LIMIT 5
    ");
    $stmt->execute([$student['id'] ?? 0]);
    $recent_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $student = null;
    $courses = [];
    $recent_grades = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - EduPredict</title>
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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .chart-card h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 1.3rem;
        }
        
        .recent-grades {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .recent-grades h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 1.3rem;
        }
        
        .grade-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .grade-item:last-child {
            border-bottom: none;
        }
        
        .grade-icon {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #2c5aa0;
        }
        
        .grade-content {
            flex: 1;
        }
        
        .grade-course {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }
        
        .grade-details {
            color: #666;
            font-size: 0.9rem;
        }
        
        .grade-score {
            font-weight: 600;
            color: #2c5aa0;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <i class="fas fa-user-graduate"></i>
                    <h1>Student Dashboard</h1>
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
                    <div class="stat-number"><?php echo count($courses); ?></div>
                    <div class="stat-label">Enrolled Courses</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number"><?php echo $student['gpa'] ?? '0.00'; ?></div>
                    <div class="stat-label">Current GPA</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number"><?php echo $student['attendance_rate'] ?? '0'; ?>%</div>
                    <div class="stat-label">Attendance Rate</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="stat-number"><?php echo ucfirst($student['risk_level'] ?? 'Low'); ?></div>
                    <div class="stat-label">Risk Level</div>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <div class="chart-card">
                    <h3>Performance Overview</h3>
                    <canvas id="performanceChart" width="400" height="200"></canvas>
                </div>
                
                <div class="recent-grades">
                    <h3>Recent Grades</h3>
                    <?php if (empty($recent_grades)): ?>
                        <div class="grade-item">
                            <div class="grade-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="grade-content">
                                <div class="grade-course">No grades recorded yet</div>
                                <div class="grade-details">Check back after assignments are graded</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_grades as $grade): ?>
                            <div class="grade-item">
                                <div class="grade-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="grade-content">
                                    <div class="grade-course"><?php echo htmlspecialchars($grade['course_name']); ?></div>
                                    <div class="grade-details">
                                        <?php echo ucfirst($grade['assignment_type']); ?> - 
                                        <span class="grade-score"><?php echo $grade['grade']; ?>/<?php echo $grade['max_grade']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                    data: [30, 40, 20, 8, 2],
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





