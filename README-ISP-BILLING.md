# 🌐 ISP Billing System

![ISP Billing System](https://img.shields.io/badge/version-1.0.0-blue.svg)
![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3.4-green.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Sistem billing ISP lengkap dengan manajemen pelanggan, integrasi Mikrotik, payment gateway, dan sistem ticketing. Didesain khusus untuk Internet Service Provider (ISP) di Indonesia.

## ✨ Fitur Utama

### 🔐 Multi-Role System

- **Super Admin**: Manajemen ISP, paket berlangganan, payment gateway, customization
- **ISP Admin**: Manajemen pelanggan, paket internet, Mikrotik, teknisi, pembayaran
- **Technician**: Permintaan pemasangan, tiket perbaikan, update status
- **Customer**: Tagihan, pembayaran, riwayat invoice, pengaduan

### 💼 Untuk Super Admin

- ✅ Registrasi & verifikasi ISP
- ✅ Manajemen paket berlangganan untuk ISP
- ✅ Konfigurasi payment gateway (Midtrans, Xendit)
- ✅ Customization website marketing
- ✅ Monitoring & analytics semua ISP
- ✅ Laporan revenue dan statistik

### 🏢 Untuk ISP Admin

- ✅ CRUD pelanggan lengkap
- ✅ Manajemen paket internet
- ✅ Integrasi Mikrotik (PPPoE/Hotspot)
- ✅ Manajemen teknisi & penjadwalan
- ✅ Generate invoice otomatis
- ✅ Approval pembayaran cash/transfer
- ✅ Sistem ticketing (pemasangan & perbaikan)
- ✅ Laporan & analytics

### 🔧 Untuk Teknisi

- ✅ Daftar permintaan pemasangan
- ✅ Daftar tiket perbaikan
- ✅ Update status pekerjaan
- ✅ Upload foto hasil pekerjaan
- ✅ Tracking lokasi (GPS)

### 👥 Untuk Customer

- ✅ Dashboard informasi layanan
- ✅ Lihat tagihan & invoice
- ✅ Pembayaran multi-metode:
  - Payment Gateway (Midtrans/Xendit)
  - Transfer Manual
  - Cash/Tunai
- ✅ Riwayat pembayaran
- ✅ Submit pengaduan
- ✅ Informasi paket & status koneksi

## 🛠️ Teknologi Stack

### Backend

- **Framework**: Laravel 11
- **Database**: MySQL 8.0 / PostgreSQL 14
- **Cache**: Redis
- **Authentication**: Laravel Sanctum
- **Queue**: Laravel Queue + Supervisor
- **Email**: SMTP/Mailgun/SendGrid
- **Payment**: Midtrans, Xendit
- **Mikrotik**: RouterOS API

### Frontend

- **Framework**: Vue.js 3
- **State Management**: Pinia
- **Routing**: Vue Router 4
- **UI Theme**: Argon Dashboard 2
- **HTTP Client**: Axios
- **Charts**: Chart.js
- **Icons**: Bootstrap Icons

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0 or PostgreSQL >= 14
- Redis
- Nginx/Apache
- SSL Certificate

## 🚀 Quick Start

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/isp-billing.git
cd isp-billing
```

### 2. Setup Backend

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# Then run migrations
php artisan migrate --seed

# Link storage
php artisan storage:link

# Start development server
php artisan serve
```

### 3. Setup Frontend

```bash
cd frontend

# Install dependencies
npm install

# Copy environment file
cp .env.example .env.local

# Configure API URL in .env.local
# VUE_APP_API_URL=http://localhost:8000/api

# Start development server
npm run serve
```

### 4. Access Application

- **Frontend**: http://localhost:8080
- **Backend API**: http://localhost:8000/api
- **Super Admin Login**: superadmin@ispbilling.com / password

## 📚 Documentation

- [Project Structure](PROJECT_STRUCTURE.md) - Arsitektur dan struktur proyek
- [Backend Setup](BACKEND_SETUP.md) - Setup dan konfigurasi Laravel backend
- [Database Schema](database_schema.sql) - Database schema lengkap
- [API Routes](backend_routes_api.php) - Dokumentasi API endpoints
- [Deployment Guide](DEPLOYMENT_GUIDE.md) - Panduan deployment lengkap

## 🗄️ Database Schema

Database menggunakan struktur polymorphic untuk multi-role system dengan tabel utama:

- **Users & Auth**: users, super_admins, isps, isp_admins, technicians, customers
- **Packages**: super_admin_packages, customer_packages, subscriptions
- **Billing**: invoices, invoice_items, payments, payment_gateways
- **Operations**: installation_requests, repair_tickets, complaints
- **Mikrotik**: mikrotik_routers
- **System**: notifications, activity_logs, settings

## 🔌 API Endpoints

### Authentication

```
POST /api/auth/login
POST /api/auth/super-admin/login
POST /api/auth/isp-admin/login
POST /api/auth/technician/login
POST /api/auth/customer/login
POST /api/auth/register/isp
POST /api/auth/logout
```

### Super Admin

```
GET  /api/super-admin/dashboard
GET  /api/super-admin/isps
POST /api/super-admin/isps
PUT  /api/super-admin/isps/{id}
GET  /api/super-admin/packages
POST /api/super-admin/packages
```

### ISP Admin

```
GET  /api/isp-admin/dashboard
GET  /api/isp-admin/customers
POST /api/isp-admin/customers
PUT  /api/isp-admin/customers/{id}
GET  /api/isp-admin/packages
POST /api/isp-admin/mikrotik/{id}/sync-users
```

### Customer

```
GET  /api/customer/dashboard
GET  /api/customer/invoices
POST /api/customer/payments/gateway
POST /api/customer/payments/transfer
POST /api/customer/complaints
```

[Lihat dokumentasi lengkap API](backend_routes_api.php)

## 💳 Payment Gateway Integration

### Midtrans

1. Daftar di [Midtrans](https://midtrans.com)
2. Dapatkan Server Key dan Client Key
3. Tambahkan ke `.env`:

```env
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

### Xendit

1. Daftar di [Xendit](https://xendit.co)
2. Dapatkan API Keys
3. Tambahkan ke `.env`:

```env
XENDIT_SECRET_KEY=your_secret_key
XENDIT_PUBLIC_KEY=your_public_key
```

## 🔧 Mikrotik Integration

Sistem ini menggunakan RouterOS API untuk integrasi dengan Mikrotik:

- Auto-create PPPoE users
- Bandwidth management
- Real-time monitoring
- User activation/suspension
- Multi-router support

### Setup Mikrotik

1. Enable API di Mikrotik
2. Buat user dengan privilege API
3. Tambahkan router di ISP Admin panel
4. Test koneksi

## 📱 Mobile App (Future Development)

API sudah dirancang RESTful dan mobile-friendly untuk pengembangan aplikasi mobile:

- iOS (Swift/React Native)
- Android (Kotlin/React Native)
- Flutter (Cross-platform)

## 🌐 Hosting Recommendations

### 🥇 Recommended (Indonesia)

**Niagahoster VPS Bisnis**

- 2 CPU, 4GB RAM
- Rp 150,000/bulan
- Support lokal
- [https://www.niagahoster.co.id](https://www.niagahoster.co.id)

### 🥈 International

**DigitalOcean**

- 2 CPU, 4GB RAM
- $12/bulan
- Singapore datacenter
- [https://www.digitalocean.com](https://www.digitalocean.com)

**Vultr**

- 2 CPU, 4GB RAM
- $12/bulan
- High performance
- [https://www.vultr.com](https://www.vultr.com)

[Lihat panduan deployment lengkap](DEPLOYMENT_GUIDE.md)

## 🔒 Security Features

- ✅ Role-based access control (RBAC)
- ✅ Email verification
- ✅ Password encryption (bcrypt)
- ✅ API token authentication (Sanctum)
- ✅ CSRF protection
- ✅ XSS protection
- ✅ SQL injection prevention
- ✅ Rate limiting
- ✅ Activity logging
- ✅ 2FA support (optional)

## 📊 Features by Role

| Feature                | Super Admin | ISP Admin | Technician | Customer |
| ---------------------- | ----------- | --------- | ---------- | -------- |
| Dashboard              | ✅          | ✅        | ✅         | ✅       |
| ISP Management         | ✅          | ❌        | ❌         | ❌       |
| Package Management     | ✅          | ✅        | ❌         | ❌       |
| Customer Management    | ❌          | ✅        | ❌         | ❌       |
| Mikrotik Integration   | ❌          | ✅        | ❌         | ❌       |
| Payment Gateway Config | ✅          | ✅\*      | ❌         | ❌       |
| Installation Requests  | ❌          | ✅        | ✅         | ❌       |
| Repair Tickets         | ❌          | ✅        | ✅         | ❌       |
| Billing & Invoices     | ✅          | ✅        | ❌         | ✅       |
| Payments               | ✅          | ✅        | ❌         | ✅       |
| Complaints             | ❌          | ✅        | ❌         | ✅       |
| Reports & Analytics    | ✅          | ✅        | ❌         | ❌       |

\*ISP specific gateway

## 🧪 Testing

```bash
# Backend tests
cd backend
php artisan test

# Frontend tests
cd frontend
npm run test
```

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Development Team

- **Backend Developer**: [Your Name]
- **Frontend Developer**: [Your Name]
- **UI/UX Designer**: [Your Name]

## 📧 Support

For support, email support@yourdomain.com or join our Slack channel.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Vue.js](https://vuejs.org) - The Progressive JavaScript Framework
- [Creative Tim](https://www.creative-tim.com) - Argon Dashboard Theme
- [Midtrans](https://midtrans.com) - Payment Gateway
- [Xendit](https://xendit.co) - Payment Gateway

## 📅 Roadmap

- [x] Multi-role authentication system
- [x] ISP management
- [x] Customer management
- [x] Mikrotik integration
- [x] Payment gateway integration
- [x] Invoice & billing system
- [x] Ticketing system
- [ ] Mobile app (iOS/Android)
- [ ] WhatsApp notification
- [ ] Auto payment reminder
- [ ] Advanced analytics
- [ ] Multi-language support
- [ ] Dark mode
- [ ] PWA support

## 🐛 Known Issues

None at the moment. Please report any bugs in the [Issues](https://github.com/yourusername/isp-billing/issues) section.

---

**Made with ❤️ for ISP Community**

⭐ Star this repository if you find it helpful!
