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





