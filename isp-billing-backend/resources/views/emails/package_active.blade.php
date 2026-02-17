<!DOCTYPE html>
<html>
<head>
    <title>Layanan Aktif</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #2dce89;">Layanan Anda Telah Aktif!</h2>
        <p>Halo <strong>{{ $isp->name }}</strong>,</p>
        
        <p>Terima kasih telah melakukan pembayaran. Layanan berlangganan Anda kini telah aktif.</p>
        
        <div style="background-color: #f8f9fe; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Paket/Layanan:</strong> {{ $order->service_name }}</p>
            <p style="margin: 5px 0;"><strong>Periode Aktif:</strong> {{ \Carbon\Carbon::parse($order->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($order->expired_date)->format('d M Y') }}</p>
            
            @if($order->username)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ddd;">
                <p style="margin: 5px 0;"><strong>Username VPN:</strong> {{ $order->username }}</p>
                <p style="margin: 5px 0;"><strong>Password VPN:</strong> {{ $order->password }}</p>
                <p style="margin: 5px 0;"><strong>Server:</strong> {{ $order->server_address }}</p>
            </div>
            @endif
        </div>
        
        <p>Anda dapat mengelola layanan ini melalui Client Area.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #2dce89; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Kelola Layanan</a>
        </div>
        
        <p style="font-size: 12px; color: #888; margin-top: 30px;">
            Email ini dibuat secara otomatis. Mohon jangan membalas email ini.<br>
            Jl. Raya ISP No. 123, Jakarta, Indonesia
        </p>
    </div>
</body>
</html>
