<?php
/**
 * Dynamic Menu Component
 * Loads menu items from database based on user role
 */
use App\Models\MenuModel;

$menuModel = new MenuModel();
$role = $_SESSION['role'] ?? null;
$menuItems = $menuModel->getMenuByRole($role);

function renderMenuItems($items, $currentPage = '') {
    foreach ($items as $item) {
        $hasChildren = !empty($item['children']);
        $isActive = ($currentPage === $item['url'] || strpos($_SERVER['REQUEST_URI'], $item['url']) !== false);
        
        echo '<li class="nav-item' . ($hasChildren ? ' has-dropdown' : '') . '">';
        echo '<a href="' . htmlspecialchars($item['url']) . '" class="nav-link' . ($isActive ? ' active' : '') . '">';
        
        if (!empty($item['icon'])) {
            echo '<i class="' . htmlspecialchars($item['icon']) . '"></i> ';
        }
        
        echo '<span>' . htmlspecialchars($item['title']) . '</span>';
        echo '</a>';
        
        if ($hasChildren) {
            echo '<ul class="dropdown-menu">';
            renderMenuItems($item['children'], $currentPage);
            echo '</ul>';
        }
        
        echo '</li>';
    }
}
?>

<div class="nav-menu" id="nav-menu">
    <ul class="nav-list">
        <?php renderMenuItems($menuItems, $current_page ?? ''); ?>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
                <a href="/projecty/public/index.php?controller=dashboard&action=index" class="nav-link nav-cta">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/projecty/public/index.php?controller=auth&action=logout" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a href="/projecty/public/index.php?controller=auth&action=login" class="nav-link nav-cta">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>




