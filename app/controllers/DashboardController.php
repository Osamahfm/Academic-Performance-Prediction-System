<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\CourseModel;
use App\Models\GradeModel;
use App\Models\AlertModel;
use App\Services\PredictionService;

class DashboardController extends Controller {
    private $userModel;
    private $studentModel;
    private $courseModel;
    private $gradeModel;
    private $alertModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->userModel = new UserModel();
        $this->studentModel = new StudentModel();
        $this->courseModel = new CourseModel();
        $this->gradeModel = new GradeModel();
        $this->alertModel = new AlertModel();
    }
    
    public function index() {
        $role = $_SESSION['role'] ?? '';
        
        switch ($role) {
            case 'admin':
                $this->admin();
                break;
            case 'instructor':
                $this->instructor();
                break;
            case 'student':
                $this->student();
                break;
            default:
                $this->redirect('/projecty/public/index.php?controller=auth&action=login');
        }
    }
    
    public function admin() {
        $this->requireRole('admin');
        
        // Get statistics
        $totalUsers = $this->userModel->count();
        $totalStudents = $this->userModel->count(['role' => 'student']);
        $totalInstructors = $this->userModel->count(['role' => 'instructor']);
        $atRiskStudents = count($this->studentModel->getAtRiskStudents('high'));
        
        // Get recent users
        $recentUsers = $this->userModel->findAll([], 'created_at DESC', 5);
        
        $this->view('dashboard/admin', [
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalInstructors' => $totalInstructors,
            'atRiskStudents' => $atRiskStudents,
            'recentUsers' => $recentUsers
        ]);
    }
    
    public function instructor() {
        $this->requireRole('instructor');
        
        $instructorId = $_SESSION['user_id'];
        $courses = $this->courseModel->getCoursesByInstructor($instructorId);
        
        // Get at-risk students
        $atRiskStudents = $this->studentModel->getAtRiskStudents('high');
        
        // Get active alerts count
        $activeAlertsCount = $this->alertModel->getAlertCountByInstructor($instructorId);
        
        $this->view('dashboard/instructor', [
            'courses' => $courses,
            'atRiskStudents' => $atRiskStudents,
            'activeAlertsCount' => $activeAlertsCount
        ]);
    }
    
    public function student() {
        $this->requireRole('student');
        
        $userId = $_SESSION['user_id'];
        $student = $this->studentModel->findByUserId($userId);
        
        // Ensure student is an array or null, not false
        if (!$student || !is_array($student)) {
            $student = null;
        }
        
        if ($student && isset($student['id'])) {
            $grades = $this->gradeModel->getGradesByStudent($student['id']);
            $averageGrade = $this->gradeModel->getAverageGrade($student['id']);
            
            // Auto-run prediction if student has enough data
            if (count($grades) > 0) {
                try {
                    $predictionService = new PredictionService();
                    $predictionService->predictPerformance($student['id']);
                } catch (\Exception $e) {
                    // Silently fail - prediction is not critical for dashboard display
                }
            }
            
            // Get overall prediction for dashboard display
            $overallPrediction = null;
            try {
                $predictionModel = new \App\Models\PredictionModel();
                $predictions = $predictionModel->getPredictionsByStudent($student['id']);
                foreach ($predictions as $pred) {
                    if (empty($pred['course_name'])) {
                        $overallPrediction = $pred;
                        break;
                    }
                }
            } catch (\Exception $e) {
                // Silently fail
            }
        } else {
            $grades = [];
            $averageGrade = 0;
            $overallPrediction = null;
        }
        
        // Ensure student data has default values
        if ($student) {
            $student['gpa'] = $student['gpa'] ?? 0.00;
            $student['attendance_rate'] = $student['attendance_rate'] ?? 0.00;
            $student['risk_level'] = $student['risk_level'] ?? 'low';
        }
        
        $this->view('dashboard/student', [
            'student' => $student,
            'grades' => $grades,
            'averageGrade' => $averageGrade,
            'overallPrediction' => $overallPrediction
        ]);
    }
}



