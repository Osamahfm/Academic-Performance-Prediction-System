<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades - Instructor - EduPredict</title>
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
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .course-selector {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .course-selector label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }
        .course-selector select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background: white;
        }
        .course-selector select:focus {
            outline: none;
            border-color: #2c5aa0;
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
        .actions {
            display: flex;
            gap: 5px;
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
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2c5aa0;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            overflow-y: auto;
        }
        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            position: relative;
        }
        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }
        .close:hover {
            color: #333;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2c5aa0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box p {
            margin: 0;
            color: #333;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .empty-state i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Manage Grades</h1>
            <div>
                <a href="/projecty/public/dashboard/instructor" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <?php if ($selectedCourseId): ?>
                <button onclick="openModal('addModal')" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Grade
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Course Selector -->
        <div class="course-selector">
            <label for="courseSelect"><i class="fas fa-book"></i> Select Course</label>
            <select id="courseSelect" onchange="selectCourse()">
                <option value="">-- Select a course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['id']; ?>" 
                            <?php echo ($selectedCourseId == $course['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (empty($courses)): ?>
            <div class="info-box" style="background: #fff3cd; border-left-color: #ffc107;">
                <p><i class="fas fa-exclamation-triangle"></i> <strong>No courses found!</strong></p>
                <p>Your instructor ID: <?php echo htmlspecialchars($_SESSION['user_id'] ?? 'Not set'); ?></p>
                <p>You don't have any courses assigned yet. This could mean:</p>
                <ul style="margin: 10px 0 10px 20px;">
                    <li>Courses exist but aren't assigned to your instructor account</li>
                    <li>No courses have been created in the system</li>
                </ul>
                <p><strong>Solution:</strong> Use the utility below to assign courses to your instructor account.</p>
                <p style="margin-top: 15px;">
                    <a href="/projecty/utilities/assign-courses-to-instructor.php" class="btn btn-success" style="text-decoration: none; display: inline-block; margin-right: 10px;">
                        <i class="fas fa-link"></i> Assign Courses to Me
                    </a>
                    <a href="/projecty/public/index.php?controller=crud&action=index&entity=course" class="btn btn-primary" style="text-decoration: none; display: inline-block;">
                        <i class="fas fa-cog"></i> Manage Courses (Admin)
                    </a>
                </p>
            </div>
        <?php elseif (!$selectedCourseId): ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h3>Select a Course</h3>
                <p>Please select a course from the dropdown above to manage grades.</p>
            </div>
        <?php else: ?>
            <?php if ($selectedCourse): ?>
                <div class="info-box">
                    <p><strong>Course:</strong> <?php echo htmlspecialchars($selectedCourse['course_code'] . ' - ' . $selectedCourse['course_name']); ?></p>
                    <p><strong>Enrolled Students:</strong> <?php echo count($enrolledStudents); ?></p>
                </div>
            <?php endif; ?>

            <?php if (empty($enrolledStudents)): ?>
                <div class="info-box">
                    <p><i class="fas fa-info-circle"></i> No students are currently enrolled in this course.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolledStudents as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['student_email']); ?></td>
                                <td>
                                    <button onclick="addGradeForStudent(<?php echo htmlspecialchars(json_encode($student)); ?>)" 
                                            class="btn btn-success" style="padding: 5px 10px;">
                                        <i class="fas fa-plus"></i> Add Grade
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- Grades Table -->
            <?php if (!empty($grades)): ?>
                <h2 style="margin-top: 40px; margin-bottom: 20px; color: #333;">
                    <i class="fas fa-list"></i> Existing Grades
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Assignment Type</th>
                            <th>Grade</th>
                            <th>Max Grade</th>
                            <th>Percentage</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($grade['student_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($grade['assignment_type'] ?? 'N/A')); ?></td>
                                <td><?php echo htmlspecialchars($grade['grade'] ?? '0'); ?></td>
                                <td><?php echo htmlspecialchars($grade['max_grade'] ?? '100'); ?></td>
                                <td>
                                    <?php 
                                    $percentage = $grade['max_grade'] > 0 
                                        ? round(($grade['grade'] / $grade['max_grade']) * 100, 1) 
                                        : 0;
                                    echo $percentage . '%';
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($grade['date_recorded'] ?? 'N/A'); ?></td>
                                <td class="actions">
                                    <button onclick="editGrade(<?php echo htmlspecialchars(json_encode($grade)); ?>)" 
                                            class="btn btn-primary" style="padding: 5px 10px;">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteGrade(<?php echo $grade['id']; ?>)" 
                                            class="btn btn-danger" style="padding: 5px 10px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state" style="margin-top: 30px;">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No Grades Yet</h3>
                    <p>Start adding grades for your students using the "Add Grade" button above.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Add/Edit Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addModal')">&times;</span>
            <h2 id="modalTitle">Add Grade</h2>
            <form id="gradeForm" onsubmit="saveGrade(event)">
                <input type="hidden" id="gradeId" name="id">
                <input type="hidden" id="gradeCourseId" name="course_id" value="<?php echo htmlspecialchars($selectedCourseId ?? ''); ?>">
                
                <div class="form-group">
                    <label>Student *</label>
                    <select id="gradeStudent" name="student_id" required>
                        <option value="">-- Select Student --</option>
                        <?php if (!empty($enrolledStudents)): ?>
                            <?php foreach ($enrolledStudents as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['student_name'] . ' (' . $student['student_id'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Assignment Type *</label>
                    <select id="gradeType" name="assignment_type" required>
                        <option value="quiz">Quiz</option>
                        <option value="exam">Exam</option>
                        <option value="assignment">Assignment</option>
                        <option value="project">Project</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Grade *</label>
                    <input type="number" id="gradeScore" name="grade" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>Max Grade *</label>
                    <input type="number" id="gradeMax" name="max_grade" step="0.01" min="0" value="100" required>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success" style="flex: 1;">Save</button>
                    <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectCourse() {
            const courseId = document.getElementById('courseSelect').value;
            if (courseId) {
                window.location.href = '/projecty/public/grade/manage?course_id=' + courseId;
            } else {
                window.location.href = '/projecty/public/grade/manage';
            }
        }

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            document.getElementById('gradeForm').reset();
            document.getElementById('gradeId').value = '';
            document.getElementById('modalTitle').textContent = 'Add Grade';
            document.getElementById('gradeCourseId').value = '<?php echo htmlspecialchars($selectedCourseId ?? ''); ?>';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function addGradeForStudent(student) {
            openModal('addModal');
            document.getElementById('gradeStudent').value = student.id;
        }

        function editGrade(grade) {
            document.getElementById('addModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit Grade';
            document.getElementById('gradeId').value = grade.id;
            document.getElementById('gradeStudent').value = grade.student_id || '';
            document.getElementById('gradeCourseId').value = grade.course_id || '';
            document.getElementById('gradeType').value = grade.assignment_type || 'quiz';
            document.getElementById('gradeScore').value = grade.grade || '';
            document.getElementById('gradeMax').value = grade.max_grade || '';
        }

        function saveGrade(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const gradeId = formData.get('id');
            const url = gradeId 
                ? '/projecty/public/grade/update'
                : '/projecty/public/grade/create';

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Grade saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function deleteGrade(id) {
            if (!confirm('Are you sure you want to delete this grade?')) return;

            fetch('/projecty/public/grade/delete?id=' + id, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Grade deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        window.onclick = function(event) {
            const modal = document.getElementById('addModal');
            if (event.target == modal) {
                closeModal('addModal');
            }
        }
    </script>
</body>
</html>

