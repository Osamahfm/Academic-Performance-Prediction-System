<?php
/**
 * Unit Tests for Validation Strategy Classes
 * Tests validation logic for User, Course, Grade, and Contact entities
 * Note: Dependencies are loaded by test runner
 */

use App\Core\Strategy\UserValidationStrategy;
use App\Core\Strategy\CourseValidationStrategy;
use App\Core\Strategy\GradeValidationStrategy;
use App\Core\Strategy\ContactValidationStrategy;

class ValidationStrategyTest {
    
    // ========== User Validation Strategy Tests ==========
    
    /**
     * Test: Valid user data (happy path)
     */
    public function testUserValidationValid() {
        $strategy = new UserValidationStrategy();
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'student'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === true, 'Valid user data should pass validation');
        assert(empty($errors), 'No errors should be present for valid data');
        
        echo "✓ User validation - valid data test passed\n";
    }
    
    /**
     * Test: User validation - missing required fields (edge case)
     */
    public function testUserValidationMissingFields() {
        $strategy = new UserValidationStrategy();
        $data = [
            'name' => 'John Doe'
            // Missing email, password, role
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Missing fields should fail validation');
        assert(isset($errors['email']), 'Email error should be present');
        assert(isset($errors['password']), 'Password error should be present');
        assert(isset($errors['role']), 'Role error should be present');
        
        echo "✓ User validation - missing fields test passed\n";
    }
    
    /**
     * Test: User validation - invalid email (edge case)
     */
    public function testUserValidationInvalidEmail() {
        $strategy = new UserValidationStrategy();
        $data = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'role' => 'student'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Invalid email should fail validation');
        assert(isset($errors['email']), 'Email error should be present');
        
        echo "✓ User validation - invalid email test passed\n";
    }
    
    /**
     * Test: User validation - short password (edge case)
     */
    public function testUserValidationShortPassword() {
        $strategy = new UserValidationStrategy();
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '12345', // Less than 6 characters
            'role' => 'student'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Short password should fail validation');
        assert(isset($errors['password']), 'Password error should be present');
        
        echo "✓ User validation - short password test passed\n";
    }
    
    /**
     * Test: User validation - invalid role (edge case)
     */
    public function testUserValidationInvalidRole() {
        $strategy = new UserValidationStrategy();
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'invalid_role'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Invalid role should fail validation');
        assert(isset($errors['role']), 'Role error should be present');
        
        echo "✓ User validation - invalid role test passed\n";
    }
    
    // ========== Course Validation Strategy Tests ==========
    
    /**
     * Test: Valid course data (happy path)
     */
    public function testCourseValidationValid() {
        $strategy = new CourseValidationStrategy();
        $data = [
            'course_code' => 'CS101',
            'course_name' => 'Introduction to Computer Science',
            'credits' => 3
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === true, 'Valid course data should pass validation');
        assert(empty($errors), 'No errors should be present');
        
        echo "✓ Course validation - valid data test passed\n";
    }
    
    /**
     * Test: Course validation - missing required fields (edge case)
     */
    public function testCourseValidationMissingFields() {
        $strategy = new CourseValidationStrategy();
        $data = [
            'course_code' => 'CS101'
            // Missing course_name
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Missing fields should fail validation');
        assert(isset($errors['course_name']), 'Course name error should be present');
        
        echo "✓ Course validation - missing fields test passed\n";
    }
    
    /**
     * Test: Course validation - invalid credits (edge case)
     */
    public function testCourseValidationInvalidCredits() {
        $strategy = new CourseValidationStrategy();
        $data = [
            'course_code' => 'CS101',
            'course_name' => 'Introduction to Computer Science',
            'credits' => 15 // Out of range (1-10)
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Invalid credits should fail validation');
        assert(isset($errors['credits']), 'Credits error should be present');
        
        echo "✓ Course validation - invalid credits test passed\n";
    }
    
    /**
     * Test: Course validation - short course code (edge case)
     */
    public function testCourseValidationShortCode() {
        $strategy = new CourseValidationStrategy();
        $data = [
            'course_code' => 'CS', // Less than 3 characters
            'course_name' => 'Introduction to Computer Science'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Short course code should fail validation');
        assert(isset($errors['course_code']), 'Course code error should be present');
        
        echo "✓ Course validation - short code test passed\n";
    }
    
    // ========== Grade Validation Strategy Tests ==========
    
    /**
     * Test: Valid grade data (happy path)
     */
    public function testGradeValidationValid() {
        $strategy = new GradeValidationStrategy();
        $data = [
            'student_id' => 1,
            'course_id' => 1,
            'grade' => 85.5,
            'max_grade' => 100,
            'assignment_type' => 'exam'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === true, 'Valid grade data should pass validation');
        assert(empty($errors), 'No errors should be present');
        
        echo "✓ Grade validation - valid data test passed\n";
    }
    
    /**
     * Test: Grade validation - grade exceeds max (edge case)
     */
    public function testGradeValidationExceedsMax() {
        $strategy = new GradeValidationStrategy();
        $data = [
            'student_id' => 1,
            'course_id' => 1,
            'grade' => 150, // Exceeds max_grade
            'max_grade' => 100,
            'assignment_type' => 'exam'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Grade exceeding max should fail validation');
        assert(isset($errors['grade']), 'Grade error should be present');
        
        echo "✓ Grade validation - exceeds max test passed\n";
    }
    
    /**
     * Test: Grade validation - negative grade (edge case)
     */
    public function testGradeValidationNegativeGrade() {
        $strategy = new GradeValidationStrategy();
        $data = [
            'student_id' => 1,
            'course_id' => 1,
            'grade' => -10, // Negative grade
            'max_grade' => 100,
            'assignment_type' => 'exam'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Negative grade should fail validation');
        assert(isset($errors['grade']), 'Grade error should be present');
        
        echo "✓ Grade validation - negative grade test passed\n";
    }
    
    /**
     * Test: Grade validation - invalid assignment type (edge case)
     */
    public function testGradeValidationInvalidType() {
        $strategy = new GradeValidationStrategy();
        $data = [
            'student_id' => 1,
            'course_id' => 1,
            'grade' => 85,
            'max_grade' => 100,
            'assignment_type' => 'invalid_type'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Invalid assignment type should fail validation');
        assert(isset($errors['assignment_type']), 'Assignment type error should be present');
        
        echo "✓ Grade validation - invalid type test passed\n";
    }
    
    /**
     * Test: Grade validation - zero max grade (edge case)
     */
    public function testGradeValidationZeroMaxGrade() {
        $strategy = new GradeValidationStrategy();
        $data = [
            'student_id' => 1,
            'course_id' => 1,
            'grade' => 50,
            'max_grade' => 0, // Invalid max grade
            'assignment_type' => 'exam'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Zero max grade should fail validation');
        assert(isset($errors['max_grade']), 'Max grade error should be present');
        
        echo "✓ Grade validation - zero max grade test passed\n";
    }
    
    // ========== Contact Validation Strategy Tests ==========
    
    /**
     * Test: Valid contact data (happy path)
     */
    public function testContactValidationValid() {
        $strategy = new ContactValidationStrategy();
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message with enough characters.',
            'subject' => 'Test Subject'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === true, 'Valid contact data should pass validation');
        assert(empty($errors), 'No errors should be present');
        
        echo "✓ Contact validation - valid data test passed\n";
    }
    
    /**
     * Test: Contact validation - short message (edge case)
     */
    public function testContactValidationShortMessage() {
        $strategy = new ContactValidationStrategy();
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Short' // Less than 10 characters
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Short message should fail validation');
        assert(isset($errors['message']), 'Message error should be present');
        
        echo "✓ Contact validation - short message test passed\n";
    }
    
    /**
     * Test: Contact validation - invalid email (edge case)
     */
    public function testContactValidationInvalidEmail() {
        $strategy = new ContactValidationStrategy();
        $data = [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'message' => 'This is a valid message with enough characters.'
        ];
        
        $isValid = $strategy->validate($data);
        $errors = $strategy->getErrors();
        
        assert($isValid === false, 'Invalid email should fail validation');
        assert(isset($errors['email']), 'Email error should be present');
        
        echo "✓ Contact validation - invalid email test passed\n";
    }
    
    /**
     * Run all tests
     */
    public function runAll() {
        echo "Running Validation Strategy Unit Tests...\n\n";
        
        try {
            // User validation tests
            $this->testUserValidationValid();
            $this->testUserValidationMissingFields();
            $this->testUserValidationInvalidEmail();
            $this->testUserValidationShortPassword();
            $this->testUserValidationInvalidRole();
            
            // Course validation tests
            $this->testCourseValidationValid();
            $this->testCourseValidationMissingFields();
            $this->testCourseValidationInvalidCredits();
            $this->testCourseValidationShortCode();
            
            // Grade validation tests
            $this->testGradeValidationValid();
            $this->testGradeValidationExceedsMax();
            $this->testGradeValidationNegativeGrade();
            $this->testGradeValidationInvalidType();
            $this->testGradeValidationZeroMaxGrade();
            
            // Contact validation tests
            $this->testContactValidationValid();
            $this->testContactValidationShortMessage();
            $this->testContactValidationInvalidEmail();
            
            echo "\n✅ All Validation Strategy tests passed!\n";
        } catch (Exception $e) {
            echo "\n❌ Test failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

