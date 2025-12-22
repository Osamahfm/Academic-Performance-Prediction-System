<?php
namespace App\Core\ML;

/**
 * K-Nearest Neighbors (KNN) Predictor
 * Implements KNN algorithm for academic performance prediction
 */
class KNNPredictor {
    private $k;
    private $trainingData;
    
    /**
     * Constructor
     * @param int $k Number of neighbors to consider (default: 5)
     */
    public function __construct($k = 5) {
        $this->k = $k;
        $this->trainingData = [];
    }
    
    /**
     * Load training data from database
     * @param array $data Array of student records with features and labels
     */
    public function loadTrainingData($data) {
        $this->trainingData = $data;
    }
    
    /**
     * Calculate Euclidean distance between two feature vectors
     * @param array $point1 First point [gpa, attendance, avg_grade, assignments_completed]
     * @param array $point2 Second point
     * @return float Distance
     */
    private function euclideanDistance($point1, $point2) {
        $sum = 0;
        for ($i = 0; $i < count($point1); $i++) {
            $sum += pow($point1[$i] - $point2[$i], 2);
        }
        return sqrt($sum);
    }
    
    /**
     * Normalize features to 0-1 range
     * @param array $features Feature vector
     * @param array $minMax Min and max values for normalization
     * @return array Normalized features
     */
    private function normalizeFeatures($features, $minMax) {
        $normalized = [];
        for ($i = 0; $i < count($features); $i++) {
            $min = $minMax[$i]['min'];
            $max = $minMax[$i]['max'];
            $range = $max - $min;
            
            if ($range == 0) {
                $normalized[] = 0.5; // Default to middle if no range
            } else {
                $normalized[] = ($features[$i] - $min) / $range;
            }
        }
        return $normalized;
    }
    
    /**
     * Get min and max values for normalization
     * @return array Min/max values for each feature
     */
    private function getMinMaxValues() {
        if (empty($this->trainingData)) {
            return null;
        }
        
        $minMax = [];
        $featureCount = 4; // GPA, Attendance, Avg Grade, Assignments
        
        // Initialize min/max arrays
        for ($i = 0; $i < $featureCount; $i++) {
            $minMax[$i] = ['min' => PHP_FLOAT_MAX, 'max' => PHP_FLOAT_MIN];
        }
        
        // Find min/max for each feature
        foreach ($this->trainingData as $record) {
            $features = [
                $record['gpa'] ?? 0,
                $record['attendance_rate'] ?? 0,
                $record['avg_grade'] ?? 0,
                $record['assignments_completed'] ?? 0
            ];
            
            for ($i = 0; $i < $featureCount; $i++) {
                if ($features[$i] < $minMax[$i]['min']) {
                    $minMax[$i]['min'] = $features[$i];
                }
                if ($features[$i] > $minMax[$i]['max']) {
                    $minMax[$i]['max'] = $features[$i];
                }
            }
        }
        
        return $minMax;
    }
    
