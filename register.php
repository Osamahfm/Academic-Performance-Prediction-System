<?php
session_start();
require_once 'config/database.php';

// Handle registration form submission
$error_message = '';
$success_message = '';

if ($_POST) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if (!empty($name) && !empty($email) && !empty($password) && !empty($role)) {
        if ($password === $confirm_password) {
            if (strlen($password) >= 6) {
                try {
                    $pdo = getDBConnection();
                    
                    // Check if email already exists
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    
                    if ($stmt->fetchColumn()) {
                        $error_message = 'Email already exists. Please use a different email.';
                    } else {
                        // Insert new user
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$name, $email, $hashed_password, $role]);
                        
                        $success_message = 'Registration successful! You can now login with your credentials.';
                        
                        // Clear form data
                        $_POST = array();
                    }
                } catch (PDOException $e) {
                    $error_message = 'Database error. Please try again later.';
                }
            } else {
                $error_message = 'Password must be at least 6 characters long.';
            }
        } else {
            $error_message = 'Passwords do not match.';
        }
    } else {
        $error_message = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EduPredict Academic Performance Prediction System</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%);
            padding: 20px;
        }
        
        .register-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        
        .register-logo {
            margin-bottom: 30px;
        }
        
        .register-logo i {
            font-size: 3rem;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        
        .register-logo h1 {
            color: #333;
            font-size: 1.8rem;
            margin: 0;
        }
        
        .register-form {
            text-align: left;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2c5aa0;
            box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.1);
        }
        
        .role-selection {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .role-option {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .role-option:hover {
            border-color: #2c5aa0;
            background: rgba(44, 90, 160, 0.05);
        }
        
        .role-option.selected {
            border-color: #2c5aa0;
            background: rgba(44, 90, 160, 0.1);
        }
        
        .role-option input[type="radio"] {
            display: none;
        }
        
        .role-option i {
            font-size: 2rem;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        
        .role-option h3 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1rem;
        }
        
        .role-option p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .register-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #2c5aa0, #1e3a8a);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 90, 160, 0.4);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: #2c5aa0;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .home-fab {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #2c5aa0;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: background-color 0.3s ease;
        }
        .home-fab:hover { background-color: #1e3a8a; }
    </style>
    <a href="index.php" class="home-fab" title="Home"><i class="fas fa-home"></i></a>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div style="text-align: right; margin-bottom: 20px;">
                <a href="index.php" style="color: white; text-decoration: none; font-size: 1.2rem; padding: 10px; background: rgba(255,255,255,0.2); border-radius: 50%; display: inline-block; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i class="fas fa-home"></i>
                </a>
            </div>
            <div class="register-logo">
                <i class="fas fa-graduation-cap"></i>
                <h1>EduPredict</h1>
                <p>Create Your Account</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="message error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="message success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <form class="register-form" method="POST" action="">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo $_POST['name'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Select Your Role</label>
                    <div class="role-selection">
                        <div class="role-option" onclick="selectRole('student')">
                            <input type="radio" name="role" value="student" id="student">
                            <i class="fas fa-user-graduate"></i>
                            <h3>Student</h3>
                            <p>View your academic performance predictions</p>
                        </div>
                        
                        <div class="role-option" onclick="selectRole('instructor')">
                            <input type="radio" name="role" value="instructor" id="instructor">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <h3>Instructor</h3>
                            <p>Monitor student performance and predictions</p>
                        </div>
                        
                        <div class="role-option" onclick="selectRole('admin')">
                            <input type="radio" name="role" value="admin" id="admin">
                            <i class="fas fa-user-shield"></i>
                            <h3>Admin</h3>
                            <p>Manage system and view analytics</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>
            
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
                <p style="margin-top: 15px;">
                    <a href="index.php" style="color: #2c5aa0; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-home"></i> Return to Main Page
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        function selectRole(role) {
            // Remove selected class from all options
            document.querySelectorAll('.role-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            event.currentTarget.classList.add('selected');
            
            // Check the radio button
            document.getElementById(role).checked = true;
        }
    </script>
</body>
</html>
