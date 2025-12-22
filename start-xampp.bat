@echo off
echo Starting XAMPP Services...
echo.

cd /d C:\xampp

echo Starting Apache...
start /B apache\bin\httpd.exe

echo Starting MySQL...
start /B mysql\bin\mysqld.exe --console

echo.
echo XAMPP services are starting...
echo Please wait a few seconds for services to fully start.
echo.
echo You can now visit: http://localhost/projecty/
echo.
pause

