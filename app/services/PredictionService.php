<?php
namespace App\Services;

use App\Core\ML\KNNPredictor;
use App\Models\StudentModel;
use App\Models\GradeModel;
use App\Models\PredictionModel;
use App\Models\AlertModel;

/**
 * Prediction Service
 * Handles ML-based academic performance prediction
 */
class PredictionService {
    private $knnPredictor;
    private $studentModel;
    private $gradeModel;
    private $predictionModel;
    private $alertModel;
    
    public function __construct() {
        $this->knnPredictor = new KNNPredictor(5); // Use 5 neighbors
        $this->studentModel = new StudentModel();
        $this->gradeModel = new GradeModel();
        $this->predictionModel = new PredictionModel();
        $this->alertModel = new AlertModel();
    }
    
    /**
     * Prepare training data from all students
     * @return array Training data with features and labels
     */
    private function prepareTrainingData() {
        $db = \App\Core\Database::getInstance()->getConnection();
        
        // Get all student data for training
        $sql = "SELECT s.id, s.gpa, s.attendance_rate, s.risk_level,
                       AVG(g.grade) as avg_grade,
                       COUNT(g.id) as assignments_completed
                FROM students s
                LEFT JOIN grades g ON s.id = g.student_id
                WHERE s.gpa IS NOT NULL 
                AND s.attendance_rate IS NOT NULL
                GROUP BY s.id, s.gpa, s.attendance_rate, s.risk_level
                HAVING COUNT(g.id) > 0";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $students = $stmt->fetchAll();
        
        $trainingData = [];
        foreach ($students as $student) {
            $trainingData[] = [
                'gpa' => (float)$student['gpa'],
                'attendance_rate' => (float)$student['attendance_rate'],
                'avg_grade' => (float)($student['avg_grade'] ?? 0),
                'assignments_completed' => (int)$student['assignments_completed'],
                'risk_level' => $student['risk_level'] ?? 'medium'
            ];
        }
        
        return $trainingData;
    }
    
    /**
     * Get student features for prediction
     * @param int $studentId Student ID
     * @param int|null $courseId Optional course ID for course-specific prediction
     * @return array Student features [gpa, attendance_rate, avg_grade, assignments_completed]
     */
    private function getStudentFeatures($studentId, $courseId = null) {
        $student = $this->studentModel->findById($studentId);
        
        if (!$student) {
            return [0, 0, 0, 0];
        }
        
        // Get grades
        if ($courseId) {
            $grades = $this->gradeModel->getGradesByCourse($courseId);
            $studentGrades = array_filter($grades, function($g) use ($studentId) {
                return $g['student_id'] == $studentId;
            });
            $avgGrade = $this->gradeModel->getAverageGrade($studentId, $courseId);
            $assignmentsCount = count($studentGrades);
        } else {
            $grades = $this->gradeModel->getGradesByStudent($studentId);
            $avgGrade = $this->gradeModel->getAverageGrade($studentId);
            $assignmentsCount = count($grades);
        }
        
        return [
            (float)($student['gpa'] ?? 0),
            (float)($student['attendance_rate'] ?? 0),
            (float)$avgGrade,
            (int)$assignmentsCount
        ];
    }
    
