@echo off
REM Create test runner file
cd /d "%~dp0"

echo Creating run-tests.php file...

C:\xampp\php\php.exe tests\generate-runner.php > tests\run-tests.php 2>nul

if exist tests\run-tests.php (
    echo ✅ File created successfully!
    echo.
    echo You can now run: C:\xampp\php\php.exe tests\run-tests.php
) else (
    echo ❌ Failed to create file.
    echo.
    echo Please manually create tests\run-tests.php
    echo Copy content from: tests\RUN_TESTS_CONTENT.txt
    echo.
    pause
    exit /b 1
)

pause

