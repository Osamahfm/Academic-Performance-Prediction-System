@echo off
REM Quick Test Runner for Windows
REM Double-click this file to run all unit tests

cd /d "%~dp0"
echo ========================================
echo Running EduPredict Unit Tests
echo ========================================
echo.

REM Try to find PHP
if exist "C:\xampp\php\php.exe" (
    C:\xampp\php\php.exe tests\run-tests.php
) else if exist "C:\php\php.exe" (
    C:\php\php.exe tests\run-tests.php
) else (
    echo PHP not found. Please install PHP or update the path in this file.
    echo.
    echo Trying to use 'php' from PATH...
    php tests\run-tests.php
)

echo.
echo ========================================
echo Tests completed!
echo ========================================
pause





