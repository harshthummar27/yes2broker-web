@echo off
REM Start XAMPP MySQL if not already running (Windows local dev)
C:\xampp\mysql\bin\mysql.exe -u root -e "SELECT 1;" >nul 2>&1
if %errorlevel%==0 (
    echo MySQL is already running.
    exit /b 0
)

echo Starting MySQL...
start "" /B C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini
timeout /t 5 /nobreak >nul

C:\xampp\mysql\bin\mysql.exe -u root -e "SELECT 1;" >nul 2>&1
if %errorlevel%==0 (
    echo MySQL started successfully.
) else (
    echo MySQL failed to start. Open XAMPP Control Panel and click Start next to MySQL.
    exit /b 1
)
