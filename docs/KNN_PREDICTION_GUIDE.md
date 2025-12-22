# 🤖 KNN Prediction System - Complete Guide

## Overview

The EduPredict system uses **K-Nearest Neighbors (KNN)** machine learning algorithm to predict student academic performance. This guide explains how to set it up and use it.

---

## 📋 Prerequisites for Predictions

For KNN predictions to work, you need:

1. **Student GPA** (0.00 - 4.00)
2. **Student Attendance Rate** (0% - 100%)
3. **Student Grades** (at least a few assignments)
4. **Enrollments** (students enrolled in courses)

---

## 🚀 Quick Start Guide

### Step 1: Add Student GPA & Attendance

**URL:** `http://localhost/projecty/utilities/manage-student-gpa.php`

1. Go to the GPA management page
2. Click "Edit" next to a student
3. Enter:
   - **GPA:** 0.00 - 4.00 (e.g., 3.5)
   - **Attendance:** 0% - 100% (e.g., 85%)
4. Click "Update"

**Bulk Update:**
- Use the "Bulk Update" section to set default values for all students without GPA/attendance
- This is useful for quick setup

### Step 2: Enroll Students in Courses

**URL:** `http://localhost/projecty/public/enrollment`

1. Select a student
2. Select a course
3. Click "Enroll Student"

**Bulk Enroll:**
- Select multiple students and courses
- Click "Bulk Enroll"

### Step 3: Add Grades

**URL:** `http://localhost/projecty/public/grade/manage` (Instructor)

1. Login as instructor
2. Select a course
3. Click "Add Grade" for a student
4. Fill in:
   - Assignment Type (Quiz, Exam, Assignment, Project)
   - Grade (e.g., 85)
   - Max Grade (usually 100)

### Step 4: Train the Model

**URL:** `http://localhost/projecty/utilities/train-knn-model.php`

1. Check training data statistics
2. Ensure you have at least 5 students with:
   - GPA & Attendance set
   - At least one grade
3. Click "🚀 Train KNN Model"
4. The system will run predictions for all students

---

## 🎯 How KNN Works

### Training Data

KNN uses all students with complete data as training examples. Each student is represented by:

```
[GPA, Attendance Rate, Average Grade, Assignments Completed]
```

**Example:**
- Student A: [3.5, 85%, 88.5, 12]
- Student B: [2.1, 65%, 72.0, 8]
- Student C: [3.8, 95%, 92.0, 15]

### Prediction Process

1. **Input:** New student features [GPA, Attendance, Avg Grade, Assignments]
2. **Find Neighbors:** Calculate distance to all training students
3. **Select K Nearest:** Pick the 5 most similar students (K=5)
4. **Vote:** Majority risk level from neighbors = prediction
5. **Output:** Predicted risk level, grade, and confidence

### Features Used

| Feature | Description | Range |
|---------|-------------|-------|
| GPA | Grade Point Average | 0.00 - 4.00 |
| Attendance Rate | Class attendance percentage | 0% - 100% |
| Average Grade | Average of all assignment grades | 0 - 100 |
| Assignments Completed | Number of completed assignments | 0+ |

---

## 📊 Understanding Predictions

### Risk Levels

- **Low Risk:** GPA ≥ 3.0, Good attendance, Good grades
- **Medium Risk:** GPA 2.0-3.0, Moderate attendance/grades
- **High Risk:** GPA < 2.0, Poor attendance, Low grades

### Confidence Score

- **0.8 - 1.0:** Very confident (most neighbors agree)
- **0.6 - 0.8:** Confident (majority agree)
- **0.4 - 0.6:** Moderate confidence
- **< 0.4:** Low confidence (neighbors disagree)

### Predicted Grade

Based on average grade of nearest neighbors. Example:
- Neighbors have grades: [85, 88, 82, 90, 87]
- Predicted grade: 86.4

---

## 🔧 Manual Steps

### Adding GPA via Database (Advanced)

```sql
UPDATE students 
SET gpa = 3.5, 
    attendance_rate = 85.0,
    risk_level = 'low'
WHERE id = 1;
```

### Running Prediction Programmatically

```php
require_once 'app/services/PredictionService.php';

$predictionService = new \App\Services\PredictionService();

// Predict for a student
$result = $predictionService->predictPerformance($studentId);

// Predict for all students
$allPredictions = $predictionService->predictAllStudents();

// Predict for course students
$coursePredictions = $predictionService->predictCourseStudents($courseId);
```

---

## ✅ Checklist for Working Predictions

- [ ] At least 5 students have GPA set (0.00 - 4.00)
- [ ] At least 5 students have attendance rate set (0% - 100%)
- [ ] Students are enrolled in courses
- [ ] Students have at least one grade
- [ ] Training data is sufficient (check training page)
- [ ] Model has been trained (run training utility)

---

## 🐛 Troubleshooting

### "No training data available"

**Problem:** Not enough students with complete data

**Solution:**
1. Add GPA and attendance to more students
2. Ensure students have grades
3. Need at least 5 students with complete data

### "Prediction confidence is low"

**Problem:** Student's features don't match well with training data

**Solution:**
1. Add more training data (more students)
2. Ensure training data covers various GPA/attendance ranges
3. Add more grades for the student

### "Prediction not updating"

**Problem:** Predictions run automatically but may need manual refresh

**Solution:**
1. Go to training page
2. Click "Train KNN Model"
3. Or trigger prediction when adding grades (automatic)

---

## 📈 Improving Prediction Accuracy

1. **More Training Data:** Add more students with complete data
2. **Diverse Data:** Include students with various GPA ranges
3. **More Grades:** Students with more assignments = better predictions
4. **Accurate GPA:** Ensure GPA reflects actual performance
5. **Regular Updates:** Run training after adding new data

---

## 🔗 Related Pages

- **Manage GPA:** `/projecty/utilities/manage-student-gpa.php`
- **Train Model:** `/projecty/utilities/train-knn-model.php`
- **View Predictions:** `/projecty/public/predictions`
- **Manage Enrollments:** `/projecty/public/enrollment`
- **Add Grades:** `/projecty/public/grade/manage`

---

## 📝 Example Workflow

1. **Create Student** → Admin creates user with role "student"
2. **Set GPA** → Admin sets GPA (3.2) and attendance (88%)
3. **Enroll in Course** → Admin enrolls student in "CS101"
4. **Add Grades** → Instructor adds grades (Quiz: 85, Exam: 90)
5. **Run Prediction** → System automatically predicts or admin runs training
6. **View Results** → Check predictions page for risk level and predicted grade

---

## 🎓 Best Practices

1. **Set Realistic GPA:** Based on actual academic performance
2. **Track Attendance:** Update regularly throughout semester
3. **Add Grades Promptly:** More data = better predictions
4. **Review Predictions:** Check if predictions make sense
5. **Update Regularly:** Run training after significant data changes

---

## 💡 Tips

- **Minimum Data:** Need at least 5 students for basic predictions
- **Optimal Data:** 20+ students for reliable predictions
- **Auto-Prediction:** Predictions run automatically when grades are added
- **Manual Training:** Use training page to refresh all predictions
- **Risk Alerts:** High-risk students automatically get alerts created

---

**Need Help?** Check the training page for detailed statistics and data quality indicators.

