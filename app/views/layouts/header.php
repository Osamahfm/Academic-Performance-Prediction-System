<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'EduPredict - Academic Performance Prediction System'; ?></title>
    <link rel="stylesheet" href="/projecty/public/assets/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Responsive Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="/projecty/public/index.php?controller=home&action=index">
                    <i class="fas fa-graduation-cap"></i>
                    <span>EduPredict</span>
                </a>
            </div>
            
            <div class="nav-menu" id="nav-menu">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="/projecty/public/index.php?controller=home&action=index" 
                           class="nav-link <?php echo ($current_page == 'index') ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/projecty/public/index.php?controller=page&action=about" 
                           class="nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>">
                            <i class="fas fa-info-circle"></i>
                            <span>About System</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/projecty/public/index.php?controller=page&action=services" 
                           class="nav-link <?php echo ($current_page == 'services') ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i>
                            <span>Prediction Models</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/projecty/public/index.php?controller=page&action=portfolio" 
                           class="nav-link <?php echo ($current_page == 'portfolio') ? 'active' : ''; ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/projecty/public/index.php?controller=contact&action=index" 
                           class="nav-link <?php echo ($current_page == 'contact') ? 'active' : ''; ?>">
                            <i class="fas fa-envelope"></i>
                            <span>Contact</span>
                        </a>
                    </li>
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
            
            <div class="nav-toggle" id="nav-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>





