# 🚀 Setup Instructions - Step by Step

## ✅ Status Progress

**Backend Laravel**: Installing packages...
**Frontend Vue**: Ready to configure
**Database**: Will use SQLite for quick demo

---

## 📋 Next Steps

### 1. Backend Setup (Laravel)

Backend sudah dibuat di: `C:\Users\inter\Documents\Billing\isp-billing-backend`

Packages yang sedang diinstall:

- ✅ Laravel 11
- ⏳ Laravel Sanctum (API Authentication)
- ⏳ Midtrans PHP (Payment Gateway)
- ⏳ Xendit PHP (Alternative Payment)

### 2. Configure Database (SQLite - Simple)

Untuk demo cepat, kita akan gunakan SQLite (tidak perlu install MySQL):

```powershell
cd C:\Users\inter\Documents\Billing\isp-billing-backend

# Edit .env file
# Ubah DB_CONNECTION=mysql menjadi DB_CONNECTION=sqlite
# Hapus/comment baris DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Create database file
New-Item database/database.sqlite
```

### 3. Import Schema

```powershell
# Generate application key
php artisan key:generate

# Run migrations (we'll create from our schema)
php artisan migrate
```

### 4. Start Backend Server

```powershell
php artisan serve
# Server will run at: http://127.0.0.1:8000
```

### 5. Frontend Setup

```powershell
cd C:\Users\inter\Documents\Billing\vue-argon-dashboard

# Install dependencies (if not yet)
npm install

# Start development server
npm run serve
# Server will run at: http://localhost:8080
```

---

## 🎯 Quick Test

Once both servers running:

1. Backend API: http://127.0.0.1:8000
2. Frontend UI: http://localhost:8080

You should see the Argon Dashboard interface!

---

## 📝 What We'll Create First

**Phase 1 - Basic Demo** (Today):

1. ✅ Backend structure
2. ⏳ Basic authentication
3. ⏳ Simple login page
4. ⏳ Dashboard view

**Phase 2** (Next):

- Complete all models
- All controllers
- All frontend pages

---

## 🔄 Current Status

Waiting for Composer packages to finish installing...
Then we'll:

1. Configure .env
2. Create basic migrations
3. Seed super admin
4. Start servers
5. See the UI!

Sedang proses... Mohon tunggu sebentar! 🚀
