# Yes2Broker — PHP (Laravel) Project

Real estate brokerage platform converted from WordPress ([yes2broker.in](https://yes2broker.in/)) to Laravel 12.

## Requirements

- PHP 8.2+ (XAMPP)
- MySQL 5.7+ / MariaDB
- Composer (included as `composer.phar`)

## Local Setup

### 1. Create database

Open phpMyAdmin (`http://localhost/phpmyadmin`) and run:

```sql
CREATE DATABASE yes2broker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Configure environment

Copy `.env.example` to `.env` if needed. Default settings:

```
APP_URL=http://localhost/yes2broker/public
DB_DATABASE=yes2broker
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Install & migrate

```powershell
cd c:\xampp\htdocs\yes2broker
C:\xampp\php\php.exe composer.phar install
C:\xampp\php\php.exe artisan migrate
C:\xampp\php\php.exe artisan storage:link
```

### 4. Access the site

- **XAMPP Apache:** http://localhost/yes2broker/public
- **Artisan serve:** `C:\xampp\php\php.exe artisan serve` → http://127.0.0.1:8000

## Documentation

| File | Description |
|------|-------------|
| [docs/SITE-AUDIT.md](docs/SITE-AUDIT.md) | Complete review of the WordPress site |
| [docs/ROADMAP.md](docs/ROADMAP.md) | Phased conversion plan |
| [docs/GO-LIVE.md](docs/GO-LIVE.md) | Production deployment & WordPress cutover guide |

## Project Structure

```
yes2broker/
├── app/                 # Application logic
├── database/            # Migrations & seeders
├── docs/                # Audit & roadmap
├── public/              # Web root (point Apache here)
├── resources/views/     # Blade templates
├── routes/web.php       # URL routes
└── .env                 # Environment config
```

## Composer Commands

```powershell
# Use local composer.phar
C:\xampp\php\php.exe composer.phar require filament/filament
C:\xampp\php\php.exe artisan make:model Property -m
```

## WordPress Migration Checklist

Before building features, export from WordPress:

- [ ] MySQL database dump (`.sql`)
- [ ] `wp-content/uploads/` media files
- [ ] List of active plugins (especially property plugin)
- [ ] Theme CSS for design reference
