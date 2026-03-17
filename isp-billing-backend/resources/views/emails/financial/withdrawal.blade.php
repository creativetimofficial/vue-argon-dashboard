@component('mail::message')
# Pemberitahuan Pencairan Dana (Withdrawal)

@if($isAdminWarning)
**[Peringatan Keamanan Admin]**
Terdapat aktivitas penarikan dana dari klien ISP Anda.
@else
Halo, **{{ $isp->company_name }}**.
Permintaan pencairan dana Anda sedang diproses oleh sistem Payment Gateway.
@endif

Berikut adalah rincian transaksinya:

@component('mail::panel')
- **Nominal Ditarik:** Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
- **Biaya Admin:** Rp {{ number_format($withdrawal->admin_fee, 0, ',', '.') }}
- **Total Diterima:** Rp {{ number_format($withdrawal->total_transfer, 0, ',', '.') }}
<br>
- **Bank Tujuan:** {{ $withdrawal->bank_name }}
- **Nomor Rekening:** {{ $withdrawal->account_number }}
- **Atas Nama:** {{ $withdrawal->account_name }}
- **Status Sekarang:** {{ strtoupper($withdrawal->status) }}
@endcomponent

Dana biasanya akan masuk ke rekening Anda dalam hitungan detik hingga 5 menit ke depan jika menggunakan akun Xendit Live. Namun kadang bergantung pada jaringan bank terkait.
Bila status penarikan tertahan atau gagal, dana akan dikembalikan ke saldo sistem Anda.

Terima kasih,<br>
Tim {{ config('app.name') }}
@endcomponent
