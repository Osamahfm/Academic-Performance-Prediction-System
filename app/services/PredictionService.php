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
            // Get all grades for the student
            $grades = $this->gradeModel->getGradesByStudent($studentId);
            $avgGrade = $this->gradeModel->getAverageGrade($studentId);
            // Count unique assignments (grades) - ensure we're counting actual grade records
            $assignmentsCount = count($grades);
            
            // If no grades but student exists, check if they're enrolled in courses
            if ($assignmentsCount == 0) {
                $db = \App\Core\Database::getInstance()->getConnection();
                $enrollmentsSql = "SELECT COUNT(*) FROM enrollments WHERE student_id = :student_id AND status = 'active'";
                $enrollStmt = $db->prepare($enrollmentsSql);
                $enrollStmt->execute([':student_id' => $studentId]);
                $enrolledCourses = $enrollStmt->fetchColumn();
                
                // If enrolled in courses but no grades, this is a valid concern
                // Otherwise, student might just be new
            }
        }
        
        return [
            (float)($student['gpa'] ?? 0),
            (float)($student['attendance_rate'] ?? 0),
            (float)$avgGrade,
            (int)$assignmentsCount
        ];
    }
    
    /**
     * Predict student GPA based on actual final grades (no grade prediction)
     * @param int $studentId Student ID
     * @param int|null $courseId Optional course ID
     * @return array Prediction results
     */
    public function predictPerformance($studentId, $courseId = null) {
        // Get student features for risk assessment
        $features = $this->getStudentFeatures($studentId, $courseId);
        
        // Load training data for risk level prediction
        $trainingData = $this->prepareTrainingData();
        $this->knnPredictor->loadTrainingData($trainingData);
        $prediction = $this->knnPredictor->predict($features);
        
        // Calculate GPA from ACTUAL final grades (not predicted grades)
        if ($courseId) {
            // For course-specific: calculate GPA from actual grades in this course
            $courseGrades = $this->gradeModel->getGradesByStudent($studentId);
            $filteredGrades = array_filter($courseGrades, function($g) use ($courseId) {
                return $g['course_id'] == $courseId;
            });
            
            if (!empty($filteredGrades)) {
                // Calculate average grade percentage from actual grades
                $totalGrade = 0;
                $totalMaxGrade = 0;
                foreach ($filteredGrades as $grade) {
                    $gradeValue = (float)($grade['grade'] ?? 0);
                    $maxGrade = (float)($grade['max_grade'] ?? 100);
                    $percentage = ($maxGrade > 0) ? ($gradeValue / $maxGrade) * 100 : 0;
                    $totalGrade += $percentage;
                    $totalMaxGrade += 100;
                }
                $averageGrade = count($filteredGrades) > 0 ? ($totalGrade / count($filteredGrades)) : 0;
                $predictedGpa = $this->gradeToGpa($averageGrade);
                $predictedGrade = $averageGrade; // For display purposes
            } else {
                // No grades yet, use prediction as fallback
                $predictedGrade = $prediction['predicted_grade'];
                $predictedGpa = $this->gradeToGpa($predictedGrade);
            }
        } else {
            // For overall: calculate GPA from actual final grades in all courses
            $db = \App\Core\Database::getInstance()->getConnection();
            $enrollmentsSql = "SELECT course_id FROM enrollments WHERE student_id = :student_id AND status = 'active'";
            $enrollStmt = $db->prepare($enrollmentsSql);
            $enrollStmt->execute([':student_id' => $studentId]);
            $courseIds = $enrollStmt->fetchAll(\PDO::FETCH_COLUMN);
            
            $courseGpas = [];
            $allGrades = $this->gradeModel->getGradesByStudent($studentId);
            
            foreach ($courseIds as $cid) {
                // Get actual grades for this course
                $courseGrades = array_filter($allGrades, function($g) use ($cid) {
                    return $g['course_id'] == $cid;
                });
                
                if (!empty($courseGrades)) {
                    // Calculate average grade percentage from actual grades
                    $totalGrade = 0;
                    foreach ($courseGrades as $grade) {
                        $gradeValue = (float)($grade['grade'] ?? 0);
                        $maxGrade = (float)($grade['max_grade'] ?? 100);
                        $percentage = ($maxGrade > 0) ? ($gradeValue / $maxGrade) * 100 : 0;
                        $totalGrade += $percentage;
                    }
                    $averageGrade = $totalGrade / count($courseGrades);
                    $courseGpas[] = $this->gradeToGpa($averageGrade);
                }
            }
            
            // Calculate overall GPA as average of course GPAs (like real transcript)
            if (!empty($courseGpas)) {
                $predictedGpa = array_sum($courseGpas) / count($courseGpas);
                // Calculate average grade percentage for display
                $predictedGrade = $this->gpaToGrade($predictedGpa);
            } else {
                // No grades yet, use prediction as fallback
                $predictedGrade = $prediction['predicted_grade'];
                $predictedGpa = $this->gradeToGpa($predictedGrade);
            }
        }
        
        // Determine risk factors using current features and base prediction
        $riskFactors = $this->identifyRiskFactors($features, $prediction, $studentId);

        // -------------------------
        // GPA-focused risk mapping
        // -------------------------

        // Get current GPA from student record (stored GPA) to compare with predicted GPA
        // Predicted GPA is calculated from all current grades, so comparing to stored GPA shows the trend
        $student = $this->studentModel->findById($studentId);
        $currentGpa = (float)($student['gpa'] ?? 0.0);
        
        // If stored GPA is 0 or seems outdated, calculate it from grades as fallback
        if ($currentGpa == 0.0) {
            $currentGpa = $this->calculateCurrentGpa($studentId);
        }
        
        // Determine GPA trend (increase/decrease/stable)
        $gpaChange = $predictedGpa - $currentGpa;
        $gpaTrend = 'stable';
        if ($gpaChange > 0.1) {
            $gpaTrend = 'increase';
        } elseif ($gpaChange < -0.1) {
            $gpaTrend = 'decrease';
        }
        
        // Derive final risk level mainly from predicted GPA / grade and risk factors,
        // so it actually changes when performance changes (not only from KNN labels).
        $riskLevel = $prediction['risk_level'];
        
        if ($predictedGrade < 60) {
            $riskLevel = 'high';
        } elseif ($predictedGrade < 75) {
            $riskLevel = 'medium';
        }
        
        // If indicators are very low, force high risk
        if ($features[0] < 2.0 || $features[1] < 60) { // low GPA or very low attendance
            $riskLevel = 'high';
        } elseif ($features[0] < 2.5 || $features[1] < 75) {
            // moderate issues
            $riskLevel = $riskLevel === 'low' ? 'medium' : $riskLevel;
        }
        
        // Save prediction with GPA trend information
        // Store risk factors as array of strings, and add prediction_data separately
        $riskFactorsArray = is_array($riskFactors) ? $riskFactors : [];
        $predictionData = [
            'predicted_grade' => $predictedGrade,
            'predicted_gpa' => $predictedGpa,
            'current_gpa' => $currentGpa,
            'gpa_trend' => $gpaTrend,
            'gpa_change' => round($gpaChange, 2)
        ];
        
        // Combine risk factors (strings) with prediction data
        $riskFactorsWithData = array_merge($riskFactorsArray, ['prediction_data' => $predictionData]);
        
        // Save prediction (always save, even if courseId is null for overall predictions)
        $this->predictionModel->savePrediction(
            $studentId,
            $courseId,
            $predictedGrade,
            $prediction['confidence'],
            json_encode($riskFactorsWithData)
        );
        
        // Update student risk level if changed
        if ($student = $this->studentModel->findById($studentId)) {
            $currentRisk = $student['risk_level'] ?? 'medium';
            if ($currentRisk !== $riskLevel) {
                $this->studentModel->updateRiskLevel($studentId, $riskLevel);
                
                // Create alert if risk level is high
                if ($riskLevel === 'high') {
                    $this->createRiskAlert($studentId, $riskFactors);
                }
            }
        }
        
        return [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'predicted_grade' => $predictedGrade,
            'predicted_gpa' => $predictedGpa,
            'current_gpa' => $currentGpa,
            'gpa_trend' => $gpaTrend,
            'gpa_change' => round($gpaChange, 2),
            'confidence' => $prediction['confidence'],
            'risk_level' => $riskLevel,
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
     * @param array $features Student features [gpa, attendance_rate, avg_grade, assignments_completed]
     * @param array $prediction Prediction results
     * @param int $studentId Student ID for checking enrollments
     * @return array Risk factors
     */
    private function identifyRiskFactors($features, $prediction, $studentId) {
        $riskFactors = [];
        
        if ($features[0] < 2.0 && $features[0] > 0) { // Low GPA (but not zero/empty)
            $riskFactors[] = 'Low GPA (' . number_format($features[0], 2) . ')';
        }
        
        if ($features[1] < 70 && $features[1] > 0) { // Low attendance (but not zero/empty)
            $riskFactors[] = 'Low attendance rate (' . number_format($features[1], 1) . '%)';
        }
        
        if ($features[2] < 60 && $features[2] > 0) { // Low average grade (but not zero/empty)
            $riskFactors[] = 'Low average grade (' . number_format($features[2], 2) . ')';
        }
        
        // Check assignments: only show as risk if student is enrolled in courses but has few/no assignments
        $db = \App\Core\Database::getInstance()->getConnection();
        $enrollmentsSql = "SELECT COUNT(*) FROM enrollments WHERE student_id = :student_id AND status = 'active'";
        $enrollStmt = $db->prepare($enrollmentsSql);
        $enrollStmt->execute([':student_id' => $studentId]);
        $enrolledCourses = (int)$enrollStmt->fetchColumn();
        
        // Only show incomplete assignments warning if:
        // 1. Student is enrolled in at least one course (so they should have assignments)
        // 2. Student has less than 3 assignments completed
        if ($enrolledCourses > 0 && $features[3] < 3) {
            if ($features[3] == 0) {
                $riskFactors[] = 'No assignments completed yet';
            } else {
                $riskFactors[] = 'Few assignments completed (' . $features[3] . ' completed)';
            }
        }
        // If student has 0 enrollments, don't show assignment risk factor
        
        if ($prediction['predicted_grade'] < 60) {
            $riskFactors[] = 'Predicted grade below passing (' . $prediction['predicted_grade'] . ')';
        }
        
        return $riskFactors;
    }

    /**
     * Calculate current GPA from all student grades (course-weighted, like real transcript)
     * @param int $studentId Student ID
     * @return float Current GPA (4.0 scale)
     */
    private function calculateCurrentGpa($studentId) {
        // Use the same calculation method as predictedGpa (course-weighted)
        $db = \App\Core\Database::getInstance()->getConnection();
        $enrollmentsSql = "SELECT course_id FROM enrollments WHERE student_id = :student_id AND status = 'active'";
        $enrollStmt = $db->prepare($enrollmentsSql);
        $enrollStmt->execute([':student_id' => $studentId]);
        $courseIds = $enrollStmt->fetchAll(\PDO::FETCH_COLUMN);
        
        if (empty($courseIds)) {
            // Fallback: use student's stored GPA if available
            $student = $this->studentModel->findById($studentId);
            return (float)($student['gpa'] ?? 0.0);
        }
        
        $courseGpas = [];
        $allGrades = $this->gradeModel->getGradesByStudent($studentId);
        
        foreach ($courseIds as $cid) {
            // Get actual grades for this course
            $courseGrades = array_filter($allGrades, function($g) use ($cid) {
                return $g['course_id'] == $cid;
            });
            
            if (!empty($courseGrades)) {
                // Calculate average grade percentage from actual grades in this course
                $totalGrade = 0;
                foreach ($courseGrades as $grade) {
                    $gradeValue = (float)($grade['grade'] ?? 0);
                    $maxGrade = (float)($grade['max_grade'] ?? 100);
                    $percentage = ($maxGrade > 0) ? ($gradeValue / $maxGrade) * 100 : 0;
                    $totalGrade += $percentage;
                }
                $averageGrade = $totalGrade / count($courseGrades);
                $courseGpas[] = $this->gradeToGpa($averageGrade);
            }
        }
        
        // Calculate overall GPA as average of course GPAs (like real transcript)
        if (!empty($courseGpas)) {
            return array_sum($courseGpas) / count($courseGpas);
        }
        
        // Fallback: use student's stored GPA if no grades
        $student = $this->studentModel->findById($studentId);
        return (float)($student['gpa'] ?? 0.0);
    }
    
    /**
     * Convert numeric percentage grade (0-100) to a 4.0 GPA scale.
     * Simple mapping: A=4.0 (90-100), B=3.0 (80-89), C=2.0 (70-79), D=1.0 (60-69), F=0.0 (<60)
     */
    private function gradeToGpa($grade) {
        if ($grade >= 90) {
            return 4.0;
        } elseif ($grade >= 80) {
            return 3.0;
        } elseif ($grade >= 70) {
            return 2.0;
        } elseif ($grade >= 60) {
            return 1.0;
        }
        return 0.0;
    }
    
    /**
     * Convert GPA (4.0 scale) back to approximate grade percentage
     * Used for display purposes when we only have GPA
     */
    private function gpaToGrade($gpa) {
        if ($gpa >= 3.5) {
            return 90 + (($gpa - 3.5) / 0.5) * 10; // 3.5-4.0 maps to 90-100
        } elseif ($gpa >= 3.0) {
            return 80 + (($gpa - 3.0) / 0.5) * 10; // 3.0-3.5 maps to 80-90
        } elseif ($gpa >= 2.0) {
            return 70 + (($gpa - 2.0) / 1.0) * 10; // 2.0-3.0 maps to 70-80
        } elseif ($gpa >= 1.0) {
            return 60 + (($gpa - 1.0) / 1.0) * 10; // 1.0-2.0 maps to 60-70
        }
        return $gpa * 60; // 0-1.0 maps to 0-60
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

