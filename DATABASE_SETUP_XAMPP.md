# 🗄️ Database Setup dengan XAMPP - Step by Step

## ✅ Status Saat Ini

- ✅ XAMPP Control Panel sudah dibuka
- ⏳ MySQL belum running
- ⏳ Database belum dibuat

---

## 📋 Langkah Setup Database

### Step 1: Start MySQL di XAMPP Control Panel

1. **Buka XAMPP Control Panel** (sudah dibuka otomatis)
2. Cari baris **MySQL**
3. Klik tombol **"Start"** di sebelah MySQL
4. Tunggu sampai status berubah jadi hijau dan port **3306** muncul
5. Pastikan tidak ada error

⚠️ **Troubleshooting jika MySQL tidak start:**

- Port 3306 sudah dipakai aplikasi lain
- Coba close Skype atau aplikasi lain yang pakai port 3306
- Atau ubah port MySQL di XAMPP config

---

### Step 2: Buat Database via phpMyAdmin (Cara Mudah)

**Opsi A: Via phpMyAdmin (Recommended)**

1. Klik tombol **"Admin"** di sebelah MySQL di XAMPP Control Panel
2. Browser akan terbuka ke phpMyAdmin (http://localhost/phpmyadmin)
3. Klik tab **"SQL"** di bagian atas
4. Copy-paste perintah ini:

```sql
CREATE DATABASE IF NOT EXISTS isp_billing
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

5. Klik **"Go"** atau **"Jalankan"**
6. Database `isp_billing` akan muncul di sidebar kiri

**Opsi B: Via Command Line**

```powershell
# Buka PowerShell baru, lalu jalankan:
cd C:\xampp\mysql\bin
.\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS isp_billing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

### Step 3: Import Database Schema

**Setelah database dibuat, jalankan ini di PowerShell:**

```powershell
# Navigate ke folder database schema
cd C:\Users\inter\Documents\Billing\vue-argon-dashboard

# Import schema ke database
C:\xampp\mysql\bin\mysql.exe -u root isp_billing < database_schema.sql
```

**Atau via phpMyAdmin:**

1. Klik database **isp_billing** di sidebar kiri
2. Klik tab **"Import"**
3. Click **"Choose File"**
4. Pilih file: `C:\Users\inter\Documents\Billing\vue-argon-dashboard\database_schema.sql`
5. Scroll ke bawah, klik **"Go"**
6. Tunggu import selesai

---

### Step 4: Configure Laravel .env

**Update file `.env` di backend Laravel:**

```powershell
# Open .env file
cd C:\Users\inter\Documents\Billing\isp-billing-backend
notepad .env
```

**Update baris database configuration:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=isp_billing
DB_USERNAME=root
DB_PASSWORD=
```

**Important Notes:**

- `DB_PASSWORD=` (kosong, karena XAMPP default tidak ada password)
- `DB_PORT=3306` (default MySQL port)
- Jika Anda sudah set password di XAMPP, masukkan passwordnya

**Untuk development, ubah juga:**

```env
APP_ENV=local
APP_DEBUG=true
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

(Kita nonaktifkan Redis dulu untuk simplicity)

---

### Step 5: Test Database Connection

**Test koneksi dari Laravel:**

```powershell
cd C:\Users\inter\Documents\Billing\isp-billing-backend

# Clear config cache
php artisan config:clear

# Test database connection
php artisan migrate:status
```

**Jika berhasil**, Anda akan melihat daftar tabel migration.

**Jika error:**

- Check MySQL running di XAMPP
- Check credentials di .env
- Check database `isp_billing` sudah dibuat

---

### Step 6: Verify Database Tables

**Check via phpMyAdmin:**

1. Buka http://localhost/phpmyadmin
2. Klik database **isp_billing**
3. Anda harus lihat **27 tables**:
   - users
   - super_admins
   - isps
   - isp_admins
   - technicians
   - customers
   - super_admin_packages
   - customer_packages
   - isp_subscriptions
   - customer_subscriptions
   - invoices
   - invoice_items
   - payments
   - payment_gateways
   - installation_requests
   - repair_tickets
   - complaints
   - mikrotik_routers
   - web_customizations
   - notifications
   - activity_logs
   - isp_settings
   - system_settings
   - password_reset_tokens
   - failed_jobs
   - sessions
   - personal_access_tokens

**Check via Command Line:**

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "USE isp_billing; SHOW TABLES;"
```

---

### Step 7: Seed Super Admin (Optional)

Database schema sudah include Super Admin default. Untuk verify:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "USE isp_billing; SELECT * FROM users WHERE email = 'superadmin@ispbilling.com';"
```

**Default Super Admin Credentials:**

- Email: `superadmin@ispbilling.com`
- Password: `password` (sudah di-hash di database)

⚠️ **Ubah password ini setelah login pertama kali!**

---

## 🔧 Quick Commands Reference

### Start/Stop MySQL

```powershell
# Via XAMPP Control Panel
# Click "Start" or "Stop" button next to MySQL

# Via Command (alternative)
net start mysql
net stop mysql
```

### Access MySQL Command Line

```powershell
C:\xampp\mysql\bin\mysql.exe -u root
# Atau jika ada password:
C:\xampp\mysql\bin\mysql.exe -u root -p
```

### Common MySQL Commands

```sql
-- Show all databases
SHOW DATABASES;

-- Use database
USE isp_billing;

-- Show all tables
SHOW TABLES;

-- Show table structure
DESCRIBE users;

-- Count records
SELECT COUNT(*) FROM users;

-- Exit
EXIT;
```

---

## 🎯 After Database Setup

Setelah database ready, Anda bisa:

1. **Test Backend API** dengan database real
2. **Create Laravel migrations** untuk manage schema
3. **Run seeders** untuk dummy data
4. **Test authentication** dengan super admin
5. **Connect frontend** ke backend API

---

## 📊 Expected Database Structure

```
isp_billing (Database)
├── users (1 super admin)
├── super_admins
├── isps
├── isp_admins
├── technicians
├── customers
├── packages (2 types)
│   ├── super_admin_packages
│   └── customer_packages
├── subscriptions (2 types)
│   ├── isp_subscriptions
│   └── customer_subscriptions
├── billing
│   ├── invoices
│   ├── invoice_items
│   └── payments
├── payment_gateways
├── operations
│   ├── installation_requests
│   ├── repair_tickets
│   └── complaints
├── integrations
│   ├── mikrotik_routers
│   └── web_customizations
├── system
│   ├── notifications
│   ├── activity_logs
│   ├── isp_settings
│   └── system_settings
└── Laravel tables
    ├── password_reset_tokens
    ├── failed_jobs
    ├── sessions
    └── personal_access_tokens
```

---

## ⚠️ Troubleshooting

### Error: "Can't connect to MySQL server"

**Solution:**

1. Check MySQL running di XAMPP Control Panel
2. Pastikan port 3306 tidak digunakan aplikasi lain
3. Restart MySQL service

### Error: "Access denied for user 'root'"

**Solution:**

1. Check DB_USERNAME dan DB_PASSWORD di .env
2. XAMPP default: username=root, password=(kosong)
3. Jika sudah set password, masukkan password yang benar

### Error: "Unknown database 'isp_billing'"

**Solution:**

1. Database belum dibuat
2. Buat manual via phpMyAdmin atau command line
3. Re-import database_schema.sql

### Error: "Table doesn't exist"

**Solution:**

1. Schema belum di-import
2. Import database_schema.sql
3. Check import success di phpMyAdmin

---

## ✅ Verification Checklist

Sebelum lanjut, pastikan:

- [ ] MySQL running di XAMPP (status hijau)
- [ ] Database `isp_billing` sudah dibuat
- [ ] Schema sudah di-import (27 tables)
- [ ] `.env` Laravel sudah dikonfigurasi
- [ ] Connection test berhasil (`php artisan migrate:status`)
- [ ] Super admin ada di database (1 record di table users)

---

## 🚀 Next Steps

Setelah database setup selesai:

1. **Copy backend files** (models, controllers, routes)
2. **Test API endpoints** dengan Postman
3. **Create login page** di frontend
4. **Connect frontend to backend**
5. **Test full authentication flow**

---

**Status**: Waiting for MySQL to start in XAMPP Control Panel

**Action Required**:

1. Start MySQL di XAMPP Control Panel
2. Lanjut ke Step 2 untuk create database
3. Beritahu saya jika sudah selesai, atau jika ada error!

🔧 **Ready to continue when MySQL is running!**
