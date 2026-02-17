@echo off
echo ========================================
echo Cleanup Landing Page Duplicates
echo ========================================
echo.

cd /d "%~dp0isp-billing-backend"

echo [1/3] Deleting duplicate landing pages...
echo This will keep only the latest landing page for super admin.
echo.

php artisan tinker --execute="DB::table('landing_pages')->whereNull('isp_id')->orderBy('id', 'desc')->skip(1)->delete(); echo 'Duplicates deleted. Kept latest entry only.';"

if errorlevel 1 (
    echo ERROR: Failed to delete duplicates!
    pause
    exit /b 1
)

echo.
echo [2/3] Running seeder to ensure correct data...
php artisan db:seed --class=LandingPageSeeder --force

echo.
echo [3/3] Clearing cache...
php artisan cache:clear

echo.
echo ========================================
echo SUCCESS! Duplicates cleaned up!
echo ========================================
echo.
echo Now you have only ONE landing page for super admin.
echo.
pause
