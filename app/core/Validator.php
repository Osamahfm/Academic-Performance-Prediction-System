<?php
namespace App\Core;

/**
 * Validation Class - Strict input validation
 * Implements validation rules for all user inputs
 */
class Validator {
    private $errors = [];
    private $data = [];
    
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * Validate required fields
     */
    public function required($field, $message = null) {
        if (empty($this->data[$field])) {
            $this->errors[$field] = $message ?? "The {$field} field is required.";
            return false;
        }
        return true;
    }
    
    /**
     * Validate email format
     */
    public function email($field, $message = null) {
        if (!empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = $message ?? "The {$field} must be a valid email address.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate minimum length
     */
    public function minLength($field, $min, $message = null) {
        if (!empty($this->data[$field])) {
            if (strlen($this->data[$field]) < $min) {
                $this->errors[$field] = $message ?? "The {$field} must be at least {$min} characters.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate maximum length
     */
    public function maxLength($field, $max, $message = null) {
        if (!empty($this->data[$field])) {
            if (strlen($this->data[$field]) > $max) {
                $this->errors[$field] = $message ?? "The {$field} must not exceed {$max} characters.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate numeric value
     */
    public function numeric($field, $message = null) {
        if (!empty($this->data[$field])) {
            if (!is_numeric($this->data[$field])) {
                $this->errors[$field] = $message ?? "The {$field} must be a number.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate value is within range
     */
    public function between($field, $min, $max, $message = null) {
        if (!empty($this->data[$field])) {
            $value = floatval($this->data[$field]);
            if ($value < $min || $value > $max) {
                $this->errors[$field] = $message ?? "The {$field} must be between {$min} and {$max}.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate value matches another field
     */
    public function match($field, $matchField, $message = null) {
        if (!empty($this->data[$field])) {
            if ($this->data[$field] !== $this->data[$matchField]) {
                $this->errors[$field] = $message ?? "The {$field} does not match {$matchField}.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate value is in allowed list
     */
    public function in($field, $allowed, $message = null) {
        if (!empty($this->data[$field])) {
            if (!in_array($this->data[$field], $allowed)) {
                $this->errors[$field] = $message ?? "The {$field} is not valid.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Validate URL format
     */
    public function url($field, $message = null) {
        if (!empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
                $this->errors[$field] = $message ?? "The {$field} must be a valid URL.";
                return false;
            }
        }
        return true;
    }
    
    /**
     * Sanitize string input
     */
    public function sanitize($field) {
        if (isset($this->data[$field])) {
            return htmlspecialchars(strip_tags(trim($this->data[$field])), ENT_QUOTES, 'UTF-8');
        }
        return null;
    }
    
    /**
     * Get all errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Check if validation passed
     */
    public function isValid() {
        return empty($this->errors);
    }
    
    /**
     * Get error for specific field
     */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }
}




