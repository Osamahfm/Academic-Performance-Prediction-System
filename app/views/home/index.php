<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-text">
            <h1 class="hero-title">
               Welcome to EduPredict
            </h1>
            <p class="hero-description">
                Harness the power of AI and machine learning to predict student academic performance, 
                identify at-risk students, and improve educational outcomes.
            </p>
            <div class="hero-buttons">
                <a href="/projecty/public/index.php?controller=contact&action=index" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Get Started
                </a>
                <a href="/projecty/public/index.php?controller=page&action=portfolio" class="btn btn-secondary">
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>





