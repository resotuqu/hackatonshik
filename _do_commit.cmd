@echo off
setlocal
cd /d "%~dp0"
set TEMP=%cd%\tmp
set TMP=%cd%\tmp
if not exist "%TEMP%" mkdir "%TEMP%"
php _run_commit.php
exit /b %ERRORLEVEL%
