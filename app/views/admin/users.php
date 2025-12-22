<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - EduPredict</title>
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
        .badge-admin {
            background: #dc3545;
            color: white;
        }
        .badge-instructor {
            background: #2c5aa0;
            color: white;
        }
        .badge-student {
            background: #28a745;
            color: white;
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
            <h1><i class="fas fa-users"></i> Manage Users</h1>
            <div>
                <a href="/projecty/public/index.php?controller=dashboard&action=admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <button onclick="openModal('addModal')" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add User
                </button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($user['role'] ?? 'student'); ?>">
                                    <?php echo ucfirst($user['role'] ?? 'student'); ?>
                                </span>
                            </td>
                            <td><?php echo isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'N/A'; ?></td>
                            <td class="actions">
                                <button onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" class="btn btn-primary" style="padding: 5px 10px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="btn btn-danger" style="padding: 5px 10px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px;">
                            <p>No users found.</p>
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
            <h2 id="modalTitle">Add User</h2>
            <form id="userForm" onsubmit="saveUser(event)">
                <input type="hidden" id="userId" name="id">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" id="userName" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" id="userEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password <?php echo '<span id="passwordRequired">*</span>'; ?></label>
                    <input type="password" id="userPassword" name="password">
                    <small style="color: #666;">Leave blank to keep current password</small>
                </div>
                <div class="form-group">
                    <label>Role *</label>
                    <select id="userRole" name="role" required>
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
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
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('modalTitle').textContent = 'Add User';
            document.getElementById('passwordRequired').style.display = 'inline';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editUser(user) {
            document.getElementById('addModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = user.id;
            document.getElementById('userName').value = user.name || '';
            document.getElementById('userEmail').value = user.email || '';
            document.getElementById('userRole').value = user.role || 'student';
            document.getElementById('passwordRequired').style.display = 'none';
        }

        function saveUser(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const userId = formData.get('id');
            const url = userId 
                ? `/projecty/public/index.php?controller=crud&action=update&entity=user&id=${userId}`
                : `/projecty/public/index.php?controller=crud&action=create&entity=user`;

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) return;

            fetch(`/projecty/public/index.php?controller=crud&action=delete&entity=user&id=${id}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User deleted successfully!');
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



