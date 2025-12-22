<?php
/**
 * Unit Tests for Validator Class
 * Note: Dependencies are loaded by test runner
 */

use App\Core\Validator;

class ValidatorTest {
    
    public function testRequired() {
        $validator = new Validator(['name' => '']);
        $validator->required('name');
        
        assert(!$validator->isValid(), 'Required field validation failed');
        assert(isset($validator->getErrors()['name']), 'Error message not set');
        
        echo "✓ Required validation test passed\n";
    }
    
    public function testEmail() {
        $validator = new Validator(['email' => 'invalid-email']);
        $validator->email('email');
        
        assert(!$validator->isValid(), 'Email validation failed');
        
        $validator2 = new Validator(['email' => 'test@example.com']);
        $validator2->email('email');
        
        assert($validator2->isValid(), 'Valid email rejected');
        
        echo "✓ Email validation test passed\n";
    }
    
    public function testMinLength() {
        $validator = new Validator(['password' => '123']);
        $validator->minLength('password', 6);
        
        assert(!$validator->isValid(), 'Min length validation failed');
        
        $validator2 = new Validator(['password' => '123456']);
        $validator2->minLength('password', 6);
        
        assert($validator2->isValid(), 'Valid length rejected');
        
        echo "✓ Min length validation test passed\n";
    }
    
    public function testNumeric() {
        $validator = new Validator(['age' => 'abc']);
        $validator->numeric('age');
        
        assert(!$validator->isValid(), 'Numeric validation failed');
        
        $validator2 = new Validator(['age' => '25']);
        $validator2->numeric('age');
        
        assert($validator2->isValid(), 'Valid number rejected');
        
        echo "✓ Numeric validation test passed\n";
    }
    
    public function testBetween() {
        $validator = new Validator(['score' => '15']);
        $validator->between('score', 0, 10);
        
        assert(!$validator->isValid(), 'Between validation failed');
        
        $validator2 = new Validator(['score' => '7']);
        $validator2->between('score', 0, 10);
        
        assert($validator2->isValid(), 'Valid range rejected');
        
        echo "✓ Between validation test passed\n";
    }
    
    public function testSanitize() {
        $validator = new Validator(['name' => '<script>alert("xss")</script>Test']);
        $sanitized = $validator->sanitize('name');
        
        assert(strpos($sanitized, '<script>') === false, 'XSS sanitization failed');
        
        echo "✓ Sanitize test passed\n";
    }
    
    public function runAll() {
        echo "Running Validator Unit Tests...\n\n";
        
        try {
            $this->testRequired();
            $this->testEmail();
            $this->testMinLength();
            $this->testNumeric();
            $this->testBetween();
            $this->testSanitize();
            
            echo "\n✅ All Validator tests passed!\n";
        } catch (Exception $e) {
            echo "\n❌ Test failed: " . $e->getMessage() . "\n";
        }
    }
}

// Run tests if executed directly
if (php_sapi_name() === 'cli') {
    $test = new ValidatorTest();
    $test->runAll();
}




