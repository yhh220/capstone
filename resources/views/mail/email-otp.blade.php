<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f4f4f5; margin: 0; padding: 20px; color: #27272a; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(12, 12, 14, 0.12); border: 1px solid #e4e4e7; }
        .header { background: linear-gradient(135deg, #0C0C0E, #C8413D); padding: 32px; text-align: center; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; letter-spacing: 0.3px; }
        .header p { margin: 8px 0 0; opacity: 0.9; font-size: 14px; }
        .body-content { padding: 32px; }
        .intro { font-size: 15px; line-height: 1.6; color: #3f3f46; margin: 0 0 24px; }
        .code-box { text-align: center; background: #fafafa; border: 2px dashed #C8413D; border-radius: 14px; padding: 24px; margin: 0 0 24px; }
        .code { font-size: 40px; font-weight: bold; letter-spacing: 12px; color: #C8413D; font-family: 'Courier New', Courier, monospace; }
        .code-label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #a1a1aa; margin: 0 0 8px; }
        .note { font-size: 13px; line-height: 1.6; color: #71717a; margin: 0; }
        .footer { background: #0C0C0E; color: #a1a1aa; padding: 24px 32px; text-align: center; font-size: 12px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $isReset ? __('Reset your password') : __('Verify your email') }}</h1>
            <p>{{ config('services.store.seo_name', 'Win Win Car Audio') }}</p>
        </div>

        <div class="body-content">
            <p class="intro">
                {{ $isReset
                    ? __('Use the code below to reset your password. If you did not request this, you can safely ignore this email.')
                    : __('Welcome! Use the code below to finish creating your account.') }}
            </p>

            <div class="code-box">
                <p class="code-label">{{ __('Your code') }}</p>
                <div class="code">{{ $code }}</div>
            </div>

            <p class="note">
                {{ __('This code expires in :minutes minutes. Never share it with anyone — our staff will never ask for it.', ['minutes' => $minutes]) }}
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('services.store.name', 'Win Win Car Audio') }}<br>
            {{ __('This is an automated message — please do not reply.') }}
        </div>
    </div>
</body>
</html>
