# SETUP ISP Billing (Production Guide)

Panduan ini berisi langkah-langkah untuk melakukan deployment aplikasi ISP Billing ke server production menggunakan virtualisasi **Proxmox**.

## 1. Persiapan Server (Proxmox)

### MariaDB Database (LXC)
- Gunakan **LXC Container** Ubuntu 22.04/24.04.
- Instal MariaDB: `sudo apt install mariadb-server`.
- Buat database `isp_billing` dan user dengan akses remote dari IP VM Aplikasi.
- Untuk detailnya, lihat: [database_setup_guide.md](file:///C:/Users/inter/.gemini/antigravity/brain/d3c8bcae-d993-44b6-9baf-b25618a8bf03/database_setup_guide.md)

### VM Aplikasi (PHP & Node.js)
- Gunakan VM Ubuntu 22.04/24.04.
- Instal PHP 8.2+, Composer, Node.js v18+, dan Nginx.

---

## 2. Instalasi Backend (Laravel)

1. Masuk ke folder `isp-billing-backend`.
2. Instal dependensi: `composer install --no-dev --optimize-autoloader`.
3. Salin `.env.example` ke `.env` dan konfigurasikan:
   - `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (Arahkan ke IP LXC MariaDB).
   - `APP_URL` (URL utama Anda).
4. Jalankan migrasi: `php artisan migrate --force`.
5. Generate kunci aplikasi: `php artisan key:generate`.
6. Tautan storage: `php artisan storage:link`.

---

## 3. Instalasi Frontend (Vue.js)

1. Masuk ke folder `vue-argon-dashboard` (root project).
2. Instal dependensi: `npm install`.
3. Build untuk production: `npm run build`.
4. Hasil build akan ada di folder `dist`.

---

## 4. Konfigurasi Domain & Nginx

### DNS (Wildcard)
- Tambahkan **A Record** `*` (Wildcard) di DNS provider Anda (misalnya Cloudflare/cPanel) yang mengarah ke IP VM Aplikasi.

### Web Server (Nginx)
Buat file konfigurasi di `/etc/nginx/sites-available/isp-billing`:
```nginx
server {
    listen 80;
    server_name ispanda.com *.ispanda.com;
    root /path/to/vue-argon-dashboard/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /api {
        proxy_pass http://localhost:8000; # Sesuaikan dengan port backend
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

---

## 5. Pengaturan Utama di UI (Langkah Terakhir)

Setelah aplikasi bisa dibuka:
1. Login sebagai **Super Admin**.
2. Masuk ke menu **System Settings**.
3. Atur **Main Domain** dengan domain utama production Anda (misalnya: `ispanda.com`).
4. **SIMPAN**. Semua fitur subdomain akan secara otomatis menggunakan domain tersebut.

---

## Tips Tambahan
- Gunakan **Let's Encrypt (Certbot)** untuk SSL Wildcard.
- Aktifkan fitur **Snapshot** secara rutin di Proxmox sebelum melakukan pembaruan aplikasi besar.
- Pastikan firewall (ufw/iptables) mengizinkan port 80, 443, dan port API.
