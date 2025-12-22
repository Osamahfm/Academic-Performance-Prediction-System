<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Enrollments - EduPredict</title>
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
        .btn-primary { background: #2c5aa0; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }
        
        .enrollment-section {
            margin-bottom: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .enrollment-section h2 {
            margin-top: 0;
            color: #2c5aa0;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group select[multiple] {
            min-height: 150px;
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
        .badge-active {
            background: #28a745;
            color: white;
        }
        .badge-completed {
            background: #6c757d;
            color: white;
        }
        .badge-dropped {
            background: #dc3545;
            color: white;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2c5aa0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-graduate"></i> Manage Student Enrollments</h1>
            <div>
                <a href="/projecty/public/dashboard/admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <div class="info-box">
            <p><strong>📚 Enrollment Management</strong></p>
            <p>Enroll students in courses so instructors can add grades. Students must be enrolled before instructors can assign grades.</p>
        </div>

        <!-- Quick Enroll Section -->
        <div class="enrollment-section">
            <h2><i class="fas fa-plus-circle"></i> Quick Enroll</h2>
            <form id="quickEnrollForm" onsubmit="quickEnroll(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label>Select Student *</label>
                        <select id="quickStudent" name="student_id" required>
                            <option value="">-- Select Student --</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['name'] . ' (' . $student['student_id'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Course *</label>
                        <select id="quickCourse" name="course_id" required>
                            <option value="">-- Select Course --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>">
                                    <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                                    <?php if ($course['instructor_name']): ?>
                                        (Instructor: <?php echo htmlspecialchars($course['instructor_name']); ?>)
                                    <?php else: ?>
                                        <span style="color: #dc3545;">(No Instructor)</span>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> Enroll Student
                </button>
            </form>
        </div>

        <!-- Bulk Enroll Section -->
        <div class="enrollment-section">
            <h2><i class="fas fa-users"></i> Bulk Enroll</h2>
            <form id="bulkEnrollForm" onsubmit="bulkEnroll(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label>Select Students * (Hold Ctrl/Cmd to select multiple)</label>
                        <select id="bulkStudents" name="student_ids[]" multiple required>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['name'] . ' (' . $student['student_id'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Courses * (Hold Ctrl/Cmd to select multiple)</label>
                        <select id="bulkCourses" name="course_ids[]" multiple required>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>">
                                    <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-double"></i> Bulk Enroll
                </button>
            </form>
        </div>

        <!-- Current Enrollments -->
        <div class="enrollment-section">
            <h2><i class="fas fa-list"></i> Current Enrollments</h2>
            <?php if (!empty($enrollments)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Enrollment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($enrollment['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($enrollment['course_code'] . ' - ' . $enrollment['course_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($enrollment['status']); ?>">
                                        <?php echo ucfirst($enrollment['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo isset($enrollment['enrollment_date']) ? date('M j, Y', strtotime($enrollment['enrollment_date'])) : 'N/A'; ?></td>
                                <td>
                                    <button onclick="unenroll(<?php echo $enrollment['id']; ?>)" 
                                            class="btn btn-danger" style="padding: 5px 10px;">
                                        <i class="fas fa-times"></i> Unenroll
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 20px; color: #666;">
                    <i class="fas fa-info-circle"></i> No enrollments found. Start enrolling students in courses above.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function quickEnroll(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            fetch('/projecty/public/enrollment/enroll', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Student enrolled successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function bulkEnroll(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            fetch('/projecty/public/enrollment/bulkEnroll', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Students enrolled successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function unenroll(enrollmentId) {
            if (!confirm('Are you sure you want to unenroll this student from the course?')) return;
            
            const formData = new FormData();
            formData.append('enrollment_id', enrollmentId);
            
            fetch('/projecty/public/enrollment/unenroll', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Student unenrolled successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
    </script>
</body>
</html>





