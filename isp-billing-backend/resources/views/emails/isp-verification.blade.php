<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        /* Netflix-inspired Professional Email Design */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #ffffff;
            -webkit-font-smoothing: antialiased;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        /* Logo Section */
        .logo-section {
            padding: 40px 40px 20px 40px;
        }

        .logo-text {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        /* Content Section */
        .content-section {
            padding: 20px 40px 40px 40px;
        }

        .main-heading {
            font-size: 32px;
            font-weight: 700;
            color: #000000;
            line-height: 1.2;
            margin: 0 0 24px 0;
            letter-spacing: -0.5px;
        }

        .description {
            font-size: 18px;
            color: #333333;
            line-height: 1.5;
            margin: 0 0 32px 0;
        }

        /* Verification Button - Netflix Style */
        .button-container {
            margin: 0 0 32px 0;
        }

        .verify-button {
            display: inline-block;
            width: 100%;
            padding: 18px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .verify-button:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8b 100%);
        }

        /* Expiry Notice */
        .expiry-notice {
            font-size: 14px;
            color: #737373;
            margin: 0 0 32px 0;
            line-height: 1.5;
        }

        /* Divider */
        .divider {
            border: 0;
            border-top: 1px solid #e5e5e5;
            margin: 32px 0;
        }

        /* Alternative Link Section */
        .alt-link-section {
            margin: 0 0 32px 0;
        }

        .alt-link-title {
            font-size: 14px;
            color: #737373;
            margin: 0 0 8px 0;
        }

        .alt-link {
            font-size: 14px;
            color: #667eea;
            word-break: break-all;
            text-decoration: none;
        }

        /* Help Text */
        .help-text {
            font-size: 14px;
            color: #737373;
            line-height: 1.5;
            margin: 0;
        }

        /* Footer */
        .footer-section {
            padding: 32px 40px;
            background-color: #f7f7f7;
            border-top: 1px solid #e5e5e5;
        }

        .footer-text {
            font-size: 13px;
            color: #737373;
            text-align: center;
            margin: 0 0 8px 0;
            line-height: 1.5;
        }

        .footer-company {
            font-size: 13px;
            color: #000000;
            font-weight: 600;
            text-align: center;
            margin: 0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .logo-section,
            .content-section {
                padding-left: 24px;
                padding-right: 24px;
            }

            .main-heading {
                font-size: 28px;
            }

            .description {
                font-size: 16px;
            }

            .verify-button {
                font-size: 16px;
                padding: 16px 20px;
            }

            .footer-section {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Logo -->
        <div class="logo-section">
            <!-- Logo akan otomatis fallback ke text jika gambar tidak ada -->
            <img src="{{ config('app.url') }}/images/logo-email.png" 
                 alt="{{ config('app.name', 'Paynet') }}" 
                 style="max-width: 180px; height: auto; display: block;"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div class="logo-text" style="display: none;">{{ config('app.name', 'Paynet') }}</div>
        </div>

        <!-- Main Content -->
        <div class="content-section">
            <!-- Main Heading -->
            <h1 class="main-heading">Verify your email address</h1>

            <!-- Description -->
            <p class="description">
                To start using {{ config('app.name', 'Paynet') }}, please confirm your email address.
            </p>

            <!-- Verification Button -->
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">Verify Email</a>
            </div>

            <!-- Expiry Notice -->
            <p class="expiry-notice">
                This link will expire in 48 hours.
            </p>

            <!-- Divider -->
            <hr class="divider">

            <!-- Alternative Link -->
            <div class="alt-link-section">
                <p class="alt-link-title">If the button above doesn't work, copy and paste this link into your browser:</p>
                <a href="{{ $verificationUrl }}" class="alt-link">{{ $verificationUrl }}</a>
            </div>

            <!-- Help Text -->
            <p class="help-text">
                If you didn't create an account, you can safely ignore this email.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <p class="footer-company">{{ config('app.name', 'Paynet') }}</p>
            <p class="footer-text">© {{ date('Y') }} {{ config('app.name', 'Paynet') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
