# Unit Test Suite Documentation

## Overview

This test suite provides comprehensive automated unit tests for the EduPredict application's core business logic. The tests cover critical use cases, including both happy paths (success scenarios) and edge cases (failure/error scenarios).

## Test Structure

```
tests/
├── run-tests.php              # Main test runner
├── Unit/
│   ├── ValidatorTest.php      # Validator class tests
│   ├── ModelFactoryTest.php   # ModelFactory tests
│   ├── KNNPredictorTest.php   # ML algorithm tests
│   ├── ValidationStrategyTest.php  # Validation strategy tests
│   ├── GradeModelTest.php     # Grade calculations tests
│   └── PredictionServiceTest.php  # Prediction service tests
└── README.md                  # This file
```

## Running Tests

### Run All Tests
```bash
php tests/run-tests.php
```

### Run Individual Test File
```bash
php -r "require 'tests/Unit/KNNPredictorTest.php'; \$test = new KNNPredictorTest(); \$test->runAll();"
```

## Test Coverage

### 1. KNNPredictor Tests (`KNNPredictorTest.php`)
**Purpose**: Tests the core Machine Learning algorithm for academic performance prediction.

**Test Cases**:
- ✅ Predict with empty training data (edge case)
- ✅ Load training data (happy path)
- ✅ Predict with valid training data (happy path)
- ✅ Predict with different K values (edge case)
- ✅ Predict course grade with historical grades (happy path)
- ✅ Predict course grade without history (edge case)
- ✅ Predict with extreme values (edge case)
- ✅ Predict with identical training data (edge case)
- ✅ Predict with single training record (edge case)

**Key Validations**:
- Predictions are within valid range (0-100)
- Risk levels are valid ('low', 'medium', 'high')
- Confidence scores are between 0 and 1
- Handles edge cases gracefully

### 2. Validation Strategy Tests (`ValidationStrategyTest.php`)
**Purpose**: Tests validation logic for all entity types (User, Course, Grade, Contact).

**Test Cases**:

**User Validation**:
- ✅ Valid user data (happy path)
- ✅ Missing required fields (edge case)
- ✅ Invalid email format (edge case)
- ✅ Short password (edge case)
- ✅ Invalid role (edge case)

**Course Validation**:
- ✅ Valid course data (happy path)
- ✅ Missing required fields (edge case)
- ✅ Invalid credits range (edge case)
- ✅ Short course code (edge case)

**Grade Validation**:
- ✅ Valid grade data (happy path)
- ✅ Grade exceeds maximum (edge case)
- ✅ Negative grade (edge case)
- ✅ Invalid assignment type (edge case)
- ✅ Zero max grade (edge case)

**Contact Validation**:
- ✅ Valid contact data (happy path)
- ✅ Short message (edge case)
- ✅ Invalid email (edge case)

**Key Validations**:
- Required fields are enforced
- Format validations work correctly
- Range validations are applied
- Error messages are generated

### 3. GradeModel Tests (`GradeModelTest.php`)
**Purpose**: Tests grade calculation and retrieval logic.

**Test Cases**:
- ✅ Calculate average grade for student (happy path)
- ✅ Calculate average with no grades (edge case)
- ✅ Calculate average for specific course (happy path)
- ✅ Get grades by student (happy path)
- ✅ Get grades by course (happy path)
- ✅ Average with different max grades (edge case)
- ✅ Get grades with no results (edge case)

**Key Validations**:
- Average calculations are correct
- Handles missing data gracefully
- Course-specific calculations work
- Returns appropriate data structures

### 4. PredictionService Tests (`PredictionServiceTest.php`)
**Purpose**: Tests prediction orchestration and risk factor identification.

**Test Cases**:
- ✅ Identify risk factors - low GPA (edge case)
- ✅ Identify risk factors - low attendance (edge case)
- ✅ Identify risk factors - low average grade (edge case)
- ✅ Identify risk factors - incomplete assignments (edge case)
- ✅ Identify risk factors - below passing grade (edge case)
- ✅ Identify risk factors - no risks (happy path)
- ✅ Identify risk factors - multiple risks (edge case)
- ✅ Get student features structure (happy path)
- ✅ Get student features - non-existent student (edge case)

**Key Validations**:
- Risk factors are correctly identified
- Multiple risk factors are detected
- Handles edge cases appropriately
- Feature extraction works correctly

### 5. Validator Tests (`ValidatorTest.php`)
**Purpose**: Tests core validation functionality.

**Test Cases**:
- ✅ Required field validation
- ✅ Email format validation
- ✅ Minimum length validation
- ✅ Numeric validation

### 6. ModelFactory Tests (`ModelFactoryTest.php`)
**Purpose**: Tests model factory pattern implementation.

**Test Cases**:
- ✅ Create UserModel
- ✅ Create StudentModel
- ✅ Singleton behavior
- ✅ Invalid model type handling

## Test Methodology

### Happy Path Tests
These tests verify that the system works correctly under normal conditions:
- Valid inputs produce expected outputs
- Business logic executes successfully
- Data is processed correctly

### Edge Case Tests
These tests verify error handling and boundary conditions:
- Invalid inputs are rejected
- Missing data is handled gracefully
- Extreme values don't break the system
- Error messages are appropriate

### Isolation
Tests are designed to run in isolation:
- Each test is independent
- Tests clean up after themselves
- Database tests use isolated test data (high IDs)
- No test depends on another test's execution

## Database Testing Notes

Some tests (`GradeModelTest`) require database access. These tests:
- Use high-numbered IDs (999999+) to avoid conflicts
- Clean up test data after execution
- Can be run against a test database or production database
- Should ideally use a separate test database in production

## Mocking Strategy

Currently, tests use:
- **Reflection** for testing private methods (PredictionService)
- **Actual database** for integration-style tests (GradeModel)
- **Isolated test data** to avoid conflicts

For production use, consider:
- Using a mocking framework (PHPUnit Mock Objects)
- Separate test database
- Database transaction rollback for isolation

## Adding New Tests

To add a new test:

1. Create a new test file in `tests/Unit/`
2. Follow the naming convention: `{ClassName}Test.php`
3. Implement a `runAll()` method
4. Add the test to `tests/run-tests.php`
5. Use `assert()` for assertions
6. Include both happy path and edge case tests

Example:
```php
class MyClassTest {
    public function testHappyPath() {
        // Test code
        assert($condition, 'Error message');
        echo "✓ Test name passed\n";
    }
    
    public function runAll() {
        echo "Running MyClass Tests...\n\n";
        try {
            $this->testHappyPath();
            echo "\n✅ All MyClass tests passed!\n";
        } catch (Exception $e) {
            echo "\n❌ Test failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}
```

## Test Results

When tests pass, you'll see:
```
✅ All {ClassName} tests passed!
```

When tests fail, you'll see:
```
❌ Test failed: {error message}
```

## Continuous Integration

These tests can be integrated into CI/CD pipelines:
```bash
# Exit with error code if tests fail
php tests/run-tests.php || exit 1
```

## Future Improvements

- [ ] Add PHPUnit framework for more advanced features
- [ ] Implement database mocking for complete isolation
- [ ] Add code coverage reporting
- [ ] Create integration tests for controllers
- [ ] Add performance/load tests
- [ ] Implement test data fixtures

## Dependencies

- PHP 7.4+
- Database connection (for some tests)
- Application autoloader

## Notes

- Tests use `assert()` which can be disabled in production PHP
- Some tests modify database - use test database in production
- Reflection is used to test private methods - consider making them protected/public for testing





