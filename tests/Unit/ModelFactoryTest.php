<?php
/**
 * Unit Tests for ModelFactory Class
 * Note: Dependencies are loaded by test runner
 */

use App\Core\Factory\ModelFactory;

class ModelFactoryTest {
    
    public function testCreateUserModel() {
        $model = ModelFactory::create('user');
        
        assert($model instanceof App\Models\UserModel, 'UserModel creation failed');
        
        echo "✓ UserModel creation test passed\n";
    }
    
    public function testCreateStudentModel() {
        $model = ModelFactory::create('student');
        
        assert($model instanceof App\Models\StudentModel, 'StudentModel creation failed');
        
        echo "✓ StudentModel creation test passed\n";
    }
    
    public function testSingletonBehavior() {
        $model1 = ModelFactory::create('user');
        $model2 = ModelFactory::create('user');
        
        assert($model1 === $model2, 'Singleton pattern not working');
        
        echo "✓ Singleton behavior test passed\n";
    }
    
    public function testInvalidModelType() {
        try {
            ModelFactory::create('invalid');
            assert(false, 'Exception should have been thrown');
        } catch (Exception $e) {
            assert(true, 'Exception correctly thrown');
        }
        
        echo "✓ Invalid model type test passed\n";
    }
    
    public function runAll() {
        echo "Running ModelFactory Unit Tests...\n\n";
        
        try {
            $this->testCreateUserModel();
            $this->testCreateStudentModel();
            $this->testSingletonBehavior();
            $this->testInvalidModelType();
            
            echo "\n✅ All ModelFactory tests passed!\n";
        } catch (Exception $e) {
            echo "\n❌ Test failed: " . $e->getMessage() . "\n";
        }
    }
}

// Run tests if executed directly
if (php_sapi_name() === 'cli') {
    $test = new ModelFactoryTest();
    $test->runAll();
}




