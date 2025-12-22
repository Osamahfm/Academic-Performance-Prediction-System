# How to Run Unit Tests

## Quick Start

### Method 1: Using XAMPP (Recommended for Windows)

1. **Open Command Prompt or PowerShell**
   - Press `Win + R`, type `cmd` or `powershell`, press Enter

2. **Navigate to your project directory**
   ```bash
   cd C:\xampp\htdocs\projecty
   ```

3. **Run the test suite**
   ```bash
   C:\xampp\php\php.exe tests\run-tests.php
   ```

   Or if PHP is in your PATH:
   ```bash
   php tests\run-tests.php
   ```

### Method 2: Using Browser (Quick Check)

1. **Open your browser**
2. **Navigate to:**
   ```
   http://localhost/projecty/tests/run-tests.php
   ```
   Note: This may not work if output buffering is enabled. Use command line method instead.

### Method 3: Using XAMPP Control Panel

1. **Open XAMPP Control Panel**
2. **Click "Shell" button** (opens command prompt)
3. **Navigate and run:**
   ```bash
   cd htdocs\projecty
   php tests\run-tests.php
   ```

## Running Individual Test Files

### Run KNN Predictor Tests Only
```bash
php -r "require 'tests/Unit/KNNPredictorTest.php'; \$test = new KNNPredictorTest(); \$test->runAll();"
```

### Run Validation Strategy Tests Only
```bash
php -r "require 'tests/Unit/ValidationStrategyTest.php'; \$test = new ValidationStrategyTest(); \$test->runAll();"
```

### Run Grade Model Tests Only
```bash
php -r "require 'tests/Unit/GradeModelTest.php'; \$test = new GradeModelTest(); \$test->runAll();"
```

### Run Prediction Service Tests Only
```bash
php -r "require 'tests/Unit/PredictionServiceTest.php'; \$test = new PredictionServiceTest(); \$test->runAll();"
```

## Expected Output

When tests pass successfully, you'll see:

```
========================================
EduPredict Unit Test Suite
========================================

Running Validator Unit Tests...

✓ Required validation test passed
✓ Email validation test passed
✓ Min length validation test passed
✓ Numeric validation test passed

✅ All Validator tests passed!

Running ModelFactory Unit Tests...

✓ UserModel creation test passed
✓ StudentModel creation test passed
✓ Singleton behavior test passed
✓ Invalid model type test passed

✅ All ModelFactory tests passed!

Running KNNPredictor Unit Tests...

✓ Predict with empty training data test passed
✓ Load training data test passed
✓ Predict with valid training data test passed
...

✅ All KNNPredictor tests passed!

========================================
Test Summary
========================================
Passed: 6
Failed: 0
Total: 6
========================================
```

## Troubleshooting

### Error: "php is not recognized"
**Solution:** Use full path to PHP:
```bash
C:\xampp\php\php.exe tests\run-tests.php
```

### Error: "Class not found"
**Solution:** Make sure you're in the project root directory:
```bash
cd C:\xampp\htdocs\projecty
```

### Error: "Database connection failed"
**Solution:** 
- Make sure XAMPP MySQL is running
- Check database credentials in `config/database.php`
- Some tests don't require database (KNNPredictor, ValidationStrategy)

### Error: "Cannot redeclare class"
**Solution:** Tests are being included multiple times. Make sure you're running from the test runner, not including files manually.

## Test Categories

### Tests That DON'T Require Database
- ✅ KNNPredictorTest
- ✅ ValidationStrategyTest
- ✅ PredictionServiceTest (most tests)
- ✅ ValidatorTest
- ✅ ModelFactoryTest

### Tests That DO Require Database
- ⚠️ GradeModelTest (uses test data with high IDs)

## Running Tests in CI/CD

For continuous integration, you can use:

```bash
# Exit with error code if tests fail
php tests/run-tests.php || exit 1
```

## Quick Test Checklist

- [ ] PHP is installed and accessible
- [ ] You're in the project root directory
- [ ] Database is running (for GradeModel tests)
- [ ] All test files exist in `tests/Unit/`
- [ ] Run: `php tests/run-tests.php`

## Alternative: Create a Test Runner Script

Create `test.bat` in project root:

```batch
@echo off
cd /d "%~dp0"
C:\xampp\php\php.exe tests\run-tests.php
pause
```

Then just double-click `test.bat` to run all tests!

## Need Help?

If tests fail:
1. Check the error message
2. Verify PHP version (needs 7.4+)
3. Check database connection
4. Review the specific test file for issues

