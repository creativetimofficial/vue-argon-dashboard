<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f7f6;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" border="0" cellspacing="0" cellpadding="0" style="background:#ffffff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;max-width:600px;width:100%;">

                    <!-- Green Header -->
                    <tr>
                        <td align="center" style="padding:40px 40px 30px;background:linear-gradient(135deg,#2dce89 0%,#1a9e65 100%);">
                            <div style="width:64px;height:64px;background:rgba(255,255,255,0.2);border-radius:50%;display:inline-block;line-height:64px;text-align:center;margin-bottom:16px;">
                                <span style="font-size:32px;line-height:64px;">✉️</span>
                            </div>
                            <h1 style="color:#ffffff;margin:0;font-size:26px;font-weight:700;letter-spacing:-0.5px;">Verifikasi Email Anda</h1>
                            <p style="color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:14px;">{{ config('app.name', 'ISP Billing') }}</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:40px;">
                            <!-- Logo (optional fallback) -->
                            <div style="margin-bottom:24px;">
                                <img src="{{ config('app.url') }}/images/logo-email.png"
                                     alt="{{ config('app.name', 'ISP Billing') }}"
                                     style="max-width:140px;height:auto;display:block;"
                                     onerror="this.style.display='none';">
                            </div>

                            <h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;letter-spacing:-0.5px;">
                                Satu langkah lagi! 🚀
                            </h2>
                            <p style="font-size:16px;color:#374151;line-height:1.7;margin:0 0 28px;">
                                Terima kasih telah mendaftar di <strong>{{ config('app.name', 'ISP Billing') }}</strong>.
                                Harap verifikasi alamat email Anda untuk mulai menggunakan layanan.
                            </p>

                            <!-- Verify Button -->
                            <div style="text-align:center;margin:0 0 28px;">
                                <a href="{{ $verificationUrl }}"
                                   style="display:inline-block;padding:16px 48px;background:linear-gradient(135deg,#2dce89 0%,#1a9e65 100%);color:#ffffff;text-decoration:none;font-size:17px;font-weight:700;border-radius:10px;letter-spacing:0.3px;box-shadow:0 4px 15px rgba(45,206,137,0.35);">
                                    ✓ Verifikasi Email
                                </a>
                            </div>

                            <!-- Expiry Notice -->
                            <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:14px 16px;margin-bottom:28px;">
                                <p style="color:#92400e;font-size:13px;margin:0;line-height:1.5;">
                                    ⚠️ Link ini akan <strong>kadaluwarsa dalam 48 jam</strong>.
                                </p>
                            </div>

                            <hr style="border:0;border-top:1px solid #e5e7eb;margin:28px 0;">

                            <!-- Alternative Link -->
                            <p style="font-size:13px;color:#6b7280;margin:0 0 8px;">
                                Jika tombol di atas tidak berfungsi, salin dan tempel link berikut ke browser Anda:
                            </p>
                            <p style="font-size:13px;margin:0 0 24px;">
                                <a href="{{ $verificationUrl }}" style="color:#1a9e65;word-break:break-all;text-decoration:none;">{{ $verificationUrl }}</a>
                            </p>

                            <p style="font-size:13px;color:#9ca3af;margin:0;line-height:1.6;">
                                Jika Anda tidak membuat akun ini, Anda dapat mengabaikan email ini dengan aman.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#f0fff8 0%,#f4f7f6 100%);padding:24px 40px;border-top:1px solid #e5e7eb;">
                            <p style="margin:0;color:#9ca3af;font-size:12px;text-align:center;line-height:1.6;">
                                © {{ date('Y') }} <strong style="color:#1a9e65;">{{ config('app.name', 'ISP Billing') }}</strong>. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
