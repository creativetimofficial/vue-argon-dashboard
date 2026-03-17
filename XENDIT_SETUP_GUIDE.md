# Panduan Pengaturan (Setup) Integrasi Xendit untuk Pencairan Dana & Verifikasi Rekening

Dokumen ini berisi langkah-langkah yang harus dilakukan oleh Administrator (Pemilik Sistem) untuk memastikan fitur **Pencairan Dana Otomatis (Disbursement)** dan **Verifikasi Nama Rekening (Name Validator)** dapat berjalan dengan normal di lingkungan *Production*.

---

## 1. Persiapan API Keys Xendit

Agar sistem kita dapat berkomunikasi dengan Xendit, Anda wajib memasukkan **Secret API Key** dari Xendit ke dalam Pengaturan Gateway Pembayaran di Dashboard Super Admin.

**Langkah-langkah:**
1. Login ke [Dashboard Xendit](https://dashboard.xendit.co/).
2. Pastikan Anda berada pada mode **Live** (bukan Test) jika ingin digunakan sungguhan.
3. Buka menu **Settings** > **API Keys**.
4. Klik **Generate Secret Key**.
5. Beri nama key tersebut (misal: "Billing App").
6. **Sangat Penting: Izin (Permissions)**
   Pada opsi perizinan (Permissions), pastikan Anda memberikan hak akses `WRITE` (Tulis) atau minimal `READ` (Baca) pada produk-produk berikut:
   - **Disbursements** (Pencairan Dana) -> Wajib `WRITE` untuk bisa mentransfer dana.
   - **Money In / Accept Payments** (Penerimaan Uang) -> Opsional, ini untuk sisi Invoice/Tagihan.
7. Simpan *Secret Key* yang muncul.
8. Masuk ke aplikasi Billing App Anda sebagai **Super Admin**.
9. Buka menu **Master Data** > **Payment Gateways** > Pilih **Xendit**.
10. Ubah ke mode **Live**, isikan *Secret Key* tadi pada kolom yang tersedia, dan klik Simpan.

---

## 2. Mengaktifkan Fitur Validasi Nama Rekening (Name Validator / Iluma)

Pada saat ini (secara default), sistem akan memberi peringatan **404 Not Found** atau peringatan *Fallback* (memerlukan ketik manual) saat klien mengecek rekening Bank Konvensional seperti BRI, BCA, BNI, dsb.

Hal ini terjadi **bukan karena error sistem (coding)**, melainkan karena akun Xendit Anda belum diizinkan menggunakan fitur bernama **Name Validator (Jasa Data Iluma)**.

**Langkah-langkah Mengaktifkan:**
1. Fitur *Name Validator* dari Xendit tidak langsung aktif saat Anda membuat akun Xendit.
2. Anda harus menghubungi tim *Customer Service (CS)* atau Account Manager Xendit Anda (bisa lewat Live Chat di Dashboard Xendit atau email ke `help@xendit.co`).
3. Sampaikan pesan ini kepada mereka:
   > *"Halo tim Xendit, saya ingin menggunakan API Endpoint `bank_account_data_requests` untuk memvalidasi nama pemilik rekening sebelum melakukan Disbursement. Mohon bantuannya untuk mengaktifkan fitur **Name Validator / Iluma Data Services** pada akun saya (email Anda)."*
4. Setelah CS Xendit mengkonfirmasi fitur tersebut telah diaktifkan di akun Anda, sistem Billing App ini **secara otomatis** akan langsung bisa mengecek nama pemilik rekening (tidak perlu ubah codingan lagi!).

---

## 3. Mengatur Webhook Xendit (Untuk Pembaruan Status Pencairan)

Saat sistem kita memerintahkan Xendit untuk mencairkan uang, butuh waktu beberapa menit bagi bank untuk memprosesnya. Xendit akan memberitahu sistem kita lewat jalur "Webhook" jika uang sudah berhasil mendarat di rekening klien (atau gagal).

**Langkah-langkah:**
1. Login ke [Dashboard Xendit](https://dashboard.xendit.co/).
2. Buka menu **Settings** > **Webhooks**.
3. Cari bagian **Disbursements** atau **Pencairan Dana Otomatis**.
4. Masukkan URL Webhook dari aplikasi kita.
   **Format URL Webhook:**
   `https://[DOMAIN-APLIKASI-ANDA]/api/xendit/webhook`
   *(Contoh: `https://billing-ispku.com/api/xendit/webhook`)*
5. Jangan lupa klik `Save` / `Test and Save`.
6. Simpan **Webhook Verification Token** yang diberikan oleh Xendit pada halaman tersebut, lalu masukkan token tersebut ke menu Pengaturan Gateway Xendit di aplikasi Billing Anda (Biasanya disebut `Webhook Token` atau `Callback Token`).

---

## Daftar Fitur yang Sudah Terautomasi dalam Sistem Ini

Jika ketiga langkah di atas sudah dilakukan, Anda hanya tinggal terima beres:

✅ **Nomor E-Wallet (DANA, OVO, GoPay) ke format standard**: Sistem backend akan otomatis mengubah nomor telepon pengguna yang salah (misal: +62812... atau 62812...) menjadi format bersih (`0812...`) yang diterima oleh sistem *Disbursement* Xendit.

✅ **Perlindungan Uang Gagal Transfer**: Jika *Name Validator* sedang down atau belum Anda aktifkan secara lisensi, sistem cerdas ini tidak akan _error_ tapi langsung membebaskan klien untuk mengetik manual namanya dengan aman.

✅ **UI Auto-Detect Bank/Wallet**: Tampilan depan sudah mendeteksi jika yang dipilih adalah Bank Biasa (contoh format tipe: `123456`) atau E-Wallet (contoh format tipe: `0812xxxx`).

---
*Panduan dibuat pada: Maret 2026*
