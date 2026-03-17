# Panduan Testing (Simulasi) Pencairan Dana Menggunakan Xendit Sandbox

Anda dapat melakukan simulasi pencairan dana secara penuh *(End-to-End Testing)* **tanpa menggunakan uang asli dan saldo asli Anda di Xendit tidak akan berkurang**. 

Xendit menyediakan mode pengujian bawaan yang disebut **Test Mode (Sandbox)**. 

---

## 1. Pastikan Menggunakan API Key Mode UJI (TEST)

Untuk mencegah sistem mentransfer uang asli, Anda harus memutus API Key "Live" dan menggantinya dengan API Key "Test".

**Langkah-langkah:**
1. Login ke [Dashboard Xendit](https://dashboard.xendit.co/).
2. Di bagian paling atas *header* dashboard, perhatikan ada _toggle switch_ (saklar) bertuliskan **"Test"/"Live"**.
3. Pastikan Anda mengaktifkan **"Test"** (mode uji coba).
4. Buka menu **Settings** > **API Keys**.
5. Klik **Generate Secret Key**.
6. Beri nama key (Misal: "Aplikasi Billing - TEST").
7. Beri perizinan (Permissions):
   - **Disbursements** -> Wajib `WRITE`.
8. *Copy* Secret Key tersebut.
9. Kembali ke Aplikasi Billing Anda (sebagai Super Admin).
10. Masuk ke **Master Data** -> **Payment Gateways** -> Edit **Xendit**.
11. Ubah **Environment** menjadi **"Test"** (Jika ada opsi ini, atau cukup masukkan Secret Key TEST Anda ke kolom API Key).

---

## 2. Saldo Virtual Anda

Di mode "Test", Anda **tidak perlu punya saldo asli**. Xendit secara otomatis akan pura-pura mencairkan dana berapapun besarnya tanpa memotong rekening Anda sama sekali.

*(Catatan: Saldo Billing ISP di dalam aplikasi Anda tetap akan "terpotong" secara database internal aplikasi Anda, ini wajar karena kita menguji alur aplikasi kita sendiri. Anda bebas menambah saldo ISP Anda langsung via Database jika habis).*

---

## 3. Nomor Rekening Khusus Simulasi

Saat melakukan penarikan via halaman Klien, Anda **tidak boleh** memasukkan nomor rekening asli, jika Anda ingin menyimulasikan "Keberhasilan" atau "Kegagalan". Xendit sudah menyiapkan kode-kode "Ajaib" *(Magic Values)* yang jika dimasukkan, Xendit akan memanipulasi hasilnya.

Gunakan data ini untuk melakukan simulasi proses *Withdraw*:

### Simulasi Berhasil
Jika Anda ingin mengetes apakah uang "masuk":
- **Bank**: BCA, BNI, BRI, dll
- **Nama**: Bebas (bisa ketik: "Test User")
- **Nomor Rekening ajaib**: `1234567890`

*(Jika Anda menggunakan nomor rekening `1234567890` di mode TEST, Xendit akan selalu bilang ke aplikasi kita: "Sukses! Dana terkirim!").*

### Simulasi Gagal
Jika Anda ingin mengetes bagaimana jika rekening tujuan salah, nyangkut, atau ditolak:
- **Bank**: BCA, BNI, BRI, dll
- **Nama**: Bebas 
- **Nomor Rekening ajaib**: Gunakan `9999999999` (Xendit akan menolaknya) atau `0000000000` (Xendit akan merespons dengan status "Gagal").

---

## 4. Cara Mengetes Webhook (Notifikasi Keberhasilan Otomatis)

Agar status `processing` di tabel Withdrawal berubah otomatis menjadi `completed`, Webhook Anda juga harus bekerja.
1. Masih dalam Dashboard Xendit mode **"Test"**.
2. Masuk ke **Settings** -> **Webhooks**.
3. Pastikan URL endpoint aplikasi Anda untuk *Disbursements* sudah dimasukkan. 
4. Di Dashbaord Test Xendit juga tersedia tombol *"Test and Save"*. Tombol ini akan mengirim simulasi notifikasi *Pencairan Sukses* ke aplikasi Billing Anda agar Anda bisa melihat apakah database kita meng-update status *Withdrawal*-nya secara benar.

---

### Kesimpulan
Dengan 3 langkah di atas (Ubah ke API Key Test, gunakan nomor rekening `1234567890`, dan siapkan Webhook), Anda bisa langsung menekan tombol *"Tarik Dana"* berkali-kali di aplikasi ini secara aman untuk menguji coba fitur. Uang Rp 1 Juta asli tidak akan berpindah sama sekali!
