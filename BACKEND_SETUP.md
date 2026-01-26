# ISP Billing System - Backend Setup Guide

## Prerequisites

- PHP >= 8.2
- Composer
- MySQL 8.0+ atau PostgreSQL 14+
- Redis (for queues and cache)
- Node.js & NPM (for asset compilation)

## Installation Steps

### 1. Create Laravel Project

```bash
cd c:\Users\inter\Documents\Billing\vue-argon-dashboard
mkdir backend
cd backend
composer create-project laravel/laravel . "11.*"
```

### 2. Install Required Packages

```bash
# Authentication
composer require laravel/sanctum

# Mikrotik API
composer require benconda/routeros-php-api

# Payment Gateways
composer require midtrans/midtrans-php
composer require xendit/xendit-php

# Other useful packages
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require barryvdh/laravel-cors
composer require tymon/jwt-auth
```

### 3. Environment Configuration

Create `.env` file:

```env
APP_NAME="ISP Billing System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=isp_billing
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=database
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@ispbilling.com"
MAIL_FROM_NAME="${APP_NAME}"

# Midtrans
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true

# Xendit
XENDIT_SECRET_KEY=your_secret_key
XENDIT_PUBLIC_KEY=your_public_key

# JWT
JWT_SECRET=your_jwt_secret

# App Settings
SUPER_ADMIN_EMAIL=superadmin@ispbilling.com
SUPER_ADMIN_PASSWORD=SecurePassword123!

# File Upload
MAX_UPLOAD_SIZE=10240
```

### 4. Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE isp_billing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Import schema
mysql -u root -p isp_billing < ../database_schema.sql

# Or run migrations (we'll create these)
php artisan migrate
php artisan db:seed
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Configure Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 7. Storage Link

```bash
php artisan storage:link
```

### 8. Queue Configuration

```bash
# Install Supervisor (Ubuntu/Debian)
sudo apt-get install supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/isp-billing-worker.conf
```

Add:

```ini
[program:isp-billing-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/backend/storage/logs/worker.log
stopwaitsecs=3600
```

Then:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start isp-billing-worker:*
```

### 9. Schedule Cron Job

Add to crontab:

```bash
crontab -e
```

Add:

```
* * * * * cd /path/to/backend && php artisan schedule:run >> /dev/null 2>&1
```

### 10. File Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Development Server

### Option 1: Laravel Built-in Server

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Option 2: Laravel Octane (High Performance)

```bash
composer require laravel/octane
php artisan octane:install
php artisan octane:start --host=0.0.0.0 --port=8000
```

## API Documentation

### Generate API Documentation

```bash
composer require darkaonline/l5-swagger
php artisan l5-swagger:generate
```

Access at: `http://localhost:8000/api/documentation`

## Testing

### Setup Testing Environment

```bash
# Create test database
CREATE DATABASE isp_billing_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Run tests
php artisan test
```

## Production Deployment

### 1. Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 2. Set Permissions

```bash
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

### 3. Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com;
    root /var/www/isp-billing/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4. SSL Certificate

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

## Security Checklist

- [x] Change default credentials
- [x] Enable SSL/TLS
- [x] Set APP_DEBUG=false in production
- [x] Configure CORS properly
- [x] Enable rate limiting
- [x] Set up firewall rules
- [x] Regular backups
- [x] Keep dependencies updated
- [x] Enable 2FA for admin accounts
- [x] Implement audit logging

## Backup Strategy

### Database Backup Script

Create `backup.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/isp-billing"
DB_NAME="isp_billing"

mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u root -p$DB_PASSWORD $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/isp-billing/backend/storage

# Keep only last 30 days
find $BACKUP_DIR -name "*.gz" -mtime +30 -delete

echo "Backup completed: $DATE"
```

Schedule daily:

```bash
0 2 * * * /path/to/backup.sh >> /var/log/backup.log 2>&1
```

## Monitoring

### Setup Monitoring

```bash
# Install Laravel Telescope (Development)
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate

# Install Laravel Horizon (Queue Monitoring)
composer require laravel/horizon
php artisan horizon:install
```

## Troubleshooting

### Common Issues

1. **Permission Denied**

   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

2. **Queue Not Processing**

   ```bash
   sudo supervisorctl restart isp-billing-worker:*
   php artisan queue:restart
   ```

3. **Cache Issues**

   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Database Connection**
   ```bash
   # Test connection
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

## Next Steps

1. Create migrations
2. Create models and relationships
3. Create controllers
4. Create API routes
5. Implement authentication
6. Create middleware
7. Implement services
8. Create jobs and events
9. Write tests
10. Create seeders
