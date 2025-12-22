-- Menu Items Table Migration
-- Self-referencing structure for dynamic menu system

CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50),
    role ENUM('admin', 'instructor', 'student', 'public') DEFAULT 'public',
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    INDEX idx_role_status (role, status),
    INDEX idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default menu items
INSERT INTO menu_items (title, url, icon, role, parent_id, sort_order, status) VALUES
-- Public menu items
('Home', '/projecty/public/index.php?controller=home&action=index', 'fas fa-home', 'public', NULL, 1, 'active'),
('About', '/projecty/public/index.php?controller=page&action=about', 'fas fa-info-circle', 'public', NULL, 2, 'active'),
('Services', '/projecty/public/index.php?controller=page&action=services', 'fas fa-chart-line', 'public', NULL, 3, 'active'),
('Portfolio', '/projecty/public/index.php?controller=page&action=portfolio', 'fas fa-chart-bar', 'public', NULL, 4, 'active'),
('Contact', '/projecty/public/index.php?controller=contact&action=index', 'fas fa-envelope', 'public', NULL, 5, 'active'),

-- Admin menu items
('Dashboard', '/projecty/public/index.php?controller=dashboard&action=admin', 'fas fa-tachometer-alt', 'admin', NULL, 1, 'active'),
('Users', '/projecty/public/index.php?controller=crud&action=index&entity=user', 'fas fa-users', 'admin', NULL, 2, 'active'),
('Courses', '/projecty/public/index.php?controller=crud&action=index&entity=course', 'fas fa-book', 'admin', NULL, 3, 'active'),
('Grades', '/projecty/public/index.php?controller=crud&action=index&entity=grade', 'fas fa-graduation-cap', 'admin', NULL, 4, 'active'),
('Menu Management', '/projecty/public/index.php?controller=menu&action=index', 'fas fa-bars', 'admin', NULL, 5, 'active'),

-- Instructor menu items
('Dashboard', '/projecty/public/index.php?controller=dashboard&action=instructor', 'fas fa-tachometer-alt', 'instructor', NULL, 1, 'active'),
('My Courses', '/projecty/public/index.php?controller=course&action=myCourses', 'fas fa-book-open', 'instructor', NULL, 2, 'active'),
('Students', '/projecty/public/index.php?controller=student&action=index', 'fas fa-user-graduate', 'instructor', NULL, 3, 'active'),
('Grades', '/projecty/public/index.php?controller=grade&action=index', 'fas fa-graduation-cap', 'instructor', NULL, 4, 'active'),

-- Student menu items
('Dashboard', '/projecty/public/index.php?controller=dashboard&action=student', 'fas fa-tachometer-alt', 'student', NULL, 1, 'active'),
('My Grades', '/projecty/public/index.php?controller=grade&action=myGrades', 'fas fa-chart-line', 'student', NULL, 2, 'active'),
('Courses', '/projecty/public/index.php?controller=course&action=myCourses', 'fas fa-book', 'student', NULL, 3, 'active'),
('Performance', '/projecty/public/index.php?controller=student&action=performance', 'fas fa-chart-bar', 'student', NULL, 4, 'active');

