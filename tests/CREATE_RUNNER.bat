@echo off
REM Create test runner file manually
REM Run this as Administrator if you get permission errors

cd /d "%~dp0"

echo Creating run-tests.php file...

(
echo <?php
echo /**
echo  * Test Runner
echo  * Executes all unit tests
echo  */
echo.
echo // Set up autoloader first
echo spl_autoload_register^(function ^($class^) {
echo     $prefix = 'App\\';
echo     $baseDir = __DIR__ . '/../app/';
echo     $len = strlen^($prefix^);
echo     if ^(strncmp^($prefix, $class, $len^) !== 0^) {
echo         return;
echo     }
echo     $relativeClass = substr^($class, $len^);
echo     $file = $baseDir . str_replace^('\\', '/', $relativeClass^) . '.php';
echo     if ^(file_exists^($file^)^) {
echo         require $file;
echo     }
echo }^);
echo.
echo // Load core dependencies
echo require_once __DIR__ . '/../app/core/Database.php';
echo require_once __DIR__ . '/../app/core/Model.php';
echo require_once __DIR__ . '/../app/core/Validator.php';
echo require_once __DIR__ . '/../app/core/Factory/ModelFactory.php';
echo require_once __DIR__ . '/../app/core/Strategy/ValidationStrategy.php';
echo require_once __DIR__ . '/../app/core/ML/KNNPredictor.php';
echo require_once __DIR__ . '/../app/models/UserModel.php';
echo require_once __DIR__ . '/../app/models/StudentModel.php';
echo require_once __DIR__ . '/../app/models/GradeModel.php';
echo require_once __DIR__ . '/../app/models/PredictionModel.php';
echo require_once __DIR__ . '/../app/models/AlertModel.php';
echo require_once __DIR__ . '/../app/models/CourseModel.php';
echo require_once __DIR__ . '/../app/models/ContactModel.php';
echo require_once __DIR__ . '/../app/models/MenuModel.php';
echo require_once __DIR__ . '/../app/services/PredictionService.php';
echo.
echo try {
echo     ob_start^(^);
echo     \App\Core\Database::getInstance^(^);
echo     ob_end_clean^(^);
echo } catch ^(Exception $e^) {
echo }
echo.
echo echo "========================================\n";
echo echo "EduPredict Unit Test Suite\n";
echo echo "========================================\n\n";
echo.
echo require_once __DIR__ . '/Unit/ValidatorTest.php';
echo require_once __DIR__ . '/Unit/ModelFactoryTest.php';
echo require_once __DIR__ . '/Unit/KNNPredictorTest.php';
echo require_once __DIR__ . '/Unit/ValidationStrategyTest.php';
echo require_once __DIR__ . '/Unit/GradeModelTest.php';
echo require_once __DIR__ . '/Unit/PredictionServiceTest.php';
echo.
echo $tests = [
echo     new ValidatorTest^(^),
echo     new ModelFactoryTest^(^),
echo     new KNNPredictorTest^(^),
echo     new ValidationStrategyTest^(^),
echo     new GradeModelTest^(^),
echo     new PredictionServiceTest^(^)
echo ];
echo.
echo $passed = 0;
echo $failed = 0;
echo.
echo foreach ^($tests as $test^) {
echo     try {
echo         $test-^>runAll^(^);
echo         $passed++;
echo     } catch ^(Exception $e^) {
echo         $failed++;
echo         echo "❌ Test suite failed: " . $e-^>getMessage^(^) . "\n";
echo     }
echo     echo "\n";
echo }
echo.
echo echo "========================================\n";
echo echo "Test Summary\n";
echo echo "========================================\n";
echo echo "Passed: {$passed}\n";
echo echo "Failed: {$failed}\n";
echo echo "Total: " . ^($passed + $failed^) . "\n";
echo echo "========================================\n";
) > run-tests.php

if exist run-tests.php (
    echo ✅ File created successfully!
    echo You can now run: php run-tests.php
) else (
    echo ❌ Failed to create file. Try running as Administrator.
)

pause





