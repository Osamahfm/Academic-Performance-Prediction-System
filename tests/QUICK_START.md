# 🚀 Quick Start: Running Tests

## Easiest Method (Windows)

### Option 1: Double-Click (Easiest!)
1. Go to your project folder: `C:\xampp\htdocs\projecty`
2. **Double-click `test.bat`**
3. Tests will run automatically!

### Option 2: Command Line

1. **Open Command Prompt**
   - Press `Windows Key + R`
   - Type `cmd` and press Enter

2. **Navigate to project**
   ```bash
   cd C:\xampp\htdocs\projecty
   ```

3. **Run tests**
   ```bash
   C:\xampp\php\php.exe tests\run-tests.php
   ```

## What You'll See

### ✅ Success Example:
```
========================================
EduPredict Unit Test Suite
========================================

Running Validator Unit Tests...

✓ Required validation test passed
✓ Email validation test passed
...

✅ All Validator tests passed!

Running KNNPredictor Unit Tests...

✓ Predict with empty training data test passed
✓ Load training data test passed
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

### ❌ Failure Example:
```
❌ Test failed: Assertion failed: Expected value but got different value
```

## Test Files Overview

| Test File | Tests | Requires DB? | What It Tests |
|-----------|-------|--------------|--------------|
| `KNNPredictorTest.php` | 9 | ❌ No | ML prediction algorithm |
| `ValidationStrategyTest.php` | 18 | ❌ No | Form validation logic |
| `GradeModelTest.php` | 7 | ✅ Yes | Grade calculations |
| `PredictionServiceTest.php` | 9 | ❌ No | Risk factor detection |
| `ValidatorTest.php` | 4 | ❌ No | Core validator class |
| `ModelFactoryTest.php` | 4 | ❌ No | Model creation |

## Quick Commands

```bash
# Run all tests
php tests\run-tests.php

# Or use the batch file
test.bat

# Check PHP version (needs 7.4+)
C:\xampp\php\php.exe -v
```

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "php is not recognized" | Use: `C:\xampp\php\php.exe tests\run-tests.php` |
| "Class not found" | Make sure you're in project root: `cd C:\xampp\htdocs\projecty` |
| Database errors | Start MySQL in XAMPP (only needed for GradeModel tests) |
| Tests hang | Check if database is running |

## Need More Help?

See `tests/HOW_TO_RUN_TESTS.md` for detailed instructions.

