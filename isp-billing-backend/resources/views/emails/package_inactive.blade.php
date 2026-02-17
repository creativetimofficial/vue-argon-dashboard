<!DOCTYPE html>
<html>
<head>
    <title>Layanan Nonaktif</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #f5365c;">Pemberitahuan Layanan {{ $reason === 'expired' ? 'Berakhir' : 'Nonaktif' }}</h2>
        <p>Halo <strong>{{ $isp->name }}</strong>,</p>
        
        <p>
            @if($reason === 'expired')
                Layanan berlangganan Anda telah berakhir.
            @else
                Layanan berlangganan Anda telah dinonaktifkan (Dibatalkan).
            @endif
        </p>
        
        <div style="background-color: #f8f9fe; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Paket/Layanan:</strong> {{ $order->service_name }}</p>
            <p style="margin: 5px 0;"><strong>Referensi Order:</strong> #{{ $order->reference }}</p>
        </div>
        
        <p>Jika Anda ingin mengaktifkan kembali layanan ini, silakan buat order baru atau perpanjang layanan melalui Client Area.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #5e72e4; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Ke Client Area</a>
        </div>
        
        <p style="font-size: 12px; color: #888; margin-top: 30px;">
            Email ini dibuat secara otomatis. Mohon jangan membalas email ini.<br>
            Jl. Raya ISP No. 123, Jakarta, Indonesia
        </p>
    </div>
</body>
</html>