    /**
     * Predict student performance
     * @param int $studentId Student ID
     * @param int|null $courseId Optional course ID
     * @return array Prediction results
     */
    public function predictPerformance($studentId, $courseId = null) {
        // Load training data
        $trainingData = $this->prepareTrainingData();
        $this->knnPredictor->loadTrainingData($trainingData);
        
        // Get student features
        $features = $this->getStudentFeatures($studentId, $courseId);
        
        // Make prediction
        $prediction = $this->knnPredictor->predict($features);
        
        // Get course-specific grade prediction if course provided
        if ($courseId) {
            $grades = $this->gradeModel->getGradesByStudent($studentId);
            $courseGrades = array_map(function($g) {
                return (float)$g['grade'];
            }, array_filter($grades, function($g) use ($courseId) {
                return $g['course_id'] == $courseId;
            }));
            
            $predictedGrade = $this->knnPredictor->predictCourseGrade($features, $courseGrades);
        } else {
            $predictedGrade = $prediction['predicted_grade'];
        }
        
        // Determine risk factors
        $riskFactors = $this->identifyRiskFactors($features, $prediction);
        
        // Save prediction (always save, even if courseId is null for overall predictions)
        $this->predictionModel->savePrediction(
            $studentId,
            $courseId,
            $predictedGrade,
            $prediction['confidence'],
            json_encode($riskFactors)
        );
        
        // Update student risk level if changed
        if ($student = $this->studentModel->findById($studentId)) {
            $currentRisk = $student['risk_level'] ?? 'medium';
            if ($currentRisk !== $prediction['risk_level']) {
                $this->studentModel->updateRiskLevel($studentId, $prediction['risk_level']);
                
                // Create alert if risk level is high
                if ($prediction['risk_level'] === 'high') {
                    $this->createRiskAlert($studentId, $riskFactors);
                }
            }
        }
        
        return [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'predicted_grade' => $predictedGrade,
            'confidence' => $prediction['confidence'],
            'risk_level' => $prediction['risk_level'],
            'risk_factors' => $riskFactors,
            'features' => [
                'gpa' => $features[0],
                'attendance_rate' => $features[1],
                'avg_grade' => $features[2],
                'assignments_completed' => $features[3]
            ]
        ];
    }
    
    /**
     * Identify risk factors based on features
     * @param array $features Student features
     * @param array $prediction Prediction results
     * @return array Risk factors
     */
    private function identifyRiskFactors($features, $prediction) {
        $riskFactors = [];
        
        if ($features[0] < 2.0) { // Low GPA
            $riskFactors[] = 'Low GPA (' . number_format($features[0], 2) . ')';
        }
        
        if ($features[1] < 70) { // Low attendance
            $riskFactors[] = 'Low attendance rate (' . number_format($features[1], 1) . '%)';
        }
        
        if ($features[2] < 60) { // Low average grade
            $riskFactors[] = 'Low average grade (' . number_format($features[2], 2) . ')';
        }
        
        if ($features[3] < 3) { // Few assignments completed
            $riskFactors[] = 'Incomplete assignments (' . $features[3] . ' completed)';
        }
        
        if ($prediction['predicted_grade'] < 60) {
            $riskFactors[] = 'Predicted grade below passing (' . $prediction['predicted_grade'] . ')';
        }
        
        return $riskFactors;
    }
    
    /**
     * Create alert for at-risk student
     * @param int $studentId Student ID
     * @param array $riskFactors Risk factors
     */
    private function createRiskAlert($studentId, $riskFactors) {
        try {
            $message = "Student identified as at-risk. Factors: " . implode(', ', $riskFactors);
            
            // Check if alert already exists
            $existingAlerts = $this->alertModel->getAlertsByStudent($studentId);
            $hasActiveAlert = false;
            
            foreach ($existingAlerts as $alert) {
                if (isset($alert['alert_type']) && $alert['alert_type'] === 'at_risk' && 
                    isset($alert['status']) && $alert['status'] === 'active') {
                    $hasActiveAlert = true;
                    break;
                }
            }
            
            if (!$hasActiveAlert) {
                $this->alertModel->createAlert($studentId, 'at_risk', $message, 'high');
            }
        } catch (\Exception $e) {
            // Silently fail - alert creation is not critical
        }
    }
    
    /**
     * Predict performance for all students
     * @return array Predictions for all students
     */
    public function predictAllStudents() {
        $students = $this->studentModel->findAll();
        $predictions = [];
        
        foreach ($students as $student) {
            $predictions[] = $this->predictPerformance($student['id']);
        }
        
        return $predictions;
    }
    
    /**
     * Predict performance for students in a course
     * @param int $courseId Course ID
     * @return array Predictions for course students
     */
    public function predictCourseStudents($courseId) {
        $db = \App\Core\Database::getInstance()->getConnection();
        
        $sql = "SELECT DISTINCT s.id 
                FROM students s
                INNER JOIN enrollments e ON s.id = e.student_id
                WHERE e.course_id = :course_id AND e.status = 'active'";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        $studentIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $predictions = [];
        foreach ($studentIds as $studentId) {
            $predictions[] = $this->predictPerformance($studentId, $courseId);
        }
        
        return $predictions;
    }
}

