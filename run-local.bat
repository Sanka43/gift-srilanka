@echo off
title Gifts Sri Lanka - Local Server
echo ===================================================
echo Starting MySQL (XAMPP)...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo MySQL is already running.
) else (
    start "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
    timeout /t 2 /nobreak >nul
    echo MySQL started.
)

echo Starting Laravel Application on http://127.0.0.1:8000 ...
start http://127.0.0.1:8000
"C:\xampp\php\php.exe" artisan serve --host=127.0.0.1 --port=8000
pause
