<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\PredictionService;
use App\Models\PredictionModel;
use App\Models\StudentModel;
use App\Models\CourseModel;

/**
 * Prediction Controller
 * Handles ML-based academic performance predictions
 */
class PredictionController extends Controller {
    private $predictionService;
    private $predictionModel;
    private $studentModel;
    private $courseModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->predictionService = new \App\Services\PredictionService();
        $this->predictionModel = new PredictionModel();
        $this->studentModel = new StudentModel();
        $this->courseModel = new CourseModel();
    }
    
    /**
     * Predict performance for a specific student
     */
    public function predictStudent() {
        $studentId = $_GET['student_id'] ?? null;
        $courseId = $_GET['course_id'] ?? null;
        
        if (!$studentId) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Student ID required'
            ], 400);
            return;
        }
        
        try {
            $prediction = $this->predictionService->predictPerformance($studentId, $courseId);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $prediction
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Predict performance for all students (Admin/Instructor)
     */
    public function predictAll() {
        $role = $_SESSION['role'] ?? '';
        
        if ($role !== 'admin' && $role !== 'instructor') {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Access denied'
            ], 403);
            return;
        }
        
        try {
            $predictions = $this->predictionService->predictAllStudents();
            
            $this->jsonResponse([
                'success' => true,
                'data' => $predictions,
                'count' => count($predictions)
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Predict performance for course students
     */
    public function predictCourse() {
        $courseId = $_GET['course_id'] ?? null;
        
        if (!$courseId) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Course ID required'
            ], 400);
            return;
        }
        
        // Check if instructor owns this course
        $role = $_SESSION['role'] ?? '';
        if ($role === 'instructor') {
            $instructorId = $_SESSION['user_id'];
            $course = $this->courseModel->findById($courseId);
            
            if (!$course || $course['instructor_id'] != $instructorId) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Access denied'
                ], 403);
                return;
            }
        }
        
        try {
            $predictions = $this->predictionService->predictCourseStudents($courseId);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $predictions,
                'count' => count($predictions)
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * View predictions page
     */
    public function index() {
        $role = $_SESSION['role'] ?? '';
        
        if ($role === 'student') {
            $userId = $_SESSION['user_id'];
            $student = $this->studentModel->findByUserId($userId);
            
            if ($student) {
                $studentId = $student['id'];
                $hasData = ($student['gpa'] !== null && $student['gpa'] > 0) && 
                          ($student['attendance_rate'] !== null && $student['attendance_rate'] > 0);
                
                // Check if manual refresh requested
                $forceRefresh = isset($_GET['refresh']) && $_GET['refresh'] == '1';
                
                // Auto-refresh if student has data and predictions are outdated or refresh requested
                if ($hasData) {
                    $needsRefresh = $forceRefresh;
                    
                    if (!$needsRefresh) {
                        // Get existing predictions
                        $existingPredictions = $this->predictionModel->getPredictionsByStudent($studentId);
                        
                        // Check if we need to refresh
                        $needsRefresh = empty($existingPredictions);
                        
                        if (!$needsRefresh && !empty($existingPredictions)) {
                            // Check if predictions show old data (risk factors mention 0 GPA/attendance)
                            $latestPrediction = $existingPredictions[0] ?? null;
                            if ($latestPrediction && !empty($latestPrediction['risk_factors'])) {
                                $riskFactors = json_decode($latestPrediction['risk_factors'], true);
                                if (is_array($riskFactors)) {
                                    foreach ($riskFactors as $key => $factor) {
                                        // Skip prediction_data array, only check string risk factors
                                        if ($key === 'prediction_data') {
                                            continue;
                                        }
                                        // Only process string risk factors
                                        if (is_string($factor)) {
                                            if (stripos($factor, 'Low GPA (0.00)') !== false || 
                                                stripos($factor, 'Low attendance rate (0.0%)') !== false) {
                                                $needsRefresh = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            
                            // Check if new grades or enrollments have been added since last prediction
                            if (!$needsRefresh && $latestPrediction) {
                                $db = \App\Core\Database::getInstance()->getConnection();
                                $lastPredictionDate = $latestPrediction['prediction_date'] ?? null;
                                
                                if ($lastPredictionDate) {
                                    // Check if any grades were added after the last prediction
                                    $newGradesSql = "SELECT COUNT(*) FROM grades 
                                                    WHERE student_id = :student_id 
                                                    AND date_recorded > :prediction_date";
                                    $newGradesStmt = $db->prepare($newGradesSql);
                                    $newGradesStmt->execute([
                                        ':student_id' => $studentId,
                                        ':prediction_date' => $lastPredictionDate
                                    ]);
                                    $newGradesCount = $newGradesStmt->fetchColumn();
                                    
                                    // Check if any new enrollments were added
                                    $newEnrollmentsSql = "SELECT COUNT(*) FROM enrollments 
                                                         WHERE student_id = :student_id 
                                                         AND status = 'active'
                                                         AND enrollment_date > :prediction_date";
                                    $newEnrollmentsStmt = $db->prepare($newEnrollmentsSql);
                                    $newEnrollmentsStmt->execute([
                                        ':student_id' => $studentId,
                                        ':prediction_date' => $lastPredictionDate
                                    ]);
                                    $newEnrollmentsCount = $newEnrollmentsStmt->fetchColumn();
                                    
                                    // Check if number of enrolled courses changed
                                    $currentEnrollmentsSql = "SELECT COUNT(DISTINCT course_id) FROM enrollments 
                                                              WHERE student_id = :student_id AND status = 'active'";
                                    $currentEnrollmentsStmt = $db->prepare($currentEnrollmentsSql);
                                    $currentEnrollmentsStmt->execute([':student_id' => $studentId]);
                                    $currentCourseCount = $currentEnrollmentsStmt->fetchColumn();
                                    
                                    // Count how many course-specific predictions exist
                                    $predictionCountSql = "SELECT COUNT(*) FROM predictions 
                                                          WHERE student_id = :student_id 
                                                          AND course_id IS NOT NULL";
                                    $predictionCountStmt = $db->prepare($predictionCountSql);
                                    $predictionCountStmt->execute([':student_id' => $studentId]);
                                    $predictionCount = $predictionCountStmt->fetchColumn();
                                    
                                    if ($newGradesCount > 0 || $newEnrollmentsCount > 0 || $currentCourseCount != $predictionCount) {
                                        $needsRefresh = true;
                                    }
                                }
                            }
                        }
                    }
                    
                    // Refresh predictions if needed
                    if ($needsRefresh) {
                        try {
                            // Get all courses the student is enrolled in
                            $db = \App\Core\Database::getInstance()->getConnection();
                            $enrollmentsSql = "SELECT course_id FROM enrollments WHERE student_id = :student_id AND status = 'active'";
                            $enrollStmt = $db->prepare($enrollmentsSql);
                            $enrollStmt->execute([':student_id' => $studentId]);
                            $courseIds = $enrollStmt->fetchAll(\PDO::FETCH_COLUMN);
                            
                            // Generate predictions for each course (only if student has grades)
                            if (!empty($courseIds)) {
                                foreach ($courseIds as $courseId) {
                                    try {
                                        $this->predictionService->predictPerformance($studentId, $courseId);
                                    } catch (\Exception $e) {
                                        // Log but continue with other courses
                                        error_log("Prediction failed for course {$courseId}: " . $e->getMessage());
                                    }
                                }
                            }
                            
                            // Also generate overall prediction (courseId = null)
                            $this->predictionService->predictPerformance($studentId, null);
                        } catch (\Exception $e) {
                            // Log error but continue
                            error_log("Auto-refresh prediction failed: " . $e->getMessage());
                        }
                    }
                }
                
                // Get updated predictions
                $predictions = $this->predictionModel->getPredictionsByStudent($studentId);
            } else {
                $predictions = [];
            }
            
            $this->view('predictions/student', [
                'predictions' => $predictions,
                'student' => $student
            ]);
        } else {
            // Admin/Instructor view
            $this->view('predictions/index', [
                'role' => $role
            ]);
        }
    }
    
    /**
     * View predictions for a specific course
     */
    public function course() {
        $courseId = $_GET['course_id'] ?? null;
        
        if (!$courseId) {
            $this->redirect('/projecty/public/index.php?controller=prediction&action=index');
            return;
        }
        
        $course = $this->courseModel->findById($courseId);
        $predictions = $this->predictionModel->getPredictionsByCourse($courseId);
        
        $this->view('predictions/course', [
            'course' => $course,
            'predictions' => $predictions
        ]);
    }
    
    /**
     * Train model - Admin interface for training KNN
     */
    public function train() {
        $this->requireRole('admin');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        
        // Handle training request
        $trainingResults = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['train_model'])) {
            try {
                $students = $db->query("SELECT id FROM students WHERE gpa IS NOT NULL AND attendance_rate IS NOT NULL")->fetchAll();
                
                $trained = 0;
                $errors = 0;
                $errorMessages = [];
                
                foreach ($students as $student) {
                    try {
                        // Get all courses this student is currently enrolled in
                        $enrollmentsSql = "SELECT course_id FROM enrollments WHERE student_id = :student_id AND status = 'active'";
                        $enrollStmt = $db->prepare($enrollmentsSql);
                        $enrollStmt->execute([':student_id' => $student['id']]);
                        $courseIds = $enrollStmt->fetchAll(\PDO::FETCH_COLUMN);
                        
                        // Generate predictions for each course
                        foreach ($courseIds as $courseId) {
                            $this->predictionService->predictPerformance($student['id'], $courseId);
                        }
                        
                        // Generate overall prediction
                        $this->predictionService->predictPerformance($student['id'], null);
                        $trained++;
                    } catch (\Exception $e) {
                        $errors++;
                        $errorMessages[] = "Student ID {$student['id']}: " . $e->getMessage();
                    }
                }
                
                $trainingResults = [
                    'success' => true,
                    'trained' => $trained,
                    'errors' => $errors,
                    'errorMessages' => $errorMessages
                ];
            } catch (\Exception $e) {
                $trainingResults = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        // Get training data statistics
        $sql = "SELECT 
                    COUNT(DISTINCT s.id) as total_students,
                    COUNT(DISTINCT CASE WHEN s.gpa IS NOT NULL AND s.attendance_rate IS NOT NULL THEN s.id END) as students_with_data,
                    COUNT(DISTINCT CASE WHEN EXISTS(SELECT 1 FROM grades g WHERE g.student_id = s.id) THEN s.id END) as students_with_grades,
                    COUNT(DISTINCT CASE WHEN s.gpa IS NOT NULL AND s.attendance_rate IS NOT NULL 
                                        AND EXISTS(SELECT 1 FROM grades g WHERE g.student_id = s.id) THEN s.id END) as training_ready,
                    AVG(s.gpa) as avg_gpa,
                    AVG(s.attendance_rate) as avg_attendance,
                    COUNT(DISTINCT g.id) as total_grades
                FROM students s
                LEFT JOIN grades g ON s.id = g.student_id";
        
        $stats = $db->query($sql)->fetch();
        
        // Get sample training data
        $trainingSql = "SELECT s.id, s.student_id, s.gpa, s.attendance_rate, s.risk_level,
                       AVG(g.grade) as avg_grade,
                       COUNT(g.id) as assignments_completed
                FROM students s
                LEFT JOIN grades g ON s.id = g.student_id
                WHERE s.gpa IS NOT NULL 
                AND s.attendance_rate IS NOT NULL
                GROUP BY s.id, s.gpa, s.attendance_rate, s.risk_level
                HAVING COUNT(g.id) > 0
                LIMIT 10";
        
        $trainingData = $db->query($trainingSql)->fetchAll();
        
        $this->view('predictions/train', [
            'stats' => $stats,
            'trainingData' => $trainingData,
            'trainingResults' => $trainingResults
        ]);
    }
}



