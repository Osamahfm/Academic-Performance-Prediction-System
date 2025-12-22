<?php
namespace App\Models;

use App\Core\Model;

class CourseModel extends Model {
    protected $table = 'courses';
    
    public function getCoursesByInstructor($instructorId) {
        return $this->findAll(['instructor_id' => $instructorId]);
    }
    
    public function getCourseWithInstructor($courseId) {
        $sql = "SELECT c.*, u.name as instructor_name, u.email as instructor_email 
                FROM {$this->table} c 
                LEFT JOIN users u ON c.instructor_id = u.id 
                WHERE c.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $courseId]);
        return $stmt->fetch();
    }
    
    /**
     * Get students enrolled in instructor's courses
     */
    public function getEnrolledStudentsByInstructor($instructorId) {
        $sql = "SELECT DISTINCT s.id, s.student_id, u.name as student_name, u.email as student_email,
                       c.id as course_id, c.course_name, c.course_code
                FROM students s
                INNER JOIN users u ON s.user_id = u.id
                INNER JOIN enrollments e ON s.id = e.student_id
                INNER JOIN courses c ON e.course_id = c.id
                WHERE c.instructor_id = :instructor_id 
                AND e.status = 'active'
                ORDER BY c.course_name, u.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':instructor_id' => $instructorId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get students enrolled in a specific course (for instructor)
     */
    public function getEnrolledStudentsByCourse($courseId, $instructorId = null) {
        $sql = "SELECT s.id, s.student_id, u.name as student_name, u.email as student_email
                FROM students s
                INNER JOIN users u ON s.user_id = u.id
                INNER JOIN enrollments e ON s.id = e.student_id
                INNER JOIN courses c ON e.course_id = c.id
                WHERE e.course_id = :course_id 
                AND e.status = 'active'";
        
        $params = [':course_id' => $courseId];
        
        // Verify instructor owns the course if instructor_id is provided
        if ($instructorId !== null) {
            $sql .= " AND c.instructor_id = :instructor_id";
            $params[':instructor_id'] = $instructorId;
        }
        
        $sql .= " ORDER BY u.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Verify if instructor owns the course
     */
    public function isInstructorCourse($courseId, $instructorId) {
        $course = $this->findOne(['id' => $courseId, 'instructor_id' => $instructorId]);
        return $course !== false;
    }
}





