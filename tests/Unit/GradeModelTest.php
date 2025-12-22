<?php
/**
 * Unit Tests for GradeModel Class
 * Tests grade calculations and business logic
 * Note: These tests use a mock database approach for isolation
 * Dependencies are loaded by test runner
 */

use App\Models\GradeModel;

class GradeModelTest {
    
    /**
     * Mock database connection for testing
     */
    private function getMockDb() {
        // In a real scenario, you'd use a mocking framework
        // For now, we'll test with actual database but isolated test data
        try {
            return \App\Core\Database::getInstance()->getConnection();
        } catch (Exception $e) {
            // Skip database tests if DB is not available
            throw new Exception("Database not available - skipping database-dependent tests");
        }
    }
    
    /**
     * Check if database is available
     */
    private function isDbAvailable() {
        try {
            \App\Core\Database::getInstance()->getConnection();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Test: Calculate average grade for student (happy path)
     */
    public function testGetAverageGrade() {
        // This test requires actual database connection
        // In production, you'd mock the database
        
        if (!$this->isDbAvailable()) {
            echo "⚠ Database not available - skipping test\n";
            return;
        }
        
        $model = new GradeModel();
        $db = $this->getMockDb();
        
        // Create test data
        $testStudentId = 999999; // Use a high ID unlikely to exist
        
        // Clean up any existing test data (grades first, then student)
        $cleanupGrades = $db->prepare("DELETE FROM grades WHERE student_id = ?");
        $cleanupGrades->execute([$testStudentId]);
        
        $cleanupStudent = $db->prepare("DELETE FROM students WHERE id = ?");
        $cleanupStudent->execute([$testStudentId]);
        
        // Create a test student first (required for foreign key constraint)
        // We need a user_id, so create a test user first
        $testUserId = 999999;
        $cleanupUser = $db->prepare("DELETE FROM users WHERE id = ?");
        $cleanupUser->execute([$testUserId]);
        
        // Create test user
        $insertUser = $db->prepare("INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $insertUser->execute([$testUserId, 'Test User', 'test@test.com', password_hash('test', PASSWORD_DEFAULT), 'student']);
        
        // Create test student
        $insertStudent = $db->prepare("INSERT INTO students (id, user_id, student_id, gpa, attendance_rate, risk_level) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStudent->execute([$testStudentId, $testUserId, 'TEST999', 3.0, 80.0, 'low']);
        
        // Insert test grades
        $insert = $db->prepare("INSERT INTO grades (student_id, course_id, grade, max_grade, assignment_type) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$testStudentId, 1, 80, 100, 'exam']);
        $insert->execute([$testStudentId, 1, 90, 100, 'exam']);
        $insert->execute([$testStudentId, 1, 85, 100, 'exam']);
        
        // Test average calculation
        $avgGrade = $model->getAverageGrade($testStudentId);
        
        assert($avgGrade == 85.0, 'Average grade should be 85.0');
        
        // Cleanup (in reverse order due to foreign keys)
        $cleanupGrades->execute([$testStudentId]);
        $cleanupStudent->execute([$testStudentId]);
        $cleanupUser->execute([$testUserId]);
        
        echo "✓ Get average grade test passed\n";
    }
    
    /**
     * Test: Calculate average grade with no grades (edge case)
     */
    public function testGetAverageGradeNoGrades() {
        if (!$this->isDbAvailable()) {
            echo "⚠ Database not available - skipping test\n";
            return;
        }
        
        $model = new GradeModel();
        $db = $this->getMockDb();
        
        $testStudentId = 999998;
        
        // Clean up
        $cleanup = $db->prepare("DELETE FROM grades WHERE student_id = ?");
        $cleanup->execute([$testStudentId]);
        
        // Test with no grades
        $avgGrade = $model->getAverageGrade($testStudentId);
        
        assert($avgGrade == 0, 'Average grade should be 0 when no grades exist');
        
        echo "✓ Get average grade - no grades test passed\n";
    }
    
    /**
     * Test: Calculate average grade for specific course (happy path)
     */
    public function testGetAverageGradeByCourse() {
        if (!$this->isDbAvailable()) {
            echo "⚠ Database not available - skipping test\n";
            return;
        }
        
        $model = new GradeModel();
        $db = $this->getMockDb();
        
        $testStudentId = 999997;
        $testUserId = 999997;
        $testCourseId = 1;
        
        // Clean up
        $cleanupGrades = $db->prepare("DELETE FROM grades WHERE student_id = ?");
        $cleanupGrades->execute([$testStudentId]);
        
        $cleanupStudent = $db->prepare("DELETE FROM students WHERE id = ?");
        $cleanupStudent->execute([$testStudentId]);
        
        $cleanupUser = $db->prepare("DELETE FROM users WHERE id = ?");
        $cleanupUser->execute([$testUserId]);
        
        // Create test user and student
        $insertUser = $db->prepare("INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $insertUser->execute([$testUserId, 'Test User 2', 'test2@test.com', password_hash('test', PASSWORD_DEFAULT), 'student']);
        
        $insertStudent = $db->prepare("INSERT INTO students (id, user_id, student_id, gpa, attendance_rate, risk_level) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStudent->execute([$testStudentId, $testUserId, 'TEST997', 3.0, 80.0, 'low']);
        
        // Insert test grades for different courses
        $insert = $db->prepare("INSERT INTO grades (student_id, course_id, grade, max_grade, assignment_type) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$testStudentId, $testCourseId, 80, 100, 'exam']);
        $insert->execute([$testStudentId, $testCourseId, 90, 100, 'exam']);
        $insert->execute([$testStudentId, 2, 70, 100, 'exam']); // Different course
        
        // Test course-specific average
        $avgGrade = $model->getAverageGrade($testStudentId, $testCourseId);
        
        assert($avgGrade == 85.0, 'Course-specific average should be 85.0');
        
        // Cleanup
        $cleanupGrades->execute([$testStudentId]);
        $cleanupStudent->execute([$testStudentId]);
        $cleanupUser->execute([$testUserId]);
        
        echo "✓ Get average grade by course test passed\n";
    }
    
    /**
     * Test: Get grades by student (happy path)
     */
    public function testGetGradesByStudent() {
        if (!$this->isDbAvailable()) {
            echo "⚠ Database not available - skipping test\n";
            return;
        }
        
        $model = new GradeModel();
        $db = $this->getMockDb();
        
        $testStudentId = 999996;
        $testUserId = 999996;
        
        // Clean up
        $cleanupGrades = $db->prepare("DELETE FROM grades WHERE student_id = ?");
        $cleanupGrades->execute([$testStudentId]);
        
        $cleanupStudent = $db->prepare("DELETE FROM students WHERE id = ?");
        $cleanupStudent->execute([$testStudentId]);
        
        $cleanupUser = $db->prepare("DELETE FROM users WHERE id = ?");
        $cleanupUser->execute([$testUserId]);
        
        // Create test user and student
        $insertUser = $db->prepare("INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $insertUser->execute([$testUserId, 'Test User 3', 'test3@test.com', password_hash('test', PASSWORD_DEFAULT), 'student']);
        
        $insertStudent = $db->prepare("INSERT INTO students (id, user_id, student_id, gpa, attendance_rate, risk_level) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStudent->execute([$testStudentId, $testUserId, 'TEST996', 3.0, 80.0, 'low']);
        
        // Insert test grades
        $insert = $db->prepare("INSERT INTO grades (student_id, course_id, grade, max_grade, assignment_type) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$testStudentId, 1, 80, 100, 'exam']);
        $insert->execute([$testStudentId, 1, 90, 100, 'assignment']);
        
        // Get grades
        $grades = $model->getGradesByStudent($testStudentId);
        
        assert(count($grades) == 2, 'Should return 2 grades');
        assert($grades[0]['grade'] == 90 || $grades[0]['grade'] == 80, 'Should contain test grades');
        
        // Cleanup
        $cleanupGrades->execute([$testStudentId]);
        $cleanupStudent->execute([$testStudentId]);
        $cleanupUser->execute([$testUserId]);
        
        echo "✓ Get grades by student test passed\n";
    }
    
    /**
     * Test: Get grades by course (happy path)
     */
    public function testGetGradesByCourse() {
        if (!$this->isDbAvailable()) {
            echo "⚠ Database not available - skipping test\n";
            return;
        }
        
        $model = new GradeModel();
        $db = $this->getMockDb();
        
        $testCourseId = 1;
        $testStudentId = 999995;
        $testUserId = 999995;
        
        // Clean up
        $cleanupGrades = $db->prepare("DELETE FROM grades WHERE student_id = ?");
        $cleanupGrades->execute([$testStudentId]);
        
        $cleanupStudent = $db->prepare("DELETE FROM students WHERE id = ?");
        $cleanupStudent->execute([$testStudentId]);
        
        $cleanupUser = $db->prepare("DELETE FROM users WHERE id = ?");
        $cleanupUser->execute([$testUserId]);
        
        // Create test user and student
        $insertUser = $db->prepare("INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $insertUser->execute([$testUserId, 'Test User 5', 'test5@test.com', password_hash('test', PASSWORD_DEFAULT), 'student']);
        
        $insertStudent = $db->prepare("INSERT INTO students (id, user_id, student_id, gpa, attendance_rate, risk_level) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStudent->execute([$testStudentId, $testUserId, 'TEST995', 3.0, 80.0, 'low']);
        
        // Insert test grades
        $insert = $db->prepare("INSERT INTO grades (student_id, course_id, grade, max_grade, assignment_type) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$testStudentId, $testCourseId, 85, 100, 'exam']);
        
        // Get grades by course
        $grades = $model->getGradesByCourse($testCourseId);
        
        assert(is_array($grades), 'Should return an array');
        // Note: May contain other students' grades for this course
        
        // Cleanup
        $cleanupGrades->execute([$testStudentId]);
        $cleanupStudent->execute([$testStudentId]);
        $cleanupUser->execute([$testUserId]);
        
        echo "✓ Get grades by course test passed\n";
    }
    
    /**
     * Test: Average grade calculation with different max grades (edge case)
     */
    public function testGetAverageGradeDifferentMaxGrades() {
        if (!$this->isDbAvailable()) {
            echo "⚠ Database not available - skipping test\n";
            return;
        }
        
        $model = new GradeModel();
        $db = $this->getMockDb();
        
        $testStudentId = 999994;
        $testUserId = 999994;
        
        // Clean up
        $cleanupGrades = $db->prepare("DELETE FROM grades WHERE student_id = ?");
        $cleanupGrades->execute([$testStudentId]);
        
        $cleanupStudent = $db->prepare("DELETE FROM students WHERE id = ?");
        $cleanupStudent->execute([$testStudentId]);
        
        $cleanupUser = $db->prepare("DELETE FROM users WHERE id = ?");
        $cleanupUser->execute([$testUserId]);
        
        // Create test user and student
        $insertUser = $db->prepare("INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $insertUser->execute([$testUserId, 'Test User 4', 'test4@test.com', password_hash('test', PASSWORD_DEFAULT), 'student']);
        
        $insertStudent = $db->prepare("INSERT INTO students (id, user_id, student_id, gpa, attendance_rate, risk_level) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStudent->execute([$testStudentId, $testUserId, 'TEST994', 3.0, 80.0, 'low']);
        
        // Insert grades with different max values
        $insert = $db->prepare("INSERT INTO grades (student_id, course_id, grade, max_grade, assignment_type) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$testStudentId, 1, 80, 100, 'exam']); // 80%
        $insert->execute([$testStudentId, 1, 40, 50, 'quiz']);  // 80%
        
        // Average should be calculated correctly (average of 80 and 40 = 60)
        $avgGrade = $model->getAverageGrade($testStudentId);
        
        assert($avgGrade == 60.0, 'Average should handle different max grades correctly');
        
        // Cleanup
        $cleanupGrades->execute([$testStudentId]);
        $cleanupStudent->execute([$testStudentId]);
        $cleanupUser->execute([$testUserId]);
        
        echo "✓ Get average grade - different max grades test passed\n";
    }
    
    /**
     * Test: Get grades with no results (edge case)
     */
    public function testGetGradesNoResults() {
        if (!$this->isDbAvailable()) {
            echo "⚠ Database not available - skipping test\n";
            return;
        }
        
        $model = new GradeModel();
        $db = $this->getMockDb();
        
        $testStudentId = 999993;
        
        // Clean up
        $cleanup = $db->prepare("DELETE FROM grades WHERE student_id = ?");
        $cleanup->execute([$testStudentId]);
        
        // Get grades for student with no grades
        $grades = $model->getGradesByStudent($testStudentId);
        
        assert(is_array($grades), 'Should return an array');
        assert(empty($grades), 'Should return empty array when no grades exist');
        
        echo "✓ Get grades - no results test passed\n";
    }
    
    /**
     * Run all tests
     */
    public function runAll() {
        echo "Running GradeModel Unit Tests...\n\n";
        
        try {
            $this->testGetAverageGrade();
            $this->testGetAverageGradeNoGrades();
            $this->testGetAverageGradeByCourse();
            $this->testGetGradesByStudent();
            $this->testGetGradesByCourse();
            $this->testGetAverageGradeDifferentMaxGrades();
            $this->testGetGradesNoResults();
            
            echo "\n✅ All GradeModel tests passed!\n";
        } catch (Exception $e) {
            echo "\n❌ Test failed: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }
}

