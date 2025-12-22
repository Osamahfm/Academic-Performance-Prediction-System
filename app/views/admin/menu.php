<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - EduPredict</title>
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
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
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
        .badge-inactive {
            background: #dc3545;
            color: white;
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
        .badge-public {
            background: #6c757d;
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
        .form-group small {
            color: #666;
            font-size: 12px;
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
            max-width: 600px;
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
        .menu-tree {
            margin-left: 20px;
        }
        .menu-item-row {
            display: flex;
            align-items: center;
        }
        .menu-indent {
            width: 30px;
            display: inline-block;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-bars"></i> Menu Management</h1>
            <div>
                <a href="/projecty/public/index.php?controller=dashboard&action=admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <button onclick="openModal('addModal')" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Menu Item
                </button>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Operation completed successfully!
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> Please fix the following errors:
                <ul style="margin: 10px 0 0 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>URL</th>
                    <th>Icon</th>
                    <th>Role</th>
                    <th>Parent</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($menuItems)): ?>
                    <?php 
                    // Build hierarchical structure for display
                    function buildMenuTree($items, $parentId = null, $level = 0) {
                        $tree = [];
                        foreach ($items as $item) {
                            $itemParentId = $item['parent_id'] ?? null;
                            if ($itemParentId == $parentId || ($itemParentId === null && $parentId === null)) {
                                $item['level'] = $level;
                                $tree[] = $item;
                                $children = buildMenuTree($items, $item['id'], $level + 1);
                                $tree = array_merge($tree, $children);
                            }
                        }
                        return $tree;
                    }
                    $treeItems = buildMenuTree($menuItems);
                    ?>
                    <?php foreach ($treeItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['id']); ?></td>
                            <td>
                                <span style="margin-left: <?php echo ($item['level'] ?? 0) * 20; ?>px;">
                                    <?php if (($item['level'] ?? 0) > 0): ?>
                                        <i class="fas fa-arrow-right" style="color: #999; margin-right: 5px;"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($item['url']); ?></td>
                            <td>
                                <?php if ($item['icon']): ?>
                                    <i class="<?php echo htmlspecialchars($item['icon']); ?>"></i>
                                <?php else: ?>
                                    <span style="color: #999;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($item['role'] ?? 'public'); ?>">
                                    <?php echo ucfirst($item['role'] ?? 'public'); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $parentId = $item['parent_id'] ?? null;
                                if ($parentId) {
                                    foreach ($menuItems as $parent) {
                                        if ($parent['id'] == $parentId) {
                                            echo htmlspecialchars($parent['title']);
                                            break;
                                        }
                                    }
                                } else {
                                    echo '<span style="color: #999;">-</span>';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['sort_order'] ?? 0); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($item['status'] ?? 'active'); ?>">
                                    <?php echo ucfirst($item['status'] ?? 'active'); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <button onclick="editMenuItem(<?php echo htmlspecialchars(json_encode($item)); ?>)" class="btn btn-primary" style="padding: 5px 10px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteMenuItem(<?php echo $item['id']; ?>)" class="btn btn-danger" style="padding: 5px 10px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 30px;">
                            <p>No menu items found. <a href="#" onclick="openModal('addModal'); return false;">Add your first menu item</a></p>
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
            <h2 id="modalTitle">Add Menu Item</h2>
            <form id="menuForm" method="POST" action="/projecty/public/index.php?controller=menu&action=create">
                <input type="hidden" id="menuId" name="id">
                <input type="hidden" name="action" id="formAction" value="create">
                
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" id="menuTitle" name="title" required placeholder="e.g., Home, About, Dashboard">
                </div>
                
                <div class="form-group">
                    <label>URL *</label>
                    <input type="text" id="menuUrl" name="url" required placeholder="e.g., /projecty/public/index.php?controller=home&action=index">
                    <small>Full URL path or relative path</small>
                </div>
                
                <div class="form-group">
                    <label>Icon</label>
                    <input type="text" id="menuIcon" name="icon" placeholder="e.g., fas fa-home, fas fa-user">
                    <small>Font Awesome icon class (e.g., fas fa-home)</small>
                </div>
                
                <div class="form-group">
                    <label>Role *</label>
                    <select id="menuRole" name="role" required>
                        <option value="public">Public (Everyone)</option>
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                    <small>Who can see this menu item</small>
                </div>
                
                <div class="form-group">
                    <label>Parent Menu</label>
                    <select id="menuParent" name="parent_id">
                        <option value="">None (Top Level)</option>
                        <?php if (!empty($menuItems)): ?>
                            <?php foreach ($menuItems as $parent): ?>
                                <?php if (empty($parent['parent_id'])): ?>
                                    <option value="<?php echo $parent['id']; ?>"><?php echo htmlspecialchars($parent['title']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small>Select parent menu item for submenu</small>
                </div>
                
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" id="menuOrder" name="sort_order" value="0" min="0">
                    <small>Lower numbers appear first</small>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <select id="menuStatus" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success" style="flex: 1;">
                        <i class="fas fa-save"></i> Save
                    </button>
                    <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary" style="flex: 1;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            document.getElementById('menuForm').reset();
            document.getElementById('menuId').value = '';
            document.getElementById('formAction').value = 'create';
            document.getElementById('modalTitle').textContent = 'Add Menu Item';
            document.getElementById('menuForm').action = '/projecty/public/index.php?controller=menu&action=create';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editMenuItem(item) {
            document.getElementById('addModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = 'Edit Menu Item';
            document.getElementById('menuId').value = item.id;
            document.getElementById('menuTitle').value = item.title || '';
            document.getElementById('menuUrl').value = item.url || '';
            document.getElementById('menuIcon').value = item.icon || '';
            document.getElementById('menuRole').value = item.role || 'public';
            document.getElementById('menuParent').value = (item.parent_id && item.parent_id !== null) ? item.parent_id : '';
            document.getElementById('menuOrder').value = item.sort_order || 0;
            document.getElementById('menuStatus').value = item.status || 'active';
            document.getElementById('formAction').value = 'update';
            document.getElementById('menuForm').action = '/projecty/public/index.php?controller=menu&action=update&id=' + item.id;
        }

        function deleteMenuItem(id) {
            if (!confirm('Are you sure you want to delete this menu item? This will also delete all submenu items.')) return;
            
            window.location.href = '/projecty/public/index.php?controller=menu&action=delete&id=' + id;
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

