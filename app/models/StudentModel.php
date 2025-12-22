<?php
namespace App\Models;

use App\Core\Model;

class StudentModel extends Model {
    protected $table = 'students';
    
    public function findByUserId($userId) {
        return $this->findOne(['user_id' => $userId]);
    }
    
    public function getAtRiskStudents($riskLevel = 'high') {
        return $this->findAll(['risk_level' => $riskLevel]);
    }

    /**
     * Get at-risk students (with user name/email and course info) for a specific instructor
     * Used on the instructor dashboard to show meaningful names instead of only IDs.
     */
    public function getAtRiskStudentsByInstructor($instructorId, $riskLevel = 'high') {
        $sql = "SELECT DISTINCT 
                    s.id,
                    s.student_id,
                    s.gpa,
                    s.attendance_rate,
                    s.risk_level,
                    u.name,
                    u.email,
                    c.course_name,
                    c.course_code
                FROM {$this->table} s
                INNER JOIN users u ON s.user_id = u.id
                INNER JOIN enrollments e ON s.id = e.student_id
                INNER JOIN courses c ON e.course_id = c.id
                WHERE s.risk_level = :risk_level
                  AND e.status = 'active'
                  AND c.instructor_id = :instructor_id
                ORDER BY u.name, c.course_name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':risk_level' => $riskLevel,
            ':instructor_id' => $instructorId
        ]);

        return $stmt->fetchAll();
    }
    
    public function getStudentWithUser($studentId) {
        $sql = "SELECT s.*, u.name, u.email 
                FROM {$this->table} s 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE s.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $studentId]);
        return $stmt->fetch();
    }
    
    public function updateRiskLevel($studentId, $riskLevel) {
        return $this->update($studentId, ['risk_level' => $riskLevel]);
    }
}






