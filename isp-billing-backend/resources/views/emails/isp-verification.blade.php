@component('mail::message')
# Welcome to ISP Billing System

Hello {{ $user->name }},

Thank you for registering your company with ISP Billing System!

Please verify your email address by clicking the button below:

@component('mail::button', ['url' => $verificationUrl])
Verify Email Address
@endcomponent

This verification link will expire in 24 hours.

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
