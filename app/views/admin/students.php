<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - EduPredict</title>
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
            max-width: 1400px;
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
        .btn-primary:hover {
            background: #1e3a8a;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
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
        .badge-low {
            background: #28a745;
            color: white;
        }
        .badge-medium {
            background: #ffc107;
            color: #333;
        }
        .badge-high {
            background: #dc3545;
            color: white;
        }
        .stats-bar {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .stat-item {
            flex: 1;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c5aa0;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .search-box input {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-graduate"></i> Manage Students</h1>
            <div>
                <a href="/projecty/public/index.php?controller=dashboard&action=admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="/projecty/public/enrollment" class="btn btn-primary">
                    <i class="fas fa-user-graduate"></i> Manage Enrollments
                </a>
                <a href="/projecty/utilities/manage-student-gpa.php" class="btn btn-success">
                    <i class="fas fa-chart-line"></i> Manage GPA
                </a>
                <a href="/projecty/utilities/train-knn-model.php" class="btn btn-success">
                    <i class="fas fa-brain"></i> Train KNN
                </a>
            </div>
        </div>

        <?php if (!empty($items)): ?>
            <div class="stats-bar">
                <div class="stat-item">
                    <div class="stat-value"><?php echo count($items); ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['with_grades'] ?? 0; ?></div>
                    <div class="stat-label">With Grades</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $stats['at_risk'] ?? 0; ?></div>
                    <div class="stat-label">At Risk</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo number_format($stats['avg_gpa'] ?? 0, 2); ?></div>
                    <div class="stat-label">Average GPA</div>
                </div>
            </div>

            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by name, student ID, or email..." onkeyup="filterTable()">
            </div>

            <table id="studentsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>GPA</th>
                        <th>Attendance</th>
                        <th>Risk Level</th>
                        <th>Grades</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['student_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></td>
                            <td><?php echo $student['gpa'] !== null ? number_format((float)$student['gpa'], 2) : 'N/A'; ?></td>
                            <td><?php echo $student['attendance_rate'] !== null ? number_format((float)$student['attendance_rate'], 1) . '%' : 'N/A'; ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($student['risk_level'] ?? 'low'); ?>">
                                    <?php echo ucfirst($student['risk_level'] ?? 'Low'); ?>
                                </span>
                            </td>
                            <td><?php echo $student['grade_count'] ?? 0; ?></td>
                            <td>
                                <a href="/projecty/public/index.php?controller=prediction&action=predictStudent&student_id=<?php echo $student['id']; ?>" 
                                   class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;" title="View Prediction">
                                    <i class="fas fa-brain"></i>
                                </a>
                                <a href="/projecty/public/index.php?controller=crud&action=index&entity=grade&student_id=<?php echo $student['id']; ?>" 
                                   class="btn btn-success" style="padding: 5px 10px; font-size: 12px;" title="View Grades">
                                    <i class="fas fa-graduation-cap"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-user-graduate" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                <h2>No Students Found</h2>
                <p>There are no students in the database.</p>
                <a href="/projecty/public/index.php?controller=crud&action=index&entity=user" class="btn btn-primary" style="margin-top: 20px;">
                    <i class="fas fa-plus"></i> Add Users
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('studentsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html>



