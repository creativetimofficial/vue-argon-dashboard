<!DOCTYPE html>
<html>
<head>
    <title>Tagihan Baru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #1a9e65;">Tagihan Baru Tersedia</h2>
        <p>Halo <strong>{{ $isp->name }}</strong>,</p>
        
        <p>Tagihan baru telah diterbitkan untuk layanan Anda.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Nomor Invoice:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Jatuh Tempo:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</td>
            </tr>
        </table>
        
        <p>Silakan lakukan pembayaran sebelum tanggal jatuh tempo untuk menghindari gangguan layanan.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #2dce89; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Lihat & Bayar Tagihan</a>
        </div>
        
        <p style="font-size: 12px; color: #888; margin-top: 30px;">
            Email ini dibuat secara otomatis. Mohon jangan membalas email ini.<br>
            Jl. Raya ISP No. 123, Jakarta, Indonesia
        </p>
    </div>
</body>
</html>
