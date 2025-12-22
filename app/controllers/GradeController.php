<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\GradeModel;
use App\Models\CourseModel;
use App\Models\StudentModel;

class GradeController extends Controller {
    private $gradeModel;
    private $courseModel;
    private $studentModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        $this->gradeModel = new GradeModel();
        $this->courseModel = new CourseModel();
        $this->studentModel = new StudentModel();
    }
    
    /**
     * Show grade management page for instructors
     */
    public function manage() {
        $this->requireRole('instructor');
        
        $instructorId = $_SESSION['user_id'];
        
        // Debug: Check instructor ID
        if (empty($instructorId)) {
            error_log("GradeController::manage() - Instructor ID is empty in session");
        }
        
        // Get instructor's courses
        $courses = $this->courseModel->getCoursesByInstructor($instructorId);
        
        // Debug: Log courses found
        error_log("GradeController::manage() - Instructor ID: " . $instructorId . ", Courses found: " . count($courses));
        
        // Get selected course ID from query parameter
        $selectedCourseId = $_GET['course_id'] ?? null;
        
        // Get enrolled students for selected course
        $enrolledStudents = [];
        $selectedCourse = null;
        if ($selectedCourseId && $this->courseModel->isInstructorCourse($selectedCourseId, $instructorId)) {
            $selectedCourse = $this->courseModel->findOne(['id' => $selectedCourseId]);
            $enrolledStudents = $this->courseModel->getEnrolledStudentsByCourse($selectedCourseId, $instructorId);
        }
        
        // Get grades for selected course if course is selected
        $grades = [];
        if ($selectedCourseId && $this->courseModel->isInstructorCourse($selectedCourseId, $instructorId)) {
            $grades = $this->gradeModel->getGradesByCourse($selectedCourseId);
        }
        
        $this->view('grades/instructor_manage', [
            'courses' => $courses,
            'selectedCourseId' => $selectedCourseId,
            'selectedCourse' => $selectedCourse,
            'enrolledStudents' => $enrolledStudents,
            'grades' => $grades
        ]);
    }
    
    /**
     * Create a new grade (POST)
     */
    public function create() {
        $this->requireRole('instructor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Method not allowed'
            ], 405);
            return;
        }
        
        $instructorId = $_SESSION['user_id'];
        $courseId = $_POST['course_id'] ?? null;
        $studentId = $_POST['student_id'] ?? null;
        
        // Verify instructor owns the course
        if (!$courseId || !$this->courseModel->isInstructorCourse($courseId, $instructorId)) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'You do not have permission to add grades for this course'
            ], 403);
            return;
        }
        
        // Verify student is enrolled in the course
        $enrolledStudents = $this->courseModel->getEnrolledStudentsByCourse($courseId, $instructorId);
        $studentEnrolled = false;
        foreach ($enrolledStudents as $student) {
            if ($student['id'] == $studentId) {
                $studentEnrolled = true;
                break;
            }
        }
        
        if (!$studentEnrolled) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Student is not enrolled in this course'
            ], 400);
            return;
        }
        
        // Validate required fields
        $requiredFields = ['student_id', 'course_id', 'assignment_type', 'grade', 'max_grade'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || $_POST[$field] === '') {
                $this->jsonResponse([
                    'success' => false,
                    'error' => "Field '{$field}' is required"
                ], 400);
                return;
            }
        }
        
        // Prepare grade data
        $gradeData = [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'assignment_type' => $_POST['assignment_type'],
            'grade' => floatval($_POST['grade']),
            'max_grade' => floatval($_POST['max_grade'])
        ];
        
        try {
            $id = $this->gradeModel->create($gradeData);
            
            // Trigger prediction update
            $this->gradeModel->triggerPrediction($studentId, $courseId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Grade added successfully',
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
     * Update an existing grade (POST)
     */
    public function update() {
        $this->requireRole('instructor');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Method not allowed'
            ], 405);
            return;
        }
        
        $instructorId = $_SESSION['user_id'];
        $gradeId = $_POST['id'] ?? null;
        
        if (!$gradeId) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Grade ID is required'
            ], 400);
            return;
        }
        
        // Get the grade to verify ownership
        $grade = $this->gradeModel->findOne(['id' => $gradeId]);
        if (!$grade) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Grade not found'
            ], 404);
            return;
        }
        
        // Verify instructor owns the course
        if (!$this->courseModel->isInstructorCourse($grade['course_id'], $instructorId)) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'You do not have permission to update this grade'
            ], 403);
            return;
        }
        
        // Prepare update data
        $updateData = [];
        if (isset($_POST['assignment_type'])) {
            $updateData['assignment_type'] = $_POST['assignment_type'];
        }
        if (isset($_POST['grade'])) {
            $updateData['grade'] = floatval($_POST['grade']);
        }
        if (isset($_POST['max_grade'])) {
            $updateData['max_grade'] = floatval($_POST['max_grade']);
        }
        
        try {
            $this->gradeModel->update($gradeId, $updateData);
            
            // Trigger prediction update
            $this->gradeModel->triggerPrediction($grade['student_id'], $grade['course_id']);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Grade updated successfully'
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a grade (POST/DELETE)
     */
    public function delete() {
        $this->requireRole('instructor');
        
        $instructorId = $_SESSION['user_id'];
        $gradeId = $_GET['id'] ?? $_POST['id'] ?? null;
        
        if (!$gradeId) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Grade ID is required'
            ], 400);
            return;
        }
        
        // Get the grade to verify ownership
        $grade = $this->gradeModel->findOne(['id' => $gradeId]);
        if (!$grade) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Grade not found'
            ], 404);
            return;
        }
        
        // Verify instructor owns the course
        if (!$this->courseModel->isInstructorCourse($grade['course_id'], $instructorId)) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'You do not have permission to delete this grade'
            ], 403);
            return;
        }
        
        try {
            $this->gradeModel->delete($gradeId);
            
            // Trigger prediction update
            $this->gradeModel->triggerPrediction($grade['student_id'], $grade['course_id']);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Grade deleted successfully'
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

