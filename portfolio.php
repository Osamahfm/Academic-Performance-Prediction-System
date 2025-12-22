<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Creative Project</title>
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

    <!-- Portfolio Hero Section -->
    <section class="portfolio-hero">
        <div class="container">
            <div class="portfolio-content">
                <h1 class="portfolio-title">Our Portfolio</h1>
                <p class="portfolio-description">
                    Explore our latest projects and see how we've helped businesses achieve their goals.
                </p>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section class="portfolio-section">
        <div class="container">
            <div class="portfolio-filter">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <button class="filter-btn" data-filter="web">Web Design</button>
                <button class="filter-btn" data-filter="app">Mobile Apps</button>
                <button class="filter-btn" data-filter="branding">Branding</button>
            </div>
            
            <div class="portfolio-grid">
                <div class="portfolio-item" data-category="web">
                    <div class="portfolio-image">
                        <div class="portfolio-overlay">
                            <h3>E-Commerce Website</h3>
                            <p>Modern online store with advanced features</p>
                            <a href="#" class="portfolio-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="portfolio-item" data-category="app">
                    <div class="portfolio-image">
                        <div class="portfolio-overlay">
                            <h3>Fitness App</h3>
                            <p>Mobile app for workout tracking</p>
                            <a href="#" class="portfolio-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="portfolio-item" data-category="branding">
                    <div class="portfolio-image">
                        <div class="portfolio-overlay">
                            <h3>Brand Identity</h3>
                            <p>Complete brand design for startup</p>
                            <a href="#" class="portfolio-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="portfolio-item" data-category="web">
                    <div class="portfolio-image">
                        <div class="portfolio-overlay">
                            <h3>Corporate Website</h3>
                            <p>Professional business website</p>
                            <a href="#" class="portfolio-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="portfolio-item" data-category="app">
                    <div class="portfolio-image">
                        <div class="portfolio-overlay">
                            <h3>Food Delivery App</h3>
                            <p>Restaurant ordering system</p>
                            <a href="#" class="portfolio-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="portfolio-item" data-category="branding">
                    <div class="portfolio-image">
                        <div class="portfolio-overlay">
                            <h3>Logo Design</h3>
                            <p>Creative logo for tech company</p>
                            <a href="#" class="portfolio-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
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
