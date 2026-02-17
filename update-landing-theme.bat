@echo off
echo ========================================
echo ISP Billing - Update Landing Page Theme
echo ========================================
echo.

cd /d "%~dp0isp-billing-backend"

echo [1/3] Checking database connection...
php artisan db:show 2>nul
if errorlevel 1 (
    echo ERROR: Cannot connect to database!
    echo Please check your .env file configuration.
    pause
    exit /b 1
)

echo [2/3] Updating landing page data with green theme...
php artisan db:seed --class=LandingPageSeeder --force

if errorlevel 1 (
    echo ERROR: Failed to run seeder!
    pause
    exit /b 1
)

echo [3/3] Clearing cache...
php artisan cache:clear
php artisan config:clear

echo.
echo ========================================
echo SUCCESS! Landing page theme updated!
echo ========================================
echo.
echo Next steps:
echo 1. Refresh your browser at http://localhost:8081/
echo 2. Press Ctrl+Shift+R to hard refresh
echo 3. You should see green theme now!
echo.
pause