    /**
     * Predict performance level for a student
     * @param array $studentFeatures [gpa, attendance_rate, avg_grade, assignments_completed]
     * @return array ['prediction' => 'low'|'medium'|'high', 'confidence' => 0-1, 'risk_level' => 'low'|'medium'|'high']
     */
    public function predict($studentFeatures) {
        if (empty($this->trainingData)) {
            // Default prediction if no training data
            return [
                'prediction' => 'medium',
                'confidence' => 0.5,
                'risk_level' => 'medium',
                'predicted_grade' => 70.0
            ];
        }
        
        // Normalize features
        $minMax = $this->getMinMaxValues();
        $normalizedFeatures = $this->normalizeFeatures($studentFeatures, $minMax);
        
        // Calculate distances to all training points
        $distances = [];
        foreach ($this->trainingData as $index => $record) {
            $trainingFeatures = [
                $record['gpa'] ?? 0,
                $record['attendance_rate'] ?? 0,
                $record['avg_grade'] ?? 0,
                $record['assignments_completed'] ?? 0
            ];
            
            $normalizedTraining = $this->normalizeFeatures($trainingFeatures, $minMax);
            $distance = $this->euclideanDistance($normalizedFeatures, $normalizedTraining);
            
            $distances[] = [
                'distance' => $distance,
                'risk_level' => $record['risk_level'] ?? 'medium',
                'gpa' => $record['gpa'] ?? 0,
                'avg_grade' => $record['avg_grade'] ?? 0
            ];
        }
        
        // Sort by distance
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        // Get k nearest neighbors
        $neighbors = array_slice($distances, 0, $this->k);
        
        if (empty($neighbors)) {
            // Fallback if no neighbors found
            return [
                'prediction' => 'medium',
                'confidence' => 0.5,
                'risk_level' => 'medium',
                'predicted_grade' => 70.0
            ];
        }
        
        // Count risk levels in neighbors
        $riskCounts = ['low' => 0, 'medium' => 0, 'high' => 0];
        $totalWeightedGrade = 0;
        $totalWeight = 0;
        $neighborGrades = [];
        
        foreach ($neighbors as $neighbor) {
            $riskLevel = $neighbor['risk_level'] ?? 'medium';
            if (isset($riskCounts[$riskLevel])) {
                $riskCounts[$riskLevel]++;
            }
            
            // Use inverse distance as weight (closer neighbors have more influence)
            // Add small epsilon to avoid division by zero
            $distance = $neighbor['distance'] ?? 1.0;
            $weight = 1.0 / ($distance + 0.0001);
            
            $neighborGrade = $neighbor['avg_grade'] ?? 0;
            $neighborGrades[] = $neighborGrade;
            $totalWeightedGrade += $neighborGrade * $weight;
            $totalWeight += $weight;
        }
        
        // Predict based on majority vote
        $predictedRisk = array_search(max($riskCounts), $riskCounts);
        
        // Calculate confidence (proportion of neighbors with same risk level)
        $confidence = $riskCounts[$predictedRisk] / count($neighbors);
        
        // Predict grade using weighted average of neighbors
        $neighborBasedGrade = $totalWeight > 0 ? ($totalWeightedGrade / $totalWeight) : (array_sum($neighborGrades) / count($neighborGrades));
        
        // Also consider the student's own current performance
        // Weight: 40% student's current avg_grade, 60% neighbor-based prediction
        $studentCurrentGrade = $studentFeatures[2] ?? 0; // avg_grade from features
        $predictedGrade = ($studentCurrentGrade * 0.4) + ($neighborBasedGrade * 0.6);
        
        // If student has no grades yet, rely more on neighbors
        if ($studentCurrentGrade == 0) {
            $predictedGrade = $neighborBasedGrade;
        }
        
        // Ensure prediction is within reasonable bounds (0-100)
        $predictedGrade = max(0, min(100, $predictedGrade));
        
        // Map risk level to performance prediction
        $performanceMap = [
            'low' => 'high',      // Low risk = High performance
            'medium' => 'medium', // Medium risk = Medium performance
            'high' => 'low'       // High risk = Low performance
        ];
        
        return [
            'prediction' => $performanceMap[$predictedRisk] ?? 'medium',
            'confidence' => round($confidence, 2),
            'risk_level' => $predictedRisk,
            'predicted_grade' => round($predictedGrade, 2),
            'neighbors_analyzed' => count($neighbors)
        ];
    }
    
    /**
     * Predict grade for a specific course
     * @param array $studentFeatures Student features
     * @param array $courseGrades Historical grades for this course
     * @return float Predicted grade
     */
    public function predictCourseGrade($studentFeatures, $courseGrades = []) {
        // Get base prediction from KNN
        $basePrediction = $this->predict($studentFeatures);
        $baseGrade = $basePrediction['predicted_grade'];
        
        if (empty($courseGrades)) {
            // Use general prediction if no course-specific data
            return $baseGrade;
        }
        
        // Calculate average of recent grades (weight recent grades more)
        $recentGrades = array_slice($courseGrades, -5); // Last 5 grades
        $avgCourseGrade = array_sum($recentGrades) / count($recentGrades);
        
        // If student has trend (improving or declining), adjust prediction
        $trend = 0;
        if (count($recentGrades) >= 2) {
            $firstHalf = array_slice($recentGrades, 0, ceil(count($recentGrades) / 2));
            $secondHalf = array_slice($recentGrades, ceil(count($recentGrades) / 2));
            $firstAvg = array_sum($firstHalf) / count($firstHalf);
            $secondAvg = array_sum($secondHalf) / count($secondHalf);
            $trend = $secondAvg - $firstAvg; // Positive = improving, Negative = declining
        }
        
        // Apply trend adjustment (up to ±5 points)
        $trendAdjustment = max(-5, min(5, $trend));
        
        // Weight: 50% course-specific grades, 30% base KNN prediction, 20% trend
        $weightedGrade = ($avgCourseGrade * 0.5) + ($baseGrade * 0.3) + ($trendAdjustment * 0.2);
        
        // Also consider student's GPA and attendance for course prediction
        $gpa = $studentFeatures[0] ?? 0;
        $attendance = $studentFeatures[1] ?? 0;
        
        // Adjust based on GPA (students with higher GPA tend to perform better)
        $gpaAdjustment = ($gpa - 2.5) * 5; // Scale GPA difference to grade points
        $gpaAdjustment = max(-10, min(10, $gpaAdjustment)); // Limit adjustment
        
        // Adjust based on attendance (students with higher attendance tend to perform better)
        $attendanceAdjustment = ($attendance - 75) * 0.2; // Scale attendance difference
        $attendanceAdjustment = max(-5, min(5, $attendanceAdjustment)); // Limit adjustment
        
        $finalGrade = $weightedGrade + ($gpaAdjustment * 0.3) + ($attendanceAdjustment * 0.2);
        
        // Ensure prediction is within reasonable bounds (0-100)
        $finalGrade = max(0, min(100, $finalGrade));
        
        return round($finalGrade, 2);
    }
}



