# 🤖 How to Make KNN Work - Complete Guide

## Overview

The K-Nearest Neighbors (KNN) machine learning algorithm predicts student academic performance. This guide shows you exactly how to set it up and use it.

## Quick Start (3 Steps)

### Step 1: Check System Status
Visit: `http://localhost/projecty/utilities/knn-status-check.php`

This will show you:
- ✅ What's working
- ⚠️ What needs attention
- ❌ What's missing

### Step 2: Add Student Data
Students need:
- **GPA** (0-4.0)
- **Attendance Rate** (0-100%)
- **Grades** (at least a few)

**Option A: Add Grades to Existing Students**
- Visit: `http://localhost/projecty/utilities/add-grades-to-students.php`
- Click "Add Grades to All Students"
- This automatically generates grades based on each student's GPA

**Option B: Import Kaggle Dataset (Recommended)**
- Visit: `http://localhost/projecty/utilities/download-kaggle-dataset.php`
- Download a dataset
- Import it: `http://localhost/projecty/utilities/import-kaggle-dataset.php`

### Step 3: Run Predictions
- Visit: `http://localhost/projecty/public/index.php?controller=prediction&action=index`
- Click **"Run Predictions for All Students"**
- View the results!

## Detailed Setup

### Prerequisites

For KNN to work, you need:

1. **Training Data** (one of these):
   - ✅ Kaggle dataset imported (best)
   - ✅ OR at least 5-10 students with complete data

2. **Student Data**:
   - ✅ Students in database
   - ✅ Each student has GPA
   - ✅ Each student has attendance_rate
   - ✅ Students have at least 1 grade (better with more)

3. **Courses** (optional but recommended):
   - ✅ Courses created
   - ✅ Students enrolled in courses

### Minimum Requirements

**Minimum to make predictions:**
- 1 student with GPA and attendance_rate
- At least 1 grade for that student
- OR imported Kaggle training data

**Recommended for good accuracy:**
- 10+ students with complete data
- 5+ grades per student
- OR 100+ records from Kaggle dataset

## How KNN Works

### 1. Training Phase
KNN loads training data:
- **Priority 1**: Imported Kaggle data (with `TRAIN_` prefix)
- **Priority 2**: Actual student data (if no Kaggle data)

### 2. Feature Extraction
For each student, KNN extracts 4 features:
- **GPA** (0-4.0)
- **Attendance Rate** (0-100%)
- **Average Grade** (0-100)
- **Assignments Completed** (count)

### 3. Prediction Phase
When predicting:
1. Normalizes student features
2. Calculates distance to all training records
3. Finds 5 nearest neighbors (K=5)
4. Predicts based on majority vote
5. Calculates confidence score

### 4. Results
Returns:
- **Predicted Grade** (0-100)
- **Risk Level** (low/medium/high)
- **Confidence** (0-1)
- **Risk Factors** (list of issues)

## Usage Methods

### Method 1: Web Interface (Easiest)

1. **Go to Predictions Page**
   ```
   http://localhost/projecty/public/index.php?controller=prediction&action=index
   ```

2. **Click "Run Predictions"**
   - For Admin/Instructor: Runs for all students
   - For Student: Runs for their own data

3. **View Results**
   - See predictions with confidence scores
   - View risk factors
   - Check predicted grades

### Method 2: API Endpoints

**Predict for one student:**
```
GET /projecty/public/index.php?controller=prediction&action=predictStudent&student_id=1
```

**Predict for all students:**
```
GET /projecty/public/index.php?controller=prediction&action=predictAll
```

**Predict for course students:**
```
GET /projecty/public/index.php?controller=prediction&action=predictCourse&course_id=1
```

### Method 3: Automatic (Student Dashboard)

When a student views their dashboard:
- KNN automatically runs if they have grades
- Updates their risk level
- Creates alerts if high risk

## Troubleshooting

### Problem: "No training data available"

**Solution:**
1. Import Kaggle dataset, OR
2. Add grades to at least 5 students
3. Ensure students have GPA and attendance

### Problem: "KNN test failed"

**Check:**
- Students have GPA values (not NULL)
- Students have attendance_rate values (not NULL)
- At least one student has grades
- Database connection is working

**Fix:**
```sql
-- Update students with default values if needed
UPDATE students SET gpa = 2.5 WHERE gpa IS NULL;
UPDATE students SET attendance_rate = 75.0 WHERE attendance_rate IS NULL;
```

### Problem: "Low confidence scores"

**Causes:**
- Not enough training data
- Students have very different features
- Training data doesn't match student patterns

**Solutions:**
- Import more Kaggle training data (100+ records)
- Add more grades to students
- Ensure training data covers diverse GPA/attendance ranges

### Problem: "Predictions seem wrong"

**Check:**
- Student's GPA and attendance are correct
- Student has enough grades
- Training data is relevant

**Improve:**
- Use Kaggle dataset with similar student profiles
- Add more training records
- Verify student data accuracy

## Testing KNN

### Quick Test

1. **Status Check:**
   ```
   http://localhost/projecty/utilities/knn-status-check.php
   ```

2. **Test Prediction:**
   - Go to predictions page
   - Click "Run Predictions"
   - Check if results appear

3. **Verify Results:**
   - Predictions should show risk levels
   - Confidence scores should be > 0.3
   - Predicted grades should be reasonable

### Manual Test

```php
// In PHP code
$predictionService = new \App\Services\PredictionService();
$prediction = $predictionService->predictPerformance($studentId);

print_r($prediction);
// Should output: predicted_grade, risk_level, confidence, etc.
```

## Best Practices

1. **Use Kaggle Dataset**
   - Better accuracy with real-world data
   - 100+ records recommended
   - Diverse GPA/attendance ranges

2. **Keep Data Updated**
   - Update student GPAs regularly
   - Add new grades as they come in
   - Re-run predictions periodically

3. **Monitor Confidence**
   - Low confidence (< 0.5) = need more training data
   - High confidence (> 0.7) = good prediction
   - Very low confidence = check data quality

4. **Validate Predictions**
   - Compare predictions to actual outcomes
   - Adjust training data if needed
   - Use feedback to improve

## Files Involved

- **KNN Algorithm**: `app/core/ML/KNNPredictor.php`
- **Prediction Service**: `app/services/PredictionService.php`
- **Prediction Controller**: `app/controllers/PredictionController.php`
- **Prediction Model**: `app/models/PredictionModel.php`
- **Views**: `app/views/predictions/`

## Next Steps

After KNN is working:

1. ✅ View predictions on dashboard
2. ✅ Set up automatic predictions
3. ✅ Create alerts for high-risk students
4. ✅ Export prediction reports
5. ✅ Analyze prediction accuracy

---

**Need Help?** Check the status page: `http://localhost/projecty/utilities/knn-status-check.php`



