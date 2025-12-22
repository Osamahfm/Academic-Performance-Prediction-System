<?php
namespace App\Models;

use App\Core\Model;

class PredictionModel extends Model {
    protected $table = 'predictions';
    
    /**
     * Get predictions for a student
     */
    public function getPredictionsByStudent($studentId) {
        $sql = "SELECT p.*, c.course_name, c.course_code
                FROM {$this->table} p
                LEFT JOIN courses c ON p.course_id = c.id
                WHERE p.student_id = :student_id
                ORDER BY p.prediction_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get predictions for a course
     */
    public function getPredictionsByCourse($courseId) {
        $sql = "SELECT p.*, s.student_id, u.name as student_name
                FROM {$this->table} p
                INNER JOIN students s ON p.student_id = s.id
                INNER JOIN users u ON s.user_id = u.id
                WHERE p.course_id = :course_id
                ORDER BY p.prediction_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':course_id' => $courseId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Save prediction
     */
    public function savePrediction($studentId, $courseId, $predictedGrade, $confidenceScore, $riskFactors = '') {
        // Check if prediction already exists (handle NULL course_id)
        $sql = "SELECT * FROM {$this->table} WHERE student_id = :student_id";
        $params = [':student_id' => $studentId];
        
        if ($courseId !== null) {
            $sql .= " AND course_id = :course_id";
            $params[':course_id'] = $courseId;
        } else {
            $sql .= " AND course_id IS NULL";
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $existing = $stmt->fetch();
        
        $data = [
            'student_id' => $studentId,
            'course_id' => $courseId,
            'predicted_grade' => $predictedGrade,
            'confidence_score' => $confidenceScore,
            'risk_factors' => $riskFactors
        ];
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->create($data);
        }
    }
    
    /**
     * Get latest prediction for student-course combination
     */
    public function getLatestPrediction($studentId, $courseId = null) {
        $conditions = ['student_id' => $studentId];
        if ($courseId) {
            $conditions['course_id'] = $courseId;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE student_id = :student_id";
        $params = [':student_id' => $studentId];
        
        if ($courseId) {
            $sql .= " AND course_id = :course_id";
            $params[':course_id'] = $courseId;
        }
        
        $sql .= " ORDER BY prediction_date DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}



