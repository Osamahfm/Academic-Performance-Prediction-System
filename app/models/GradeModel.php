<?php
namespace App\Models;

use App\Core\Model;

class GradeModel extends Model {
    protected $table = 'grades';
    
    public function getGradesByStudent($studentId) {
        $sql = "SELECT g.*, c.course_name, c.course_code 
                FROM {$this->table} g 
                INNER JOIN courses c ON g.course_id = c.id 
                WHERE g.student_id = :student_id 
                ORDER BY g.date_recorded DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }
    
    public function getGradesByCourse($courseId) {
        $sql = "SELECT g.*, s.student_id, u.name as student_name 
                FROM {$this->table} g 
                INNER JOIN students s ON g.student_id = s.id 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE g.course_id = :course_id 
                ORDER BY g.date_recorded DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        return $stmt->fetchAll();
    }
    
    public function getAverageGrade($studentId, $courseId = null) {
        $sql = "SELECT AVG(grade) as avg_grade FROM {$this->table} WHERE student_id = :student_id";
        $params = [':student_id' => $studentId];
        
        if ($courseId) {
            $sql .= " AND course_id = :course_id";
            $params[':course_id'] = $courseId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['avg_grade'] ?? 0;
    }
    
    /**
     * Trigger KNN prediction after grade is added
     * This is called automatically when a grade is created
     */
    public function triggerPrediction($studentId, $courseId = null) {
        try {
            // Only trigger if student has GPA and attendance
            $studentSql = "SELECT id, gpa, attendance_rate FROM students WHERE id = :id";
            $studentStmt = $this->db->prepare($studentSql);
            $studentStmt->execute([':id' => $studentId]);
            $student = $studentStmt->fetch();
            
            if ($student && $student['gpa'] !== null && $student['attendance_rate'] !== null) {
                // Run prediction in background (don't block the request)
                $predictionService = new \App\Services\PredictionService();
                $predictionService->predictPerformance($studentId, $courseId);
            }
        } catch (\Exception $e) {
            // Silently fail - prediction is not critical for grade creation
            // Log error if needed: error_log("KNN prediction failed: " . $e->getMessage());
        }
    }
}



