<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\UserModel;
use App\Models\StudentModel;

// Ensure ValidationStrategy classes are loaded (they're all in one file)
require_once __DIR__ . '/../core/Strategy/ValidationStrategy.php';
use App\Core\Strategy\UserValidationStrategy;

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }
    
    public function login() {
        $error_message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? ''
            ];
            
            // Validate input
            $validator = new Validator($data);
            $validator->required('email', 'Email is required.');
            $validator->required('password', 'Password is required.');
            $validator->email('email', 'Invalid email format.');
            
            if ($validator->isValid()) {
                $email = $validator->sanitize('email');
                $password = $data['password']; // Don't sanitize password
                
                $user = $this->userModel->findByEmail($email);
                
                if ($user && $user['status'] === 'active' && 
                    $this->userModel->verifyPassword($password, $user['password'])) {
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['name'] = $user['name'];
                    
                    // Redirect based on role
                    $this->redirect('/projecty/public/index.php?controller=dashboard&action=index');
                } else {
                    $error_message = 'Invalid email or password. Please try again.';
                }
            } else {
                $errors = $validator->getErrors();
                $error_message = implode(' ', array_values($errors));
            }
        }
        
        $this->view('auth/login', ['error_message' => $error_message]);
    }
    
    public function register() {
        $error_message = '';
        $success_message = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
                'role' => $_POST['role'] ?? ''
            ];
            
            // Use validation strategy
            $strategy = new UserValidationStrategy();
            
            // Add password confirmation validation
            $validator = new Validator($data);
            $validator->match('password', 'confirm_password', 'Passwords do not match.');
            
            if ($strategy->validate($data) && $validator->isValid()) {
                $name = $validator->sanitize('name');
                $email = $validator->sanitize('email');
                $password = $data['password']; // Don't sanitize password
                $role = $data['role'];
                
                $existingUser = $this->userModel->findByEmail($email);
                
                if ($existingUser) {
                    $error_message = 'Email already exists. Please use a different email.';
                } else {
                    $userId = $this->userModel->createUser($name, $email, $password, $role);
                    
                    // If student, create student record
                    if ($role === 'student') {
                        $studentModel = new StudentModel();
                        $studentModel->create([
                            'user_id' => $userId,
                            'student_id' => 'STU' . str_pad($userId, 3, '0', STR_PAD_LEFT),
                            'gpa' => 0.00,
                            'attendance_rate' => 0.00,
                            'risk_level' => 'low'
                        ]);
                    }
                    
                    $success_message = 'Registration successful! You can now login with your credentials.';
                }
            } else {
                $errors = array_merge($strategy->getErrors(), $validator->getErrors());
                $error_message = implode(' ', array_values($errors));
            }
        }
        
        $this->view('auth/register', [
            'error_message' => $error_message,
            'success_message' => $success_message
        ]);
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/projecty/public/index.php?controller=home&action=index');
    }
}


