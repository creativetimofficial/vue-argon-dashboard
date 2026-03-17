# Panduan Konfigurasi Xendit Production (Live Mode)

Ikuti langkah-langkah ini untuk memastikan sistem penarikan dana Anda berjalan lancar saat sudah menggunakan domain asli dan uang asli.

## 1. Persiapan Akun Xendit
1. Pastikan akun Xendit Anda sudah **Terverifikasi** (KYC selesai). Payout/Disbursement hanya bisa jalan jika akun sudah aktif.
2. Di Dashboard Xendit, pindahkan saklar di header dari **TEST** ke **LIVE**.
3. Di menu **Developer > API Keys**:
   - Buat **Secret Key** baru untuk mode Live.
   - Pilih perizinan: `Disbursements` -> **WRITE**.
   - Simpan key ini di Master Data Payment Gateways di aplikasi Anda.

## 2. Pengaturan Webhook (Otomatisasi Status)
1. Masuk ke Dashboard Xendit Live > **Developer > Webhooks**.
2. Cari bagian **Disbursements (Sent)**.
3. Masukkan URL domain Anda: `https://domain-anda.com/api/callback/xendit/disbursement`.
4. Klik **Test and Save**. (Pastikan domain Anda sudah menggunakan **HTTPS**).

## 3. Keamanan Tambahan (Wajib untuk Production)
1. Di Dashboard Xendit Live > **Settings > Developer > API Keys**, cari bagian **Callback Verification Token**.
2. Salin token tersebut.
3. Buka file `.env` di server backend Anda, lalu tambahkan:
   ```env
   XENDIT_X_CALLBACK_TOKEN=isi_token_dari_xendit_tadi
   ```
   *Sistem akan menolak semua update status transaksi jika token ini tidak cocok, sehingga aman dari peretasan.*

## 4. Aktivasi Fitur Payout
Secara default, penarikan dana ke rekening Bank di Xendit Live mungkin perlu diaktifkan manual oleh Tim CS Xendit atau via Dashboard.
1. Masuk ke **Menu > Payouts**.
2. Jika ada tombol **"Activate Payouts"**, silakan klik dan ikuti prosedurnya.
3. Pastikan Anda sudah mengisi **Saldo (Balance)** di Xendit Live Anda sebelum melakukan penarikan pertama.

## 5. Batas Transaksi (Security Limits)
Sistem sekarang sudah saya atur dengan batasan berikut untuk keamanan:
- **Maksimal 2 kali penarikan per hari** per ISP.
- **Maksimal total Rp 50.000.000 per hari** per ISP.
- *Anda dapat mengubah angka ini di file `ClientAreaController.php` jika diperlukan di masa depan.*

---
> [!NOTE]
> Pastikan variabel `APP_ENV` di file `.env` server Anda sudah diset ke `production` agar verifikasi SSL Xendit aktif sepenuhnya.
