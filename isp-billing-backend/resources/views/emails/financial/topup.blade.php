@component('mail::message')
# Saldo Berhasil Ditambahkan

Halo, **{{ $isp->company_name }}**.
Kami menginformasikan bahwa pembayaran untuk isi saldo / tagihan paket Anda telah berhasil kami terima.

Berikut adalah rincian transaksinya:

@component('mail::panel')
- **ID Transaksi:** #{{ $payment->id }}
- **Tanggal Selesai:** {{ \Carbon\Carbon::parse($payment->updated_at)->translatedFormat('d F Y H:i:s') }}
- **Nominal Top-up:** Rp {{ number_format($payment->amount, 0, ',', '.') }}
- **Metode Pembayaran:** {{ $payment->payment_method }}
- **Status Pembayaran:** {{ strtoupper($payment->status) }}
@endcomponent

Terima kasih telah mempercayakan bisnis Anda bersama jaringan {{ config('app.name') }}.

Salam hangat,<br>
Tim {{ config('app.name') }}
@endcomponent
