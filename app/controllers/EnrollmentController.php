<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class EnrollmentController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->requireRole('admin');
    }
    
    /**
     * Show enrollment management page
     */
    public function index() {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        // Get all students
        $studentsSql = "SELECT s.id, s.student_id, u.name, u.email 
                       FROM students s 
                       INNER JOIN users u ON s.user_id = u.id 
                       ORDER BY u.name";
        $students = $pdo->query($studentsSql)->fetchAll();
        
        // Get all courses
        $coursesSql = "SELECT c.id, c.course_code, c.course_name, u.name as instructor_name 
                      FROM courses c 
                      LEFT JOIN users u ON c.instructor_id = u.id 
                      ORDER BY c.course_code";
        $courses = $pdo->query($coursesSql)->fetchAll();
        
        // Get all enrollments
        $enrollmentsSql = "SELECT e.*, s.student_id, u.name as student_name, 
                          c.course_code, c.course_name
                          FROM enrollments e
                          INNER JOIN students s ON e.student_id = s.id
                          INNER JOIN users u ON s.user_id = u.id
                          INNER JOIN courses c ON e.course_id = c.id
                          ORDER BY u.name, c.course_code";
        $enrollments = $pdo->query($enrollmentsSql)->fetchAll();
        
        $this->view('enrollments/index', [
            'students' => $students,
            'courses' => $courses,
            'enrollments' => $enrollments
        ]);
    }
    
    /**
     * Enroll student in course
     */
    public function enroll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Method not allowed'
            ], 405);
            return;
        }
        
        $studentId = $_POST['student_id'] ?? null;
        $courseId = $_POST['course_id'] ?? null;
        
        if (!$studentId || !$courseId) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Student ID and Course ID are required'
            ], 400);
            return;
        }
        
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            // Check if enrollment already exists
            $checkSql = "SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute([
                ':student_id' => $studentId,
                ':course_id' => $courseId
            ]);
            
            if ($checkStmt->fetch()) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Student is already enrolled in this course'
                ], 400);
                return;
            }
            
            // Create enrollment
            $sql = "INSERT INTO enrollments (student_id, course_id, status) 
                    VALUES (:student_id, :course_id, 'active')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':student_id' => $studentId,
                ':course_id' => $courseId
            ]);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Student enrolled successfully',
                'id' => $pdo->lastInsertId()
            ], 201);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Unenroll student from course
     */
    public function unenroll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Method not allowed'
            ], 405);
            return;
        }
        
        $enrollmentId = $_POST['enrollment_id'] ?? $_GET['id'] ?? null;
        
        if (!$enrollmentId) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Enrollment ID is required'
            ], 400);
            return;
        }
        
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            // Delete enrollment
            $sql = "DELETE FROM enrollments WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $enrollmentId]);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Student unenrolled successfully'
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk enroll students in courses
     */
    public function bulkEnroll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Method not allowed'
            ], 405);
            return;
        }
        
        $studentIds = $_POST['student_ids'] ?? [];
        $courseIds = $_POST['course_ids'] ?? [];
        
        if (empty($studentIds) || empty($courseIds)) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Please select at least one student and one course'
            ], 400);
            return;
        }
        
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            $enrolled = 0;
            $skipped = 0;
            
            foreach ($studentIds as $studentId) {
                foreach ($courseIds as $courseId) {
                    // Check if enrollment already exists
                    $checkSql = "SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
                    $checkStmt = $pdo->prepare($checkSql);
                    $checkStmt->execute([
                        ':student_id' => $studentId,
                        ':course_id' => $courseId
                    ]);
                    
                    if (!$checkStmt->fetch()) {
                        // Create enrollment
                        $sql = "INSERT INTO enrollments (student_id, course_id, status) 
                                VALUES (:student_id, :course_id, 'active')";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':student_id' => $studentId,
                            ':course_id' => $courseId
                        ]);
                        $enrolled++;
                    } else {
                        $skipped++;
                    }
                }
            }
            
            $this->jsonResponse([
                'success' => true,
                'message' => "Enrolled {$enrolled} student(s), skipped {$skipped} duplicate(s)"
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}





