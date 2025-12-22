<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Factory\ModelFactory;
use App\Core\Strategy\ValidationStrategy;
use App\Core\Strategy\UserValidationStrategy;
use App\Core\Strategy\CourseValidationStrategy;
use App\Core\Strategy\GradeValidationStrategy;
use App\Core\Strategy\ContactValidationStrategy;
use App\Models\GradeModel;
use App\Models\StudentModel;

/**
 * Generic CRUD Controller
 * Handles Create, Read, Update, Delete operations for all entities
 */
class CrudController extends Controller {
    
    /**
     * Get validation strategy for entity type
     */
    private function getValidationStrategy($entityType) {
        switch (strtolower($entityType)) {
            case 'user':
                return new UserValidationStrategy();
            case 'course':
                return new CourseValidationStrategy();
            case 'grade':
                return new GradeValidationStrategy();
            case 'contact':
                return new ContactValidationStrategy();
            default:
                return null;
        }
    }
    
    /**
     * List all records (Read)
     */
    public function index($entityType) {
        $this->requireLogin();
        
        // Check if this is an API request (JSON) or browser request (HTML)
        $isApiRequest = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
        $isApiRequest = $isApiRequest || (isset($_GET['format']) && $_GET['format'] === 'json');
        
        try {
            $model = null; // Initialize model variable
            $items = []; // Initialize items variable
            
            // Use specialized methods for courses, grades, and students to get joined data
            if (strtolower($entityType) === 'student') {
                // Get students with user info and grade counts
                $db = \App\Core\Database::getInstance()->getConnection();
                $sql = "SELECT s.id, s.student_id, s.gpa, s.attendance_rate, s.risk_level,
                               u.name, u.email,
                               COUNT(g.id) as grade_count
                        FROM students s 
                        LEFT JOIN users u ON s.user_id = u.id
                        LEFT JOIN grades g ON s.id = g.student_id
                        WHERE s.student_id NOT LIKE 'TRAIN_%'
                        GROUP BY s.id, s.student_id, s.gpa, s.attendance_rate, s.risk_level, u.name, u.email
                        ORDER BY s.id DESC";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                // Calculate statistics
                $stats = [
                    'with_grades' => 0,
                    'at_risk' => 0,
                    'avg_gpa' => 0
                ];
                $totalGPA = 0;
                $gpaCount = 0;
                
                foreach ($items as $student) {
                    if (($student['grade_count'] ?? 0) > 0) {
                        $stats['with_grades']++;
                    }
                    if (($student['risk_level'] ?? 'low') === 'high') {
                        $stats['at_risk']++;
                    }
                    if ($student['gpa'] !== null) {
                        $totalGPA += (float)$student['gpa'];
                        $gpaCount++;
                    }
                }
                
                $stats['avg_gpa'] = $gpaCount > 0 ? $totalGPA / $gpaCount : 0;
            } elseif (strtolower($entityType) === 'course') {
                $model = ModelFactory::create($entityType);
                // Get courses with instructor names
                $db = \App\Core\Database::getInstance()->getConnection();
                $sql = "SELECT c.*, u.name as instructor_name 
                        FROM courses c 
                        LEFT JOIN users u ON c.instructor_id = u.id 
                        ORDER BY c.id DESC";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } elseif (strtolower($entityType) === 'grade') {
                $model = ModelFactory::create($entityType);
                // Get grades with student and course names
                $db = \App\Core\Database::getInstance()->getConnection();
                $sql = "SELECT g.*, c.course_name, c.course_code, s.student_id, u.name as student_name 
                        FROM grades g 
                        LEFT JOIN courses c ON g.course_id = c.id 
                        LEFT JOIN students s ON g.student_id = s.id 
                        LEFT JOIN users u ON s.user_id = u.id 
                        ORDER BY g.id DESC";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                // Create model for other entity types (user, etc.)
                try {
                    $model = ModelFactory::create($entityType);
                    if (!$model) {
                        throw new \Exception("ModelFactory returned null for entity type: {$entityType}");
                    }
                    $items = $model->findAll([], 'id DESC');
                } catch (\Exception $e) {
                    throw new \Exception("Failed to create model for '{$entityType}': " . $e->getMessage());
                }
            }
            
            // Ensure items is set
            if (!isset($items)) {
                throw new \Exception("Items array not initialized for entity type: {$entityType}");
            }
            
            // Return JSON for API requests
            if ($isApiRequest) {
                $this->jsonResponse([
                    'success' => true,
                    'data' => $items,
                    'count' => count($items)
                ]);
                return;
            }
            
            // Render HTML view for browser requests based on entity type
            $viewMap = [
                'user' => 'admin/users',
                'course' => 'admin/courses',
                'grade' => 'admin/grades',
                'student' => 'admin/students',
                'contact' => 'admin/contacts'
            ];
            
            $viewName = $viewMap[strtolower($entityType)] ?? 'admin/crud/index';
            
            $viewData = [
                'entityType' => $entityType,
                'items' => $items,
                'count' => count($items)
            ];
            
            // Add statistics for students
            if (strtolower($entityType) === 'student' && isset($stats)) {
                $viewData['stats'] = $stats;
            }
            
            $this->view($viewName, $viewData);
        } catch (\Exception $e) {
            if ($isApiRequest) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            } else {
                $viewMap = [
                    'user' => 'admin/users',
                    'course' => 'admin/courses',
                    'grade' => 'admin/grades',
                    'contact' => 'admin/contacts'
                ];
                $viewName = $viewMap[strtolower($entityType)] ?? 'admin/crud/index';
                $this->view($viewName, [
                    'entityType' => $entityType,
                    'items' => [],
                    'count' => 0,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Get single record (Read)
     */
    public function show($entityType, $id) {
        $this->requireLogin();
        
        try {
            $model = ModelFactory::create($entityType);
            $item = $model->findById($id);
            
            if ($item) {
                $this->jsonResponse([
                    'success' => true,
                    'data' => $item
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Record not found'
                ], 404);
            }
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create new record
     */
    public function create($entityType) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Method not allowed'
            ], 405);
            return;
        }
        
        try {
            $data = $_POST;
            
            // Get validation strategy
            $strategy = $this->getValidationStrategy($entityType);
            
            if ($strategy && !$strategy->validate($data)) {
                $this->jsonResponse([
                    'success' => false,
                    'errors' => $strategy->getErrors()
                ], 400);
                return;
            }
            
            // Sanitize data
            $validator = new \App\Core\Validator($data);
            foreach ($data as $key => $value) {
                $data[$key] = $validator->sanitize($key);
            }
            
            $model = ModelFactory::create($entityType);
            $id = $model->create($data);
            
            // If student was created, create student record and optionally enroll in courses
            if (strtolower($entityType) === 'user' && isset($data['role']) && $data['role'] === 'student') {
                try {
                    $studentModel = new \App\Models\StudentModel();
                    $studentModel->create([
                        'user_id' => $id,
                        'student_id' => 'STU' . str_pad($id, 3, '0', STR_PAD_LEFT),
                        'gpa' => 0.00,
                        'attendance_rate' => 0.00,
                        'risk_level' => 'low'
                    ]);
                    
                    // Auto-enroll in courses if provided
                    if (!empty($data['course_ids']) && is_array($data['course_ids'])) {
                        $db = Database::getInstance()->getConnection();
                        $studentId = $studentModel->findOne(['user_id' => $id])['id'];
                        
                        foreach ($data['course_ids'] as $courseId) {
                            // Check if enrollment already exists
                            $checkSql = "SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
                            $checkStmt = $db->prepare($checkSql);
                            $checkStmt->execute([
                                ':student_id' => $studentId,
                                ':course_id' => $courseId
                            ]);
                            
                            if (!$checkStmt->fetch()) {
                                $enrollSql = "INSERT INTO enrollments (student_id, course_id, status) 
                                            VALUES (:student_id, :course_id, 'active')";
                                $enrollStmt = $db->prepare($enrollSql);
                                $enrollStmt->execute([
                                    ':student_id' => $studentId,
                                    ':course_id' => $courseId
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the user creation
                    error_log("Error creating student record or enrollment: " . $e->getMessage());
                }
            }
            
            // If grade was created, trigger KNN prediction automatically
            if (strtolower($entityType) === 'grade' && isset($data['student_id'])) {
                try {
                    $gradeModel = new \App\Models\GradeModel();
                    $courseId = $data['course_id'] ?? null;
                    $gradeModel->triggerPrediction($data['student_id'], $courseId);
                } catch (\Exception $e) {
                    // Silently fail - prediction is not critical for grade creation
                }
            }
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Record created successfully',
                'id' => $id
            ], 201);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update existing record
     */
    public function update($entityType, $id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Method not allowed'
            ], 405);
            return;
        }
        
        try {
            $data = $_POST;
            
            // Get validation strategy
            $strategy = $this->getValidationStrategy($entityType);
            
            if ($strategy && !$strategy->validate($data)) {
                $this->jsonResponse([
                    'success' => false,
                    'errors' => $strategy->getErrors()
                ], 400);
                return;
            }
            
            // Sanitize data
            $validator = new \App\Core\Validator($data);
            foreach ($data as $key => $value) {
                $data[$key] = $validator->sanitize($key);
            }
            
            $model = ModelFactory::create($entityType);
            $affected = $model->update($id, $data);
            
            // If grade was updated, trigger KNN prediction automatically
            if (strtolower($entityType) === 'grade' && $affected > 0) {
                try {
                    // Get the grade to find student_id
                    $grade = $model->findById($id);
                    if ($grade && isset($grade['student_id'])) {
                        $gradeModel = new GradeModel();
                        $courseId = $grade['course_id'] ?? null;
                        $gradeModel->triggerPrediction($grade['student_id'], $courseId);
                    }
                } catch (\Exception $e) {
                    // Silently fail - prediction is not critical for grade update
                }
            }
            
            if ($affected > 0) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Record updated successfully'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Record not found or no changes made'
                ], 404);
            }
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete record
     */
    public function delete($entityType, $id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Method not allowed'
            ], 405);
            return;
        }
        
        try {
            $model = ModelFactory::create($entityType);
            $affected = $model->delete($id);
            
            if ($affected > 0) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Record deleted successfully'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Record not found'
                ], 404);
            }
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


