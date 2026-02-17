# Panduan Setup Otomatisasi (Scheduler)

Agar sistem dapat berjalan otomatis **tanpa perintah manual**, Anda perlu melakukan **satu kali setup** pada server Anda. Laravel menggunakan fitur bernama "Task Scheduling" yang hanya membutuhkan satu "pemicu" dari sistem operasi.

## Kenapa Ini Terbaik?
1.  **Mandiri**: Tidak tergantung layanan pihak ketiga (seperti cron-job.org). Jika internet putus, sistem tetap jalan di lokal.
2.  **Aman**: Tidak membuka URL/API publik yang bisa disalahgunakan orang lain untuk memicu sistem.
3.  **Terpusat**: Semua jadwal (hourly, daily, monthly) diatur di kode (`routes/console.php`), tidak perlu setting banyak cron di panel hosting.

---

## 1. Setup di Production (Linux / cPanel / VPS)
Ini adalah lingkungan umum untuk server website.

### Jika Menggunakan cPanel:
1.  Masuk ke **cPanel**.
2.  Cari menu **Cron Jobs**.
3.  Tambahkan **New Cron Job**:
    -   **Common Settings**: Pilih `Once Per Minute` (* * * * *).
    -   **Command**:
        ```bash
        cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
        ```
        *(Ganti `/path/to/your/project` dengan lokasi folder project Anda, misal `/home/user/public_html/isp-billing-backend`)*
    -   **Info**: Perintah ini akan menjalankan scheduler Laravel setiap menit. Scheduler kemudian akan memeriksa `routes/console.php` dan menjalankan perintah `isp:check-expiry` hanya jika sudah waktunya (setiap jam).

### Jika Menggunakan VPS (Ubuntu/CentOS):
1.  SSH ke server.
2.  Ketik `crontab -e`.
3.  Tambahkan baris ini di paling bawah:
    ```bash
    * * * * * cd /var/www/isp-billing-backend && php artisan schedule:run >> /dev/null 2>&1
    ```

---

## 2. Setup di Development (Windows - Localhost)
Karena Windows tidak punya "Cron", kita gunakan **Task Scheduler** atau script sederhana.

### Cara Mudah (Batch Script):
Buat file baru bernama `scheduler.bat` di folder project backend Anda, isi dengan:

```batch
:loop
php artisan schedule:run
timeout /t 60
goto loop
```

**Cara pakai:** 
Cukup klik ganda file `scheduler.bat` ini dan biarkan jendelanya terbuka di background. Dia akan menjalankan perintah setiap 60 detik.

### Cara Permanen (Windows Task Scheduler):
1.  Buka aplikasi **Task Scheduler**.
2.  Klik **Create Basic Task**.
3.  Nama: `Laravel Scheduler`, Next.
4.  Trigger: **Daily**, Next.
5.  Action: **Start a program**, Next.
6.  Program/script: `path\to\php.exe` (misal: `C:\xampp\php\php.exe`).
7.  Add arguments: `artisan schedule:run`.
8.  Start in: `C:\Users\inter\Documents\Billing\vue-argon-dashboard\isp-billing-backend`.
9.  Finish.
10. Klik kanan task tadi -> Properties -> Triggers -> Edit -> Centang **Repeat task every 1 minute** -> Duration: **Indefinitely**.

---

## Kesimpulan
Dengan setup satu kali ini, semua perintah yang kita buat (cek expired, kirim email) akan jalan otomatis selamanya selama komputer/server hidup. Ini adalah cara standar industri Laravel ("best practice").
