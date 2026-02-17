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
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 0; background: linear-gradient(135deg, #1f2937 0%, #111827 100%);">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">Reset Password</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #4b5563; font-size: 16px; margin-bottom: 24px; text-align: center;">
                                Kami menerima permintaan untuk mereset password akun Paynet Anda.<br>
                                Gunakan kode verifikasi (OTP) berikut untuk melanjutkan:
                            </p>
                            
                            <!-- OTP Code Box -->
                            <div style="text-align: center; margin: 30px 0;">
                                <div style="display: inline-block; padding: 15px 30px; background-color: #f3f4f6; border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <span style="font-family: monospace; font-size: 32px; font-weight: 700; letter-spacing: 5px; color: #1f2937;">
                                        {{ $code }}
                                    </span>
                                </div>
                            </div>
                            
                            <p style="color: #6b7280; font-size: 14px; text-align: center; margin-bottom: 0;">
                                Kode ini akan kadaluwarsa dalam 15 menit.<br>
                                Jika Anda tidak meminta reset password, abaikan email ini.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px; text-align: center;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                &copy; {{ date('Y') }} Paynet. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
