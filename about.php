<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About System - EduPredict Academic Performance Prediction</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Responsive Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">
                    <i class="fas fa-graduation-cap"></i>
                    <span>EduPredict</span>
                </a>
            </div>
            
            <div class="nav-menu" id="nav-menu">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="about.php" class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">
                            <i class="fas fa-info-circle"></i>
                            <span>About System</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="services.php" class="nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i>
                            <span>Prediction Models</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="portfolio.php" class="nav-link <?php echo ($current_page == 'portfolio.php') ? 'active' : ''; ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="contact.php" class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
                            <i class="fas fa-envelope"></i>
                            <span>Contact</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-toggle" id="nav-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <!-- About Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="about-content">
                <h1 class="about-title">About EduPredict System</h1>
                <p class="about-description">
                    An advanced AI-powered academic performance prediction system designed to help 
                    educational institutions identify at-risk students and improve learning outcomes.
                </p>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-text">
                    <h2>System Overview</h2>
                    <p>
                        EduPredict is a comprehensive academic performance prediction system that leverages 
                        machine learning algorithms and educational data analytics to provide accurate 
                        predictions of student academic performance.
                    </p>
                    <p>
                        Our system analyzes various factors including attendance, assignment submissions, 
                        quiz scores, and engagement metrics to identify students who may be at risk of 
                        academic failure, enabling early intervention and support.
                    </p>
                    <p>
                        The system is designed for educational institutions, teachers, and administrators 
                        who want to improve student success rates and optimize educational resources.
                    </p>
                </div>
                <div class="about-stats">
                    <div class="stat-item">
                        <h3>95%</h3>
                        <p>Prediction Accuracy</p>
                    </div>
                    <div class="stat-item">
                        <h3>50+</h3>
                        <p>Institutions Served</p>
                    </div>
                    <div class="stat-item">
                        <h3>10K+</h3>
                        <p>Students Analyzed</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-rocket"></i>
                        <span>CreativeProject</span>
                    </div>
                    <p class="footer-description">
                        Creating amazing web experiences with modern technology and creative design.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="portfolio.php">Portfolio</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Services</h3>
                    <ul class="footer-links">
                        <li><a href="#">Web Design</a></li>
                        <li><a href="#">Web Development</a></li>
                        <li><a href="#">Mobile Apps</a></li>
                        <li><a href="#">SEO Services</a></li>
                        <li><a href="#">Consulting</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Contact Info</h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>hello@creativeproject.com</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+1 (555) 123-4567</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Creative Street, Design City</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 CreativeProject. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
