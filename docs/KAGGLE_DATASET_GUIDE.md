# 📊 Kaggle Dataset Import Guide for KNN Training

## Overview

The EduPredict system uses K-Nearest Neighbors (KNN) machine learning algorithm to predict student academic performance. To train the model effectively, you need to import a dataset from Kaggle.

## Quick Start

### Option 1: Use Kaggle Dataset (Recommended)

1. **Download Dataset**
   - Visit: `http://localhost/projecty/utilities/download-kaggle-dataset.php`
   - Choose a recommended dataset
   - Download the CSV file

2. **Prepare CSV File**
   - Rename to: `student_performance.csv`
   - Place in: `projecty/utilities/dataset/` folder

3. **Import Dataset**
   - Visit: `http://localhost/projecty/utilities/import-kaggle-dataset.php`
   - The script will automatically detect and map columns
   - Import training data into database

4. **Use Predictions**
   - Go to: `http://localhost/projecty/public/index.php?controller=prediction&action=index`
   - Click "Run Predictions" to use KNN with imported data

### Option 2: Generate Sample Data

If you don't have a Kaggle dataset:
- Visit: `http://localhost/projecty/utilities/generate-sample-dataset.php`
- This creates 200 synthetic training samples
- Then import using the import script

## Recommended Kaggle Datasets

### 1. Students Performance in Exams
- **URL**: https://www.kaggle.com/datasets/spscientist/students-performance-in-exams
- **Records**: 1,000 students
- **Features**: Math, Reading, Writing scores
- **Usage**: Calculate GPA from scores

### 2. Student Performance and Learning Behavior
- **URL**: https://www.kaggle.com/datasets/aljarah/xAPI-Edu-Data
- **Records**: 480 students
- **Features**: Performance, attendance, grades
- **Usage**: Direct mapping available

### 3. College Student Performance and Placement
- **URL**: https://www.kaggle.com/datasets/tejashvi14/college-student-performance-and-placement-data
- **Records**: 10,000+ students
- **Features**: GPA, IQ scores, performance ratings
- **Usage**: Direct use of GPA data

## CSV Format Requirements

### Required Columns (flexible naming)

| Column Name | Variations | Description | Range |
|------------|------------|-------------|-------|
| GPA | `gpa`, `GPA`, `grade_point_average`, `cgpa` | Grade Point Average | 0-4.0 |
| Attendance | `attendance`, `attendance_rate`, `attendance_percentage` | Attendance % | 0-100 |
| Avg Grade | `avg_grade`, `average_grade`, `mean_grade` | Average grade | 0-100 |
| Assignments | `assignments`, `assignments_completed` | Number completed | Integer |
| Risk Level | `risk_level`, `risk`, `performance` | Risk level | low/medium/high |

### Example CSV

```csv
gpa,attendance_rate,avg_grade,assignments_completed,risk_level
3.5,95.0,87.5,12,low
2.1,65.0,52.5,5,high
3.0,85.0,75.0,10,medium
```

## How KNN Training Works

1. **Data Import**: CSV data is imported into `students` table with `TRAIN_` prefix
2. **Feature Extraction**: System extracts 4 features:
   - GPA (0-4.0)
   - Attendance Rate (0-100%)
   - Average Grade (0-100)
   - Assignments Completed (count)

3. **Training**: KNN uses imported data as training set
4. **Prediction**: When predicting, KNN finds 5 nearest neighbors from training data
5. **Result**: Predicts risk level, performance, and grade based on neighbors

## Import Process

The import script automatically:
- ✅ Detects column names (case-insensitive)
- ✅ Maps common column variations
- ✅ Validates data ranges
- ✅ Normalizes risk levels
- ✅ Creates training records
- ✅ Generates synthetic grades if needed

## Training Data Priority

The system prioritizes training data in this order:

1. **Imported Kaggle Data** (`TRAIN_` prefix) - Used first
2. **Actual Student Data** - Used as fallback if no training data

## Troubleshooting

### CSV Not Found
- Ensure file is named: `student_performance.csv`
- Check location: `projecty/utilities/dataset/`
- Create `dataset` folder if missing

### Column Mapping Failed
- Check column names match required variations
- Ensure CSV has header row
- Verify at least GPA and Attendance columns exist

### Import Errors
- Check database connection
- Verify CSV file format (UTF-8 encoding)
- Ensure data values are within valid ranges

### No Training Data
- Import more records (minimum 10 recommended)
- Check that GPA and Attendance columns have valid data
- Verify risk_level column or let system infer it

## Best Practices

1. **Dataset Size**: Use at least 100-200 records for better accuracy
2. **Data Quality**: Ensure clean, validated data
3. **Feature Balance**: Include diverse GPA and attendance ranges
4. **Regular Updates**: Re-import when adding new training data

## Files Created

- `utilities/import-kaggle-dataset.php` - Main import script
- `utilities/download-kaggle-dataset.php` - Dataset download guide
- `utilities/generate-sample-dataset.php` - Sample data generator
- `utilities/dataset/README.md` - Dataset folder guide
- `app/services/PredictionService.php` - Updated to use training data

## Next Steps

After importing dataset:
1. ✅ Training data is ready
2. ✅ KNN model can use it for predictions
3. ✅ Go to Predictions page
4. ✅ Click "Run Predictions"
5. ✅ View ML-based predictions

---

**Note**: The KNN model improves with more training data. Import larger datasets for better prediction accuracy!



