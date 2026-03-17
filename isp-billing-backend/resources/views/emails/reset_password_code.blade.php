<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding: 40px 16px;">
                <table role="presentation" width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; max-width: 600px; width: 100%;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 40px 30px; background: linear-gradient(135deg, #2dce89 0%, #1a9e65 100%);">
                            <div style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                                <span style="font-size: 28px;">🔒</span>
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">Reset Password</h1>
                            <p style="color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 14px;">{{ config('app.name', 'ISP Billing') }}</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #374151; font-size: 16px; margin: 0 0 12px; line-height: 1.6;">
                                Halo! Kami menerima permintaan untuk mereset password akun Anda.
                            </p>
                            <p style="color: #6b7280; font-size: 15px; margin: 0 0 32px; line-height: 1.6;">
                                Gunakan kode verifikasi (OTP) berikut untuk melanjutkan:
                            </p>
                            
                            <!-- OTP Code Box -->
                            <div style="text-align: center; margin: 0 0 32px;">
                                <div style="display: inline-block; padding: 20px 40px; background: linear-gradient(135deg, #f0fff8 0%, #e6fff5 100%); border-radius: 12px; border: 2px solid #2dce89;">
                                    <p style="margin: 0 0 4px; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #1a9e65; font-weight: 600;">Kode OTP Anda</p>
                                    <span style="font-family: 'Courier New', monospace; font-size: 38px; font-weight: 800; letter-spacing: 8px; color: #1a9e65; display: block; line-height: 1;">
                                        {{ $code }}
                                    </span>
                                </div>
                            </div>
                            
                            <div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 14px 16px; margin-bottom: 28px;">
                                <p style="color: #92400e; font-size: 13px; margin: 0; line-height: 1.5;">
                                    ⚠️ Kode ini akan <strong>kadaluwarsa dalam 15 menit</strong>. Jangan bagikan kode ini kepada siapapun.
                                </p>
                            </div>
                            
                            <p style="color: #9ca3af; font-size: 13px; text-align: center; margin: 0; line-height: 1.6;">
                                Jika Anda tidak meminta reset password, abaikan email ini.<br>
                                Akun Anda tetap aman.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f0fff8 0%, #f4f7f6 100%); padding: 24px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center; line-height: 1.6;">
                                © {{ date('Y') }} <strong style="color: #1a9e65;">{{ config('app.name', 'ISP Billing') }}</strong>. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
