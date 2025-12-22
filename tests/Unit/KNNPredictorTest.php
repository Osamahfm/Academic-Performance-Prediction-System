<?php
/**
 * Unit Tests for KNNPredictor Class
 * Tests the core ML algorithm for academic performance prediction
 * Note: Dependencies are loaded by test runner
 */

use App\Core\ML\KNNPredictor;

class KNNPredictorTest {
    
    /**
     * Test: Predict with empty training data (edge case)
     */
    public function testPredictWithEmptyTrainingData() {
        $predictor = new KNNPredictor(5);
        $features = [3.5, 85.0, 88.0, 10];
        
        $result = $predictor->predict($features);
        
        assert(isset($result['predicted_grade']), 'Predicted grade should be set');
        assert($result['predicted_grade'] == 70.0, 'Default prediction should be 70.0');
        assert($result['risk_level'] == 'medium', 'Default risk level should be medium');
        assert($result['confidence'] == 0.5, 'Default confidence should be 0.5');
        
        echo "✓ Predict with empty training data test passed\n";
    }
    
    /**
     * Test: Load training data (happy path)
     */
    public function testLoadTrainingData() {
        $predictor = new KNNPredictor(5);
        $trainingData = [
            ['gpa' => 3.5, 'attendance_rate' => 90.0, 'avg_grade' => 85.0, 'assignments_completed' => 10, 'risk_level' => 'low'],
            ['gpa' => 2.5, 'attendance_rate' => 70.0, 'avg_grade' => 65.0, 'assignments_completed' => 5, 'risk_level' => 'medium'],
            ['gpa' => 1.5, 'attendance_rate' => 50.0, 'avg_grade' => 45.0, 'assignments_completed' => 2, 'risk_level' => 'high']
        ];
        
        $predictor->loadTrainingData($trainingData);
        
        // Use reflection to check private property
        $reflection = new ReflectionClass($predictor);
        $property = $reflection->getProperty('trainingData');
        $property->setAccessible(true);
        $loadedData = $property->getValue($predictor);
        
        assert(count($loadedData) == 3, 'Training data should be loaded');
        assert($loadedData[0]['gpa'] == 3.5, 'First training record should match');
        
        echo "✓ Load training data test passed\n";
    }
    
    /**
     * Test: Predict with valid training data (happy path)
     */
    public function testPredictWithValidTrainingData() {
        $predictor = new KNNPredictor(3);
        $trainingData = [
            ['gpa' => 3.8, 'attendance_rate' => 95.0, 'avg_grade' => 92.0, 'assignments_completed' => 12, 'risk_level' => 'low'],
            ['gpa' => 3.5, 'attendance_rate' => 90.0, 'avg_grade' => 85.0, 'assignments_completed' => 10, 'risk_level' => 'low'],
            ['gpa' => 3.2, 'attendance_rate' => 85.0, 'avg_grade' => 80.0, 'assignments_completed' => 8, 'risk_level' => 'low'],
            ['gpa' => 2.5, 'attendance_rate' => 70.0, 'avg_grade' => 65.0, 'assignments_completed' => 5, 'risk_level' => 'medium'],
            ['gpa' => 2.0, 'attendance_rate' => 60.0, 'avg_grade' => 55.0, 'assignments_completed' => 3, 'risk_level' => 'medium'],
            ['gpa' => 1.5, 'attendance_rate' => 50.0, 'avg_grade' => 45.0, 'assignments_completed' => 2, 'risk_level' => 'high']
        ];
        
        $predictor->loadTrainingData($trainingData);
        
        // Test student similar to high performers
        $highPerformerFeatures = [3.7, 93.0, 90.0, 11];
        $result = $predictor->predict($highPerformerFeatures);
        
        assert(isset($result['predicted_grade']), 'Predicted grade should be set');
        assert($result['predicted_grade'] > 0, 'Predicted grade should be positive');
        assert($result['predicted_grade'] <= 100, 'Predicted grade should not exceed 100');
        assert(in_array($result['risk_level'], ['low', 'medium', 'high']), 'Risk level should be valid');
        assert($result['confidence'] >= 0 && $result['confidence'] <= 1, 'Confidence should be between 0 and 1');
        
        echo "✓ Predict with valid training data test passed\n";
    }
    
