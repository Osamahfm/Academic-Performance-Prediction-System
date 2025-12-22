<?php
namespace App\Core\Strategy;

use App\Core\Validator;

/**
 * Strategy Pattern - Validation Strategy Interface
 * Different validation strategies for different entity types
 */
interface ValidationStrategy {
    public function validate($data);
    public function getErrors();
}

/**
 * User Validation Strategy
 */
class UserValidationStrategy implements ValidationStrategy {
    private $validator;
    private $errors = [];
    
    public function validate($data) {
        $this->validator = new Validator($data);
        
        // Required fields
        $this->validator->required('name', 'Name is required.');
        $this->validator->required('email', 'Email is required.');
        $this->validator->required('password', 'Password is required.');
        $this->validator->required('role', 'Role is required.');
        
        // Email validation
        $this->validator->email('email', 'Invalid email format.');
        
        // Length validations
        $this->validator->minLength('name', 2, 'Name must be at least 2 characters.');
        $this->validator->maxLength('name', 100, 'Name cannot exceed 100 characters.');
        $this->validator->minLength('password', 6, 'Password must be at least 6 characters.');
        $this->validator->maxLength('password', 255, 'Password cannot exceed 255 characters.');
        
        // Role validation
        $this->validator->in('role', ['admin', 'instructor', 'student'], 'Invalid role selected.');
        
        $this->errors = $this->validator->getErrors();
        return $this->validator->isValid();
    }
    
    public function getErrors() {
        return $this->errors;
    }
}

/**
 * Course Validation Strategy
 */
class CourseValidationStrategy implements ValidationStrategy {
    private $validator;
    private $errors = [];
    
    public function validate($data) {
        $this->validator = new Validator($data);
        
        $this->validator->required('course_code', 'Course code is required.');
        $this->validator->required('course_name', 'Course name is required.');
        
        $this->validator->minLength('course_code', 3, 'Course code must be at least 3 characters.');
        $this->validator->maxLength('course_code', 20, 'Course code cannot exceed 20 characters.');
        $this->validator->maxLength('course_name', 100, 'Course name cannot exceed 100 characters.');
        
        if (isset($data['credits'])) {
            $this->validator->numeric('credits', 'Credits must be a number.');
            $this->validator->between('credits', 1, 10, 'Credits must be between 1 and 10.');
        }
        
        $this->errors = $this->validator->getErrors();
        return $this->validator->isValid();
    }
    
    public function getErrors() {
        return $this->errors;
    }
}

/**
 * Grade Validation Strategy
 */
class GradeValidationStrategy implements ValidationStrategy {
    private $validator;
    private $errors = [];
    
    public function validate($data) {
        $this->validator = new Validator($data);
        
        $this->validator->required('student_id', 'Student ID is required.');
        $this->validator->required('course_id', 'Course ID is required.');
        $this->validator->required('grade', 'Grade is required.');
        $this->validator->required('max_grade', 'Maximum grade is required.');
        $this->validator->required('assignment_type', 'Assignment type is required.');
        
        $this->validator->numeric('grade', 'Grade must be a number.');
        $this->validator->numeric('max_grade', 'Maximum grade must be a number.');
        
        if (isset($data['grade']) && isset($data['max_grade'])) {
            $grade = floatval($data['grade']);
            $maxGrade = floatval($data['max_grade']);
            
            if ($grade < 0) {
                $this->errors['grade'] = 'Grade cannot be negative.';
            }
            if ($maxGrade <= 0) {
                $this->errors['max_grade'] = 'Maximum grade must be greater than 0.';
            }
            if ($grade > $maxGrade) {
                $this->errors['grade'] = 'Grade cannot exceed maximum grade.';
            }
        }
        
        $this->validator->in('assignment_type', ['quiz', 'assignment', 'exam', 'project', 'participation'], 
            'Invalid assignment type.');
        
        $this->errors = array_merge($this->errors, $this->validator->getErrors());
        return empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
}

/**
 * Contact Validation Strategy
 */
class ContactValidationStrategy implements ValidationStrategy {
    private $validator;
    private $errors = [];
    
    public function validate($data) {
        $this->validator = new Validator($data);
        
        $this->validator->required('name', 'Name is required.');
        $this->validator->required('email', 'Email is required.');
        $this->validator->required('message', 'Message is required.');
        
        $this->validator->email('email', 'Invalid email format.');
        $this->validator->minLength('name', 2, 'Name must be at least 2 characters.');
        $this->validator->maxLength('name', 100, 'Name cannot exceed 100 characters.');
        $this->validator->minLength('message', 10, 'Message must be at least 10 characters.');
        $this->validator->maxLength('message', 2000, 'Message cannot exceed 2000 characters.');
        
        if (isset($data['subject'])) {
            $this->validator->maxLength('subject', 200, 'Subject cannot exceed 200 characters.');
        }
        
        $this->errors = $this->validator->getErrors();
        return $this->validator->isValid();
    }
    
    public function getErrors() {
        return $this->errors;
    }
}








