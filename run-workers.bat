@echo off
cd /d C:\laragon\www\mp

echo Running queue worker...
start php artisan queue:work

echo Running message checker loop...
:loop
php artisan app:check-messages
timeout /t 10 >nul
goto loop