    /**
     * Test: Predict with different K values (edge case)
     */
    public function testPredictWithDifferentKValues() {
        $trainingData = [
            ['gpa' => 3.8, 'attendance_rate' => 95.0, 'avg_grade' => 92.0, 'assignments_completed' => 12, 'risk_level' => 'low'],
            ['gpa' => 3.5, 'attendance_rate' => 90.0, 'avg_grade' => 85.0, 'assignments_completed' => 10, 'risk_level' => 'low'],
            ['gpa' => 3.2, 'attendance_rate' => 85.0, 'avg_grade' => 80.0, 'assignments_completed' => 8, 'risk_level' => 'low'],
            ['gpa' => 2.5, 'attendance_rate' => 70.0, 'avg_grade' => 65.0, 'assignments_completed' => 5, 'risk_level' => 'medium'],
            ['gpa' => 2.0, 'attendance_rate' => 60.0, 'avg_grade' => 55.0, 'assignments_completed' => 3, 'risk_level' => 'medium']
        ];
        
        $predictor1 = new KNNPredictor(3);
        $predictor1->loadTrainingData($trainingData);
        
        $predictor2 = new KNNPredictor(5);
        $predictor2->loadTrainingData($trainingData);
        
        $features = [3.0, 80.0, 75.0, 7];
        $result1 = $predictor1->predict($features);
        $result2 = $predictor2->predict($features);
        
        assert(isset($result1['predicted_grade']), 'K=3 prediction should work');
        assert(isset($result2['predicted_grade']), 'K=5 prediction should work');
        assert($result1['neighbors_analyzed'] == 3, 'Should analyze 3 neighbors');
        assert($result2['neighbors_analyzed'] == 5, 'Should analyze 5 neighbors');
        
        echo "✓ Predict with different K values test passed\n";
    }
    
    /**
     * Test: Predict course grade with historical grades (happy path)
     */
    public function testPredictCourseGradeWithHistory() {
        $predictor = new KNNPredictor(5);
        $trainingData = [
            ['gpa' => 3.5, 'attendance_rate' => 90.0, 'avg_grade' => 85.0, 'assignments_completed' => 10, 'risk_level' => 'low'],
            ['gpa' => 2.5, 'attendance_rate' => 70.0, 'avg_grade' => 65.0, 'assignments_completed' => 5, 'risk_level' => 'medium']
        ];
        $predictor->loadTrainingData($trainingData);
        
        $features = [3.0, 80.0, 75.0, 7];
        $courseGrades = [80.0, 85.0, 82.0, 88.0]; // Historical grades
        
        $predictedGrade = $predictor->predictCourseGrade($features, $courseGrades);
        
        assert($predictedGrade > 0, 'Predicted course grade should be positive');
        assert($predictedGrade <= 100, 'Predicted course grade should not exceed 100');
        assert(is_numeric($predictedGrade), 'Predicted grade should be numeric');
        
        echo "✓ Predict course grade with history test passed\n";
    }
    
    /**
     * Test: Predict course grade without history (edge case)
     */
    public function testPredictCourseGradeWithoutHistory() {
        $predictor = new KNNPredictor(5);
        $trainingData = [
            ['gpa' => 3.5, 'attendance_rate' => 90.0, 'avg_grade' => 85.0, 'assignments_completed' => 10, 'risk_level' => 'low']
        ];
        $predictor->loadTrainingData($trainingData);
        
        $features = [3.0, 80.0, 75.0, 7];
        $predictedGrade = $predictor->predictCourseGrade($features, []);
        
        assert($predictedGrade > 0, 'Predicted grade should be positive');
        assert($predictedGrade <= 100, 'Predicted grade should not exceed 100');
        
        echo "✓ Predict course grade without history test passed\n";
    }
    
