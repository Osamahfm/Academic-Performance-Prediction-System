<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\AlertModel;
use App\Models\CourseModel;

/**
 * Alert Controller
 * Handles alert management for instructors
 */
class AlertController extends Controller {
    private $alertModel;
    private $courseModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->alertModel = new AlertModel();
        $this->courseModel = new CourseModel();
    }
    
    /**
     * View alerts for instructor
     */
    public function index() {
        $role = $_SESSION['role'] ?? '';
        
        try {
            if ($role === 'instructor') {
                $instructorId = $_SESSION['user_id'];
                $alerts = $this->alertModel->getAlertsByInstructor($instructorId);
            } elseif ($role === 'admin') {
                // Admin can see all alerts with student info
                $db = \App\Core\Database::getInstance()->getConnection();
                $sql = "SELECT a.*, s.student_id, s.user_id, u.name as student_name, c.course_name, c.course_code
                        FROM alerts a
                        LEFT JOIN students s ON a.student_id = s.id
                        LEFT JOIN users u ON s.user_id = u.id
                        LEFT JOIN enrollments e ON s.id = e.student_id
                        LEFT JOIN courses c ON e.course_id = c.id
                        WHERE a.status = 'active'
                        ORDER BY a.created_at DESC";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $alerts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                // Students see their own alerts
                $studentModel = new \App\Models\StudentModel();
                $student = $studentModel->findByUserId($_SESSION['user_id']);
                if ($student && isset($student['id'])) {
                    $alerts = $this->alertModel->getAlertsByStudent($student['id']);
                } else {
                    $alerts = [];
                }
            }
        } catch (\Exception $e) {
            $alerts = [];
        }
        
        $this->view('alerts/index', [
            'alerts' => $alerts ?? [],
            'role' => $role
        ]);
    }
    
    /**
     * Resolve alert
     */
    public function resolve() {
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $this->alertModel->resolveAlert($id);
            $this->redirect('/projecty/public/index.php?controller=alert&action=index&success=resolved');
        } else {
            $this->redirect('/projecty/public/index.php?controller=alert&action=index');
        }
    }
    
    /**
     * Dismiss alert
     */
    public function dismiss() {
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $this->alertModel->dismissAlert($id);
            $this->redirect('/projecty/public/index.php?controller=alert&action=index&success=dismissed');
        } else {
            $this->redirect('/projecty/public/index.php?controller=alert&action=index');
        }
    }
}

