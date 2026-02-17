<!DOCTYPE html>
<html>
<head>
    <title>Layanan Segera Berakhir</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #fb6340;">Peringatan: Layanan Segera Berakhir</h2>
        <p>Halo <strong>{{ $isp->name }}</strong>,</p>
        
        <p>Kami informasikan bahwa layanan berlangganan Anda akan segera berakhir dalam waktu dekat.</p>
        
        <div style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #ffeeba;">
            <p style="margin: 5px 0;"><strong>Paket/Layanan:</strong> {{ $order->service_name }}</p>
            <p style="margin: 5px 0;"><strong>Tanggal Berakhir:</strong> {{ \Carbon\Carbon::parse($order->expired_date)->format('d M Y') }}</p>
            <p style="margin: 5px 0;"><strong>Sisa Waktu:</strong> {{ $days_left < 0 ? 0 : ceil($days_left) }} Hari</p>
        </div>
        
        <p>Harap segera melakukan perpanjangan layanan sebelum tanggal tersebut untuk menghindari gangguan layanan.</p>
        <p>Kami memberikan masa tenggang (grace period) selama <strong>3 hari</strong> setelah tanggal berakhir sebelum layanan dinonaktifkan sepenuhnya.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #fb6340; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Perpanjang Sekarang</a>
        </div>
        
        <p style="font-size: 12px; color: #888; margin-top: 30px;">
            Email ini dibuat secara otomatis. Mohon jangan membalas email ini.<br>
            Jl. Raya ISP No. 123, Jakarta, Indonesia
        </p>
    </div>
</body>
</html>