    /**
     * Test: Predict with extreme values (edge case)
     */
    public function testPredictWithExtremeValues() {
        $predictor = new KNNPredictor(5);
        $trainingData = [
            ['gpa' => 4.0, 'attendance_rate' => 100.0, 'avg_grade' => 100.0, 'assignments_completed' => 20, 'risk_level' => 'low'],
            ['gpa' => 0.0, 'attendance_rate' => 0.0, 'avg_grade' => 0.0, 'assignments_completed' => 0, 'risk_level' => 'high']
        ];
        $predictor->loadTrainingData($trainingData);
        
        // Test with perfect student
        $perfectFeatures = [4.0, 100.0, 100.0, 20];
        $result = $predictor->predict($perfectFeatures);
        
        assert($result['predicted_grade'] >= 0, 'Predicted grade should be non-negative');
        assert($result['predicted_grade'] <= 100, 'Predicted grade should not exceed 100');
        
        // Test with failing student
        $failingFeatures = [0.0, 0.0, 0.0, 0];
        $result2 = $predictor->predict($failingFeatures);
        
        assert($result2['predicted_grade'] >= 0, 'Predicted grade should be non-negative');
        assert($result2['predicted_grade'] <= 100, 'Predicted grade should not exceed 100');
        
        echo "✓ Predict with extreme values test passed\n";
    }
    
    /**
     * Test: Predict with identical training data (edge case)
     */
    public function testPredictWithIdenticalTrainingData() {
        $predictor = new KNNPredictor(5);
        $trainingData = [
            ['gpa' => 3.0, 'attendance_rate' => 80.0, 'avg_grade' => 75.0, 'assignments_completed' => 8, 'risk_level' => 'medium'],
            ['gpa' => 3.0, 'attendance_rate' => 80.0, 'avg_grade' => 75.0, 'assignments_completed' => 8, 'risk_level' => 'medium'],
            ['gpa' => 3.0, 'attendance_rate' => 80.0, 'avg_grade' => 75.0, 'assignments_completed' => 8, 'risk_level' => 'medium']
        ];
        $predictor->loadTrainingData($trainingData);
        
        $features = [3.0, 80.0, 75.0, 8];
        $result = $predictor->predict($features);
        
        assert(isset($result['predicted_grade']), 'Should handle identical training data');
        assert($result['predicted_grade'] == 75.0, 'Should predict based on identical neighbors');
        
        echo "✓ Predict with identical training data test passed\n";
    }
    
    /**
     * Test: Predict with single training record (edge case)
     */
    public function testPredictWithSingleTrainingRecord() {
        $predictor = new KNNPredictor(5);
        $trainingData = [
            ['gpa' => 3.5, 'attendance_rate' => 90.0, 'avg_grade' => 85.0, 'assignments_completed' => 10, 'risk_level' => 'low']
        ];
        $predictor->loadTrainingData($trainingData);
        
        $features = [3.0, 80.0, 75.0, 7];
        $result = $predictor->predict($features);
        
        assert(isset($result['predicted_grade']), 'Should work with single training record');
        assert($result['neighbors_analyzed'] == 1, 'Should analyze 1 neighbor');
        
        echo "✓ Predict with single training record test passed\n";
    }
    
    /**
     * Run all tests
     */
    public function runAll() {
        echo "Running KNNPredictor Unit Tests...\n\n";
        
        try {
            $this->testPredictWithEmptyTrainingData();
            $this->testLoadTrainingData();
            $this->testPredictWithValidTrainingData();
            $this->testPredictWithDifferentKValues();
            $this->testPredictCourseGradeWithHistory();
            $this->testPredictCourseGradeWithoutHistory();
            $this->testPredictWithExtremeValues();
            $this->testPredictWithIdenticalTrainingData();
            $this->testPredictWithSingleTrainingRecord();
            
            echo "\n✅ All KNNPredictor tests passed!\n";
        } catch (Exception $e) {
            echo "\n❌ Test failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

