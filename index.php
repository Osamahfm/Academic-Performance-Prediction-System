<?php
// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Performance Prediction System - AI-Powered Student Analytics</title>
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
                    <li class="nav-item">
                        <a href="login.php" class="nav-link nav-cta">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <span class="gradient-text">Academic</span> Performance
                    <br>Prediction System
                </h1>
                <p class="hero-description">
                    Harness the power of AI and machine learning to predict student academic performance, 
                    identify at-risk students, and improve educational outcomes.
                </p>
                <div class="hero-buttons">
                    <a href="contact.php" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Get Started
                    </a>
                    <a href="portfolio.php" class="btn btn-secondary">
                        <i class="fas fa-chart-bar"></i>
                        View Analytics
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <div class="floating-card">
                    <i class="fas fa-brain"></i>
                    <h3>AI Powered</h3>
                </div>
                <div class="floating-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Predictive</h3>
                </div>
                <div class="floating-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Educational</h3>
                </div>
            </div>
        </div>
        <div class="hero-background">
            <div class="bg-shape shape-1"></div>
            <div class="bg-shape shape-2"></div>
            <div class="bg-shape shape-3"></div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">System Features</h2>
                <p class="section-description">Advanced AI-powered tools for academic performance prediction and analysis</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="feature-title">AI-Powered Predictions</h3>
                    <p class="feature-description">
                        Advanced machine learning algorithms predict student performance with high accuracy.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="feature-title">Early Warning System</h3>
                    <p class="feature-description">
                        Identify at-risk students early to provide timely intervention and support.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Performance Analytics</h3>
                    <p class="feature-description">
                        Comprehensive analytics and insights into student learning patterns and trends.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3 class="feature-title">Data Integration</h3>
                    <p class="feature-description">
                        Seamlessly integrate with existing student information systems and databases.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3 class="feature-title">Personalized Insights</h3>
                    <p class="feature-description">
                        Generate personalized recommendations for individual students and classes.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Privacy Protected</h3>
                    <p class="feature-description">
                        Secure handling of student data with FERPA compliance and privacy protection.
                    </p>
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
                        <i class="fas fa-graduation-cap"></i>
                        <span>EduPredict</span>
                    </div>
                    <p class="footer-description">
                        AI-powered academic performance prediction system for educational institutions and educators.
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
                    <h3 class="footer-title">Features</h3>
                    <ul class="footer-links">
                        <li><a href="#">Performance Prediction</a></li>
                        <li><a href="#">Risk Assessment</a></li>
                        <li><a href="#">Analytics Dashboard</a></li>
                        <li><a href="#">Data Integration</a></li>
                        <li><a href="#">Reporting Tools</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Contact Info</h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>info@edupredict.edu</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+1 (555) 123-4567</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>456 Education Avenue, Academic City</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 EduPredict Academic Performance Prediction System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>