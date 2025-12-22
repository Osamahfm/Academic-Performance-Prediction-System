<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - EduPredict</title>
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
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
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
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
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
        }
    </style>
</head>
<body>
    <?php
    // Determine current user role and id for instructor-specific behavior
    $currentRole = $_SESSION['role'] ?? 'admin';
    $currentUserId = $_SESSION['user_id'] ?? null;
    ?>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-book"></i> Manage Courses</h1>
            <div>
                <a href="/projecty/public/index.php?controller=dashboard&action=<?php echo $currentRole === 'instructor' ? 'instructor' : 'admin'; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <button onclick="openModal('addModal')" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Course
                </button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Instructor</th>
                    <th>Credits</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $course): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['id']); ?></td>
                            <td><?php echo htmlspecialchars($course['course_code'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($course['course_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($course['instructor_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($course['credits'] ?? '0'); ?></td>
                            <td class="actions">
                                <button onclick="editCourse(<?php echo htmlspecialchars(json_encode($course)); ?>)" class="btn btn-primary" style="padding: 5px 10px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteCourse(<?php echo $course['id']; ?>)" class="btn btn-danger" style="padding: 5px 10px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px;">
                            <p>No courses found.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addModal')">&times;</span>
            <h2 id="modalTitle">Add Course</h2>
            <form id="courseForm" onsubmit="saveCourse(event)">
                <input type="hidden" id="courseId" name="id">
                <div class="form-group">
                    <label>Course Code *</label>
                    <input type="text" id="courseCode" name="course_code" required>
                </div>
                <div class="form-group">
                    <label>Course Name *</label>
                    <input type="text" id="courseName" name="course_name" required>
                </div>
                <div class="form-group">
                    <label>Credits *</label>
                    <input type="number" id="courseCredits" name="credits" min="1" max="6" required>
                </div>
                <?php if ($currentRole === 'admin'): ?>
                    <div class="form-group">
                        <label>Instructor ID</label>
                        <input type="number" id="courseInstructor" name="instructor_id">
                    </div>
                <?php else: ?>
                    <!-- For instructors, automatically assign themselves as the instructor -->
                    <input type="hidden" id="courseInstructor" name="instructor_id" value="<?php echo htmlspecialchars($currentUserId ?? ''); ?>">
                <?php endif; ?>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success" style="flex: 1;">Save</button>
                    <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            document.getElementById('courseForm').reset();
            document.getElementById('courseId').value = '';
            document.getElementById('modalTitle').textContent = 'Add Course';

            // If not admin, keep instructor_id fixed to current user
            <?php if ($currentRole !== 'admin'): ?>
            document.getElementById('courseInstructor').value = '<?php echo htmlspecialchars($currentUserId ?? ''); ?>';
            <?php endif; ?>
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editCourse(course) {
            document.getElementById('addModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit Course';
            document.getElementById('courseId').value = course.id;
            document.getElementById('courseCode').value = course.course_code || '';
            document.getElementById('courseName').value = course.course_name || '';
            document.getElementById('courseCredits').value = course.credits || '';
            // Only allow changing instructor_id if admin; instructors stay assigned to themselves
            <?php if ($currentRole === 'admin'): ?>
            document.getElementById('courseInstructor').value = course.instructor_id || '';
            <?php endif; ?>
        }

        function saveCourse(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const courseId = formData.get('id');
            const url = courseId 
                ? `/projecty/public/index.php?controller=crud&action=update&entity=course&id=${courseId}`
                : `/projecty/public/index.php?controller=crud&action=create&entity=course`;

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Course saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function deleteCourse(id) {
            if (!confirm('Are you sure you want to delete this course?')) return;

            fetch(`/projecty/public/index.php?controller=crud&action=delete&entity=course&id=${id}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Course deleted successfully!');
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




