# Kaggle Dataset Import Guide

## 📊 How to Import Kaggle Dataset for KNN Training

### Step 1: Download Dataset from Kaggle

1. Go to [Kaggle.com](https://www.kaggle.com)
2. Search for "Student Performance Dataset" or "Academic Performance Dataset"
3. Download a dataset that contains:
   - GPA or Grade Point Average
   - Attendance Rate
   - Grades or Performance Scores
   - Assignment/Exam data

### Recommended Kaggle Datasets:

1. **Student Performance Dataset**
   - URL: `https://www.kaggle.com/datasets/aljarah/xAPI-Edu-Data`
   - Contains: Student performance data with grades and attendance

2. **Students' Academic Performance Dataset**
   - Contains: GPA, attendance, grades, assignments

3. **Student Grades Dataset**
   - Contains: Grade data with performance metrics

### Step 2: Prepare the CSV File

1. Download the dataset CSV file
2. Rename it to `student_performance.csv`
3. Place it in: `projecty/utilities/dataset/` folder

### Step 3: CSV Format Requirements

Your CSV file should have columns (names can vary):

**Required Columns:**
- `gpa` or `GPA` - Grade Point Average (0-4.0 scale)
- `attendance` or `attendance_rate` - Attendance percentage (0-100)
- `avg_grade` or `average_grade` - Average grade (0-100) [Optional]
- `assignments` or `assignments_completed` - Number of assignments [Optional]
- `risk_level` or `performance` - Risk level (low/medium/high) [Optional]

**Example CSV:**
```csv
gpa,attendance_rate,avg_grade,assignments_completed,risk_level
3.5,95.0,87.5,12,low
2.1,65.0,52.5,5,high
3.0,85.0,75.0,10,medium
```

### Step 4: Import the Dataset

1. Visit: `http://localhost/projecty/utilities/import-kaggle-dataset.php`
2. The script will automatically:
   - Detect column names
   - Map columns to required fields
   - Import data into database
   - Create training records

### Step 5: Use the Training Data

After import:
1. Go to Predictions page
2. Click "Run Predictions"
3. The KNN model will use the imported data for training

### Alternative: Generate Sample Data

If you don't have a Kaggle dataset:
1. Visit: `http://localhost/projecty/utilities/generate-sample-dataset.php`
2. This will create a synthetic dataset with 200 samples
3. Then import it using the import script

## 📝 Notes

- The import script is flexible with column names
- Missing columns will be estimated from available data
- Risk levels are automatically inferred from GPA if not provided
- Training data is stored in the `students` table with `TRAIN_` prefix



