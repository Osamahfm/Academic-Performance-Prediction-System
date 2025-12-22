@echo off
REM Create test runner file
cd /d "%~dp0"

echo Creating run-tests.php...

REM Use PHP script to generate the file
C:\xampp\php\php.exe create-runner.php

if exist tests\run-tests.php (
    echo.
    echo ✅ File created successfully!
    echo.
    echo Run tests with: C:\xampp\php\php.exe tests\run-tests.php
) else (
    echo.
    echo ❌ Failed. Please create manually:
    echo 1. Open tests\RUN_TESTS_CONTENT.txt
    echo 2. Copy content starting from line 3
    echo 3. Save as tests\run-tests.php
)

pause

