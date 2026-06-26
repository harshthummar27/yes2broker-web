@echo off
setlocal

echo Repairing yes2broker database...
echo This fixes MySQL error 1932 (table doesn't exist in engine).
echo.

set MYSQL=c:\xampp\mysql\bin\mysql.exe
if not exist "%MYSQL%" (
    echo MySQL not found at %MYSQL%
    exit /b 1
)

cd /d "%~dp0.."

echo Dropping broken tables...
php -r "require 'vendor/autoload.php'; $app=require 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); DB::statement('SET FOREIGN_KEY_CHECKS=0'); foreach (DB::select('SHOW TABLES') as $t) { $name=array_values((array)$t)[0]; DB::statement('DROP TABLE IF EXISTS `'.$name.'`'); echo 'Dropped: '.$name.PHP_EOL; } DB::statement('SET FOREIGN_KEY_CHECKS=1');"

echo Removing orphaned InnoDB files...
if exist "c:\xampp\mysql\data\yes2broker\*.ibd" del /q "c:\xampp\mysql\data\yes2broker\*.ibd"

echo Running migrations...
php artisan migrate
if errorlevel 1 exit /b 1

echo Seeding data...
php artisan db:seed
if errorlevel 1 exit /b 1

echo Syncing property images from storage...
php artisan properties:sync-images
if errorlevel 1 exit /b 1

echo Importing property details from CSV...
php artisan properties:import-csv
if errorlevel 1 exit /b 1

echo.
echo Database repaired successfully.
echo Admin login: admin@yes2broker.in / password
endlocal
