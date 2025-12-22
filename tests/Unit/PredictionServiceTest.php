<?php
/**
 * Unit Tests for PredictionService Class
 * Tests the prediction orchestration logic
 * Note: These tests focus on business logic without full database dependencies
 * Dependencies are loaded by test runner
 */

use App\Services\PredictionService;

class PredictionServiceTest {
    
    /**
     * Test: Identify risk factors - low GPA (edge case)
     */
    public function testIdentifyRiskFactorsLowGPA() {
        // Use reflection to test private method
        $service = new PredictionService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('identifyRiskFactors');
        $method->setAccessible(true);
        
        $features = [1.5, 80.0, 70.0, 5]; // Low GPA
        $prediction = ['predicted_grade' => 65.0, 'risk_level' => 'high'];
        
        $riskFactors = $method->invoke($service, $features, $prediction);
        
        assert(is_array($riskFactors), 'Risk factors should be an array');
        assert(in_array('Low GPA (1.50)', $riskFactors), 'Should identify low GPA');
        
        echo "✓ Identify risk factors - low GPA test passed\n";
    }
    
    /**
     * Test: Identify risk factors - low attendance (edge case)
     */
    public function testIdentifyRiskFactorsLowAttendance() {
        $service = new PredictionService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('identifyRiskFactors');
        $method->setAccessible(true);
        
        $features = [3.0, 60.0, 70.0, 5]; // Low attendance
        $prediction = ['predicted_grade' => 65.0, 'risk_level' => 'medium'];
        
        $riskFactors = $method->invoke($service, $features, $prediction);
        
        assert(is_array($riskFactors), 'Risk factors should be an array');
        assert(in_array('Low attendance rate (60.0%)', $riskFactors), 'Should identify low attendance');
        
        echo "✓ Identify risk factors - low attendance test passed\n";
    }
    
    /**
     * Test: Identify risk factors - low average grade (edge case)
     */
    public function testIdentifyRiskFactorsLowAverageGrade() {
        $service = new PredictionService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('identifyRiskFactors');
        $method->setAccessible(true);
        
        $features = [3.0, 80.0, 55.0, 5]; // Low average grade
        $prediction = ['predicted_grade' => 60.0, 'risk_level' => 'medium'];
        
        $riskFactors = $method->invoke($service, $features, $prediction);
        
        assert(is_array($riskFactors), 'Risk factors should be an array');
        assert(in_array('Low average grade (55.00)', $riskFactors), 'Should identify low average grade');
        
        echo "✓ Identify risk factors - low average grade test passed\n";
    }
    
    /**
     * Test: Identify risk factors - incomplete assignments (edge case)
     */
    public function testIdentifyRiskFactorsIncompleteAssignments() {
        $service = new PredictionService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('identifyRiskFactors');
        $method->setAccessible(true);
        
        $features = [3.0, 80.0, 70.0, 2]; // Few assignments
        $prediction = ['predicted_grade' => 70.0, 'risk_level' => 'medium'];
        
        $riskFactors = $method->invoke($service, $features, $prediction);
        
        assert(is_array($riskFactors), 'Risk factors should be an array');
        assert(in_array('Incomplete assignments (2 completed)', $riskFactors), 'Should identify incomplete assignments');
        
        echo "✓ Identify risk factors - incomplete assignments test passed\n";
    }
    
    /**
     * Test: Identify risk factors - predicted grade below passing (edge case)
     */
    public function testIdentifyRiskFactorsBelowPassing() {
        $service = new PredictionService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('identifyRiskFactors');
        $method->setAccessible(true);
        
        $features = [2.5, 75.0, 65.0, 5];
        $prediction = ['predicted_grade' => 55.0, 'risk_level' => 'high']; // Below 60
        
        $riskFactors = $method->invoke($service, $features, $prediction);
        
        assert(is_array($riskFactors), 'Risk factors should be an array');
        assert(in_array('Predicted grade below passing (55)', $riskFactors), 'Should identify below passing grade');
        
        echo "✓ Identify risk factors - below passing test passed\n";
    }
    
    /**
     * Test: Identify risk factors - no risk factors (happy path)
     */
    public function testIdentifyRiskFactorsNoRisks() {
        $service = new PredictionService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('identifyRiskFactors');
        $method->setAccessible(true);
        
        $features = [3.8, 95.0, 90.0, 12]; // Excellent student
        $prediction = ['predicted_grade' => 92.0, 'risk_level' => 'low'];
        
        $riskFactors = $method->invoke($service, $features, $prediction);
        
        assert(is_array($riskFactors), 'Risk factors should be an array');
        assert(empty($riskFactors), 'Should have no risk factors for excellent student');
        
        echo "✓ Identify risk factors - no risks test passed\n";
    }
    
    /**
     * Test: Identify risk factors - multiple risk factors (edge case)
     */
    public function testIdentifyRiskFactorsMultiple() {
        $service = new PredictionService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('identifyRiskFactors');
        $method->setAccessible(true);
        
        $features = [1.5, 60.0, 50.0, 2]; // Multiple issues
        $prediction = ['predicted_grade' => 45.0, 'risk_level' => 'high'];
        
        $riskFactors = $method->invoke($service, $features, $prediction);
        
        assert(is_array($riskFactors), 'Risk factors should be an array');
        assert(count($riskFactors) >= 4, 'Should identify multiple risk factors');
        
        echo "✓ Identify risk factors - multiple risks test passed\n";
    }
    
    /**
     * Test: Get student features structure (happy path)
     */
    public function testGetStudentFeaturesStructure() {
        $service = new PredictionService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('getStudentFeatures');
        $method->setAccessible(true);
        
        // Mock student ID that doesn't exist
        $features = $method->invoke($service, 999999, null);
        
        assert(is_array($features), 'Features should be an array');
        assert(count($features) == 4, 'Should return 4 features');
        assert(isset($features[0]), 'GPA should be present');
        assert(isset($features[1]), 'Attendance should be present');
        assert(isset($features[2]), 'Average grade should be present');
        assert(isset($features[3]), 'Assignments completed should be present');
        
        echo "✓ Get student features structure test passed\n";
    }
    
    /**
     * Test: Get student features with non-existent student (edge case)
     */
    public function testGetStudentFeaturesNonExistent() {
        $service = new PredictionService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('getStudentFeatures');
        $method->setAccessible(true);
        
        // Use a very high ID that shouldn't exist
        $features = $method->invoke($service, 999999999, null);
        
        assert(is_array($features), 'Features should be an array');
        assert($features[0] == 0, 'GPA should default to 0');
        assert($features[1] == 0, 'Attendance should default to 0');
        assert($features[2] == 0, 'Average grade should default to 0');
        assert($features[3] == 0, 'Assignments should default to 0');
        
        echo "✓ Get student features - non-existent student test passed\n";
    }
    
    /**
     * Run all tests
     */
    public function runAll() {
        echo "Running PredictionService Unit Tests...\n\n";
        
        try {
            $this->testIdentifyRiskFactorsLowGPA();
            $this->testIdentifyRiskFactorsLowAttendance();
            $this->testIdentifyRiskFactorsLowAverageGrade();
            $this->testIdentifyRiskFactorsIncompleteAssignments();
            $this->testIdentifyRiskFactorsBelowPassing();
            $this->testIdentifyRiskFactorsNoRisks();
            $this->testIdentifyRiskFactorsMultiple();
            $this->testGetStudentFeaturesStructure();
            $this->testGetStudentFeaturesNonExistent();
            
            echo "\n✅ All PredictionService tests passed!\n";
        } catch (Exception $e) {
            echo "\n❌ Test failed: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }
}

