<?php
namespace App\Models;

use App\Core\Model;

class AlertModel extends Model {
    protected $table = 'alerts';
    
    /**
     * Get alerts for students in instructor's courses
     */
    public function getAlertsByInstructor($instructorId) {
        // Get all alerts for students enrolled in instructor's courses
        $sql = "SELECT a.*, s.student_id, s.user_id, u.name as student_name, 
                       c.course_name, c.course_code
                FROM {$this->table} a
                INNER JOIN students s ON a.student_id = s.id
                INNER JOIN users u ON s.user_id = u.id
                INNER JOIN enrollments e ON s.id = e.student_id
                INNER JOIN courses c ON e.course_id = c.id
                WHERE a.status = 'active'
                AND c.instructor_id = :instructor_id
                ORDER BY a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':instructor_id' => $instructorId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get count of active alerts for instructor
     */
    public function getAlertCountByInstructor($instructorId) {
        $alerts = $this->getAlertsByInstructor($instructorId);
        return count($alerts);
    }
    
    /**
     * Get all active alerts
     */
    public function getActiveAlerts() {
        return $this->findAll(['status' => 'active'], 'created_at DESC');
    }
    
    /**
     * Get alerts for a specific student
     */
    public function getAlertsByStudent($studentId) {
        return $this->findAll(['student_id' => $studentId, 'status' => 'active'], 'created_at DESC');
    }
    
    /**
     * Mark alert as resolved
     */
    public function resolveAlert($id) {
        return $this->update($id, ['status' => 'resolved']);
    }
    
    /**
     * Dismiss alert
     */
    public function dismissAlert($id) {
        return $this->update($id, ['status' => 'dismissed']);
    }
    
    /**
     * Create alert
     */
    public function createAlert($studentId, $alertType, $message, $severity = 'medium') {
        return $this->create([
            'student_id' => $studentId,
            'alert_type' => $alertType,
            'message' => $message,
            'severity' => $severity,
            'status' => 'active'
        ]);
    }
}

