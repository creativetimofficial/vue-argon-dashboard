# ISP Billing System - Complete Deployment Guide

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Server Setup](#server-setup)
3. [Backend Deployment](#backend-deployment)
4. [Frontend Deployment](#frontend-deployment)
5. [SSL Configuration](#ssl-configuration)
6. [Performance Optimization](#performance-optimization)
7. [Monitoring & Maintenance](#monitoring--maintenance)
8. [Backup Strategy](#backup-strategy)
9. [Troubleshooting](#troubleshooting)

---

## 🎯 Prerequisites

### Recommended VPS Specifications

#### **For Small ISP (up to 500 customers)**

- **Provider**: Niagahoster VPS Bisnis atau DigitalOcean
- **CPU**: 2 vCPU
- **RAM**: 4 GB
- **Storage**: 80 GB SSD
- **Bandwidth**: Unlimited
- **OS**: Ubuntu 22.04 LTS
- **Cost**: ~Rp 150,000/bulan (Niagahoster) or $12/month (DigitalOcean)

#### **For Medium ISP (500-2000 customers)**

- **CPU**: 4 vCPU
- **RAM**: 8 GB
- **Storage**: 160 GB SSD
- **Cost**: ~Rp 300,000/bulan or $24/month

#### **For Large ISP (2000+ customers)**

- **CPU**: 8 vCPU
- **RAM**: 16 GB
- **Storage**: 320 GB SSD
- **Cost**: ~Rp 600,000/bulan or $48/month
- **Consider**: Load balancing and database replication

### Software Requirements

- Ubuntu 22.04 LTS
- PHP 8.2+
- MySQL 8.0+ or PostgreSQL 14+
- Node.js 18+
- Nginx
- Redis
- Supervisor
- Certbot (for SSL)

---

## 🖥️ Server Setup

### 1. Initial Server Configuration

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install basic tools
sudo apt install -y software-properties-common curl wget git unzip vim

# Set timezone
sudo timedatectl set-timezone Asia/Jakarta

# Create swap file (recommended for servers with < 8GB RAM)
sudo fallocate -l 4G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

### 2. Install PHP 8.2

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
  php8.2-mysql php8.2-pgsql php8.2-zip php8.2-gd php8.2-mbstring \
  php8.2-curl php8.2-xml php8.2-bcmath php8.2-redis php8.2-intl

# Verify installation
php -v
```

### 3. Install MySQL 8.0

```bash
# Install MySQL
sudo apt install -y mysql-server

# Secure MySQL installation
sudo mysql_secure_installation

# Login to MySQL and create database
sudo mysql -u root -p

# In MySQL prompt:
CREATE DATABASE isp_billing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ispbilling'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';
GRANT ALL PRIVILEGES ON isp_billing.* TO 'ispbilling'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Install Redis

```bash
# Install Redis
sudo apt install -y redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
# Set: supervised systemd
# Set: maxmemory 256mb
# Set: maxmemory-policy allkeys-lru

# Restart Redis
sudo systemctl restart redis-server
sudo systemctl enable redis-server

# Test Redis
redis-cli ping
```

### 5. Install Node.js 18

```bash
# Install Node.js via NodeSource
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verify installation
node -v
npm -v
```

### 6. Install Nginx

```bash
# Install Nginx
sudo apt install -y nginx

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 7. Install Composer

```bash
# Download and install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify installation
composer --version
```

### 8. Install Supervisor

```bash
# Install Supervisor
sudo apt install -y supervisor

# Enable Supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

---

## 🚀 Backend Deployment

### 1. Clone Repository

```bash
# Create application directory
sudo mkdir -p /var/www/isp-billing
sudo chown -R $USER:$USER /var/www/isp-billing

# Clone repository (adjust URL to your repo)
cd /var/www/isp-billing
git clone https://github.com/yourusername/isp-billing.git .

# Or upload files via SFTP/SCP
```

### 2. Setup Laravel Backend

```bash
cd /var/www/isp-billing/backend

# Install dependencies
composer install --optimize-autoloader --no-dev

# Copy environment file
cp .env.example .env

# Edit .env file
nano .env
```

Update `.env`:

```env
APP_NAME="ISP Billing System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=isp_billing
DB_USERNAME=ispbilling
DB_PASSWORD=YourStrongPassword123!

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Email configuration (use your SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com

# Midtrans
MIDTRANS_SERVER_KEY=your_production_server_key
MIDTRANS_CLIENT_KEY=your_production_client_key
MIDTRANS_IS_PRODUCTION=true

# Xendit (optional)
XENDIT_SECRET_KEY=your_production_secret_key
```

Continue setup:

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Create storage link
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Set permissions
sudo chown -R www-data:www-data /var/www/isp-billing/backend
sudo chmod -R 755 /var/www/isp-billing/backend
sudo chmod -R 775 /var/www/isp-billing/backend/storage
sudo chmod -R 775 /var/www/isp-billing/backend/bootstrap/cache
```

### 3. Configure Queue Worker

Create supervisor configuration:

```bash
sudo nano /etc/supervisor/conf.d/isp-billing-worker.conf
```

Add:

```ini
[program:isp-billing-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/isp-billing/backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/isp-billing/backend/storage/logs/worker.log
stopwaitsecs=3600
```

Start worker:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start isp-billing-worker:*
```

### 4. Configure Cron Job

```bash
sudo crontab -e
```

Add:

```
* * * * * cd /var/www/isp-billing/backend && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Configure Nginx for Backend

```bash
sudo nano /etc/nginx/sites-available/isp-billing-api
```

Add:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name api.yourdomain.com;
    root /var/www/isp-billing/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Increase upload size for file uploads
    client_max_body_size 20M;

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
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/isp-billing-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🎨 Frontend Deployment

### 1. Build Frontend

```bash
cd /var/www/isp-billing/frontend

# Install dependencies
npm install

# Create production environment file
nano .env.production
```

Add to `.env.production`:

```env
VUE_APP_API_URL=https://api.yourdomain.com/api
VUE_APP_NAME=ISP Billing System
```

Build:

```bash
# Build for production
npm run build

# The build files will be in /dist folder
```

### 2. Configure Nginx for Frontend

```bash
sudo nano /etc/nginx/sites-available/isp-billing-frontend
```

Add:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/isp-billing/frontend/dist;

    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript application/json;
    gzip_comp_level 6;
    gzip_min_length 1000;
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/isp-billing-frontend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🔒 SSL Configuration

### 1. Install Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 2. Obtain SSL Certificates

```bash
# For API
sudo certbot --nginx -d api.yourdomain.com

# For Frontend
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 3. Auto-renewal

Certbot automatically sets up renewal. Test it:

```bash
sudo certbot renew --dry-run
```

---

## ⚡ Performance Optimization

### 1. PHP-FPM Optimization

```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

Adjust based on your RAM:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

### 2. MySQL Optimization

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Add/modify:

```ini
[mysqld]
innodb_buffer_pool_size = 1G  # 70% of available RAM
innodb_log_file_size = 256M
max_connections = 200
query_cache_size = 0
query_cache_type = 0
```

Restart MySQL:

```bash
sudo systemctl restart mysql
```

### 3. Redis Optimization

```bash
sudo nano /etc/redis/redis.conf
```

Set:

```
maxmemory 512mb
maxmemory-policy allkeys-lru
```

Restart Redis:

```bash
sudo systemctl restart redis-server
```

---

## 📊 Monitoring & Maintenance

### 1. Log Monitoring

```bash
# Laravel logs
tail -f /var/www/isp-billing/backend/storage/logs/laravel.log

# Nginx access logs
tail -f /var/log/nginx/access.log

# Nginx error logs
tail -f /var/log/nginx/error.log

# Queue worker logs
tail -f /var/www/isp-billing/backend/storage/logs/worker.log
```

### 2. Health Checks

Create monitoring script:

```bash
sudo nano /usr/local/bin/check-health.sh
```

Add:

```bash
#!/bin/bash

# Check if Nginx is running
if ! systemctl is-active --quiet nginx; then
    echo "Nginx is down!" | mail -s "Alert: Nginx Down" admin@yourdomain.com
    systemctl restart nginx
fi

# Check if PHP-FPM is running
if ! systemctl is-active --quiet php8.2-fpm; then
    echo "PHP-FPM is down!" | mail -s "Alert: PHP-FPM Down" admin@yourdomain.com
    systemctl restart php8.2-fpm
fi

# Check if MySQL is running
if ! systemctl is-active --quiet mysql; then
    echo "MySQL is down!" | mail -s "Alert: MySQL Down" admin@yourdomain.com
    systemctl restart mysql
fi

# Check if Redis is running
if ! systemctl is-active --quiet redis-server; then
    echo "Redis is down!" | mail -s "Alert: Redis Down" admin@yourdomain.com
    systemctl restart redis-server
fi

# Check if queue workers are running
if ! supervisorctl status isp-billing-worker:* | grep -q RUNNING; then
    echo "Queue workers are down!" | mail -s "Alert: Workers Down" admin@yourdomain.com
    supervisorctl restart isp-billing-worker:*
fi
```

Make executable and schedule:

```bash
sudo chmod +x /usr/local/bin/check-health.sh
sudo crontab -e
```

Add:

```
*/5 * * * * /usr/local/bin/check-health.sh
```

---

## 💾 Backup Strategy

### 1. Database Backup Script

```bash
sudo nano /usr/local/bin/backup-database.sh
```

Add:

```bash
#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/isp-billing/database"
DB_NAME="isp_billing"
DB_USER="ispbilling"
DB_PASS="YourStrongPassword123!"

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Database backup completed: $DATE"
```

### 2. Files Backup Script

```bash
sudo nano /usr/local/bin/backup-files.sh
```

Add:

```bash
#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/isp-billing/files"
SOURCE_DIR="/var/www/isp-billing"

mkdir -p $BACKUP_DIR

# Backup application files and storage
tar -czf $BACKUP_DIR/files_$DATE.tar.gz \
    $SOURCE_DIR/backend/storage \
    $SOURCE_DIR/backend/.env \
    --exclude=$SOURCE_DIR/backend/storage/logs \
    --exclude=$SOURCE_DIR/backend/storage/framework/cache

# Keep only last 7 days (files are larger)
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Files backup completed: $DATE"
```

### 3. Schedule Backups

```bash
sudo chmod +x /usr/local/bin/backup-database.sh
sudo chmod +x /usr/local/bin/backup-files.sh

sudo crontab -e
```

Add:

```
# Daily database backup at 2 AM
0 2 * * * /usr/local/bin/backup-database.sh >> /var/log/backup-db.log 2>&1

# Daily files backup at 3 AM
0 3 * * * /usr/local/bin/backup-files.sh >> /var/log/backup-files.log 2>&1
```

### 4. Remote Backup (Optional but Recommended)

Use rclone to sync backups to cloud storage:

```bash
# Install rclone
curl https://rclone.org/install.sh | sudo bash

# Configure rclone (follow prompts)
rclone config

# Create sync script
sudo nano /usr/local/bin/sync-to-cloud.sh
```

Add:

```bash
#!/bin/bash
rclone sync /backups/isp-billing remote:isp-billing-backups --progress
```

Schedule:

```bash
sudo chmod +x /usr/local/bin/sync-to-cloud.sh
sudo crontab -e
```

Add:

```
# Sync to cloud at 4 AM
0 4 * * * /usr/local/bin/sync-to-cloud.sh >> /var/log/cloud-sync.log 2>&1
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. 502 Bad Gateway

```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

#### 2. Slow Performance

```bash
# Check server resources
htop

# Check MySQL slow queries
sudo mysql -e "SHOW PROCESSLIST;"

# Optimize Laravel
cd /var/www/isp-billing/backend
php artisan optimize:clear
php artisan optimize
```

#### 3. Queue Not Processing

```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart isp-billing-worker:*

# Check Redis
redis-cli ping
```

#### 4. Permission Issues

```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/isp-billing/backend
sudo chmod -R 755 /var/www/isp-billing/backend
sudo chmod -R 775 /var/www/isp-billing/backend/storage
sudo chmod -R 775 /var/www/isp-billing/backend/bootstrap/cache
```

---

## 📦 Recommended Hosting Providers

### 1. **Niagahoster VPS** (Indonesia - Best for Indonesian ISPs)

- **Pros**:
  - Local support (Bahasa Indonesia)
  - Local data center (fast for Indonesia)
  - Good customer service
  - Competitive pricing
- **Cons**:
  - Limited international locations
- **Recommended Plan**: VPS Bisnis (Rp 150k/month)
- **Website**: https://www.niagahoster.co.id

### 2. **DigitalOcean** (Global - Most Popular)

- **Pros**:
  - Excellent documentation
  - Reliable infrastructure
  - Easy to scale
  - Singapore datacenter available
- **Cons**:
  - No Indonesian support
  - Payment in USD
- **Recommended Plan**: Basic Droplet $12/month (2GB RAM)
- **Website**: https://www.digitalocean.com
- **Bonus**: Get $200 credit for 60 days with referral

### 3. **Vultr** (Global - Fast Performance)

- **Pros**:
  - High performance
  - Singapore location
  - Hourly billing
  - Good price/performance ratio
- **Cons**:
  - No Indonesian support
- **Recommended Plan**: Cloud Compute $12/month
- **Website**: https://www.vultr.com

### 4. **AWS Lightsail** (Enterprise Grade)

- **Pros**:
  - Highly scalable
  - Integration with other AWS services
  - Singapore region
  - First 3 months free
- **Cons**:
  - More complex
  - Can be expensive if not managed well
- **Recommended Plan**: $10/month (2GB RAM)
- **Website**: https://aws.amazon.com/lightsail

---

## 🎯 Post-Deployment Checklist

- [ ] SSL certificates installed and working
- [ ] Database backups scheduled
- [ ] File backups scheduled
- [ ] Queue workers running
- [ ] Cron jobs configured
- [ ] Firewall configured (UFW)
- [ ] Fail2ban installed for security
- [ ] Monitoring setup
- [ ] Email sending working
- [ ] Payment gateway tested
- [ ] Mikrotik integration tested
- [ ] Super admin login tested
- [ ] Documentation updated
- [ ] Team trained

---

## 🔐 Security Hardening

### 1. Install and Configure UFW

```bash
# Install UFW
sudo apt install -y ufw

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 2. Install Fail2ban

```bash
# Install Fail2ban
sudo apt install -y fail2ban

# Configure
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 3. Disable Root Login

```bash
sudo nano /etc/ssh/sshd_config
```

Set:

```
PermitRootLogin no
PasswordAuthentication no  # If using SSH keys
```

Restart SSH:

```bash
sudo systemctl restart sshd
```

---

**Deployment Complete! 🎉**

Your ISP Billing System is now live and ready to use!
