<?php
session_start();
require_once 'config/database.php';

// Handle login form submission
$error_message = '';
$success_message = '';

if ($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: admin-dashboard.php');
                        break;
                    case 'instructor':
                        header('Location: instructor-dashboard.php');
                        break;
                    case 'student':
                        header('Location: student-dashboard.php');
                        break;
                    default:
                        header('Location: index.php');
                }
                exit;
            } else {
                $error_message = 'Invalid email or password. Please try again.';
            }
        } catch (PDOException $e) {
            $error_message = 'Database connection error. Please try again later.';
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
    <title>Login - EduPredict Academic Performance Prediction System</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%);
            padding: 20px;
        }
        
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        
        .login-logo {
            margin-bottom: 30px;
        }
        
        .login-logo i {
            font-size: 3rem;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        
        .login-logo h1 {
            color: #333;
            font-size: 1.8rem;
            margin: 0;
        }
        
        .login-form {
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
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2c5aa0;
            box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.1);
        }
        
        .login-btn {
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
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 90, 160, 0.4);
        }
        
        .demo-accounts {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: left;
        }
        
        .demo-accounts h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 1rem;
        }
        
        .demo-account {
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #2c5aa0;
        }
        
        .demo-account strong {
            color: #2c5aa0;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-link a {
            color: #2c5aa0;
            text-decoration: none;
            font-weight: 500;
        }
        
        .register-link a:hover {
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
        .home-fab:hover {
            background-color: #1e3a8a;
        }
    </style>
    <a href="index.php" class="home-fab" title="Home"><i class="fas fa-home"></i></a>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-graduation-cap"></i>
                <h1>EduPredict</h1>
                <p>Academic Performance Prediction System</p>
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
            
            <form class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo $_POST['email'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>
            
            
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
