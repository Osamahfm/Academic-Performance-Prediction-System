<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Creative Project</title>
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
                    <i class="fas fa-rocket"></i>
                    <span>CreativeProject</span>
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
                            <i class="fas fa-user"></i>
                            <span>About</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="services.php" class="nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">
                            <i class="fas fa-cogs"></i>
                            <span>Services</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="portfolio.php" class="nav-link <?php echo ($current_page == 'portfolio.php') ? 'active' : ''; ?>">
                            <i class="fas fa-briefcase"></i>
                            <span>Portfolio</span>
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

    <!-- Services Hero Section -->
    <section class="services-hero">
        <div class="container">
            <div class="services-content">
                <h1 class="services-title">Our Services</h1>
                <p class="services-description">
                    We offer comprehensive web development and design services to help your business grow online.
                </p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Web Development</h3>
                    <p>Custom web applications built with modern technologies and best practices.</p>
                    <ul>
                        <li>Frontend Development</li>
                        <li>Backend Development</li>
                        <li>Database Design</li>
                        <li>API Integration</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                    <h3>Web Design</h3>
                    <p>Beautiful, user-friendly designs that convert visitors into customers.</p>
                    <ul>
                        <li>UI/UX Design</li>
                        <li>Responsive Design</li>
                        <li>Brand Identity</li>
                        <li>Prototyping</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Apps</h3>
                    <p>Native and cross-platform mobile applications for iOS and Android.</p>
                    <ul>
                        <li>iOS Development</li>
                        <li>Android Development</li>
                        <li>React Native</li>
                        <li>App Store Optimization</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>SEO Services</h3>
                    <p>Improve your search engine rankings and drive more organic traffic.</p>
                    <ul>
                        <li>Keyword Research</li>
                        <li>On-Page SEO</li>
                        <li>Technical SEO</li>
                        <li>Content Marketing</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Digital Marketing</h3>
                    <p>Comprehensive digital marketing strategies to grow your online presence.</p>
                    <ul>
                        <li>Social Media Marketing</li>
                        <li>Google Ads</li>
                        <li>Email Marketing</li>
                        <li>Analytics & Reporting</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Consulting</h3>
                    <p>Expert advice and guidance for your digital transformation journey.</p>
                    <ul>
                        <li>Technology Consulting</li>
                        <li>Digital Strategy</li>
                        <li>Performance Optimization</li>
                        <li>Security Audits</li>
                    </ul>
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
