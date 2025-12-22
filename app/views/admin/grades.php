<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades - EduPredict</title>
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
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Manage Grades</h1>
            <div>
                <a href="/projecty/public/index.php?controller=dashboard&action=<?php echo $_SESSION['role'] ?? 'admin'; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <button onclick="openModal('addModal')" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Grade
                </button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Assignment</th>
                    <th>Grade</th>
                    <th>Max Grade</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $grade): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['id']); ?></td>
                            <td><?php echo htmlspecialchars($grade['student_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['course_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['assignment_type'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['grade'] ?? '0'); ?></td>
                            <td><?php echo htmlspecialchars($grade['max_grade'] ?? '100'); ?></td>
                            <td class="actions">
                                <button onclick="editGrade(<?php echo htmlspecialchars(json_encode($grade)); ?>)" class="btn btn-primary" style="padding: 5px 10px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteGrade(<?php echo $grade['id']; ?>)" class="btn btn-danger" style="padding: 5px 10px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">
                            <p>No grades found.</p>
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
            <h2 id="modalTitle">Add Grade</h2>
            <form id="gradeForm" onsubmit="saveGrade(event)">
                <input type="hidden" id="gradeId" name="id">
                <div class="form-group">
                    <label>Student ID *</label>
                    <input type="number" id="gradeStudent" name="student_id" required>
                </div>
                <div class="form-group">
                    <label>Course ID *</label>
                    <input type="number" id="gradeCourse" name="course_id" required>
                </div>
                <div class="form-group">
                    <label>Assignment Type *</label>
                    <select id="gradeType" name="assignment_type" required>
                        <option value="quiz">Quiz</option>
                        <option value="assignment">Assignment</option>
                        <option value="midterm">Midterm</option>
                        <option value="final">Final</option>
                        <option value="project">Project</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Grade *</label>
                    <input type="number" id="gradeScore" name="grade" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Max Grade *</label>
                    <input type="number" id="gradeMax" name="max_grade" step="0.01" min="0" required>
                </div>
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
            document.getElementById('gradeForm').reset();
            document.getElementById('gradeId').value = '';
            document.getElementById('modalTitle').textContent = 'Add Grade';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editGrade(grade) {
            document.getElementById('addModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit Grade';
            document.getElementById('gradeId').value = grade.id;
            document.getElementById('gradeStudent').value = grade.student_id || '';
            document.getElementById('gradeCourse').value = grade.course_id || '';
            document.getElementById('gradeType').value = grade.assignment_type || 'quiz';
            document.getElementById('gradeScore').value = grade.grade || '';
            document.getElementById('gradeMax').value = grade.max_grade || '';
        }

        function saveGrade(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const gradeId = formData.get('id');
            const url = gradeId 
                ? `/projecty/public/index.php?controller=crud&action=update&entity=grade&id=${gradeId}`
                : `/projecty/public/index.php?controller=crud&action=create&entity=grade`;

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

            fetch(`/projecty/public/index.php?controller=crud&action=delete&entity=grade&id=${id}`, {
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



