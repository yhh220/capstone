@php
    $title = match ($purpose) {
        'pwreset'   => __('Reset your password'),
        'login2fa'  => __("Confirm it's you"),
        'enable2fa' => __("Confirm it's you"),
        'setpw'     => __('Set your password'),
        default     => __('Verify your email'),
    };
    $body = match ($purpose) {
        'pwreset'   => __('Use the code below to reset your password. If you did not request this, you can safely ignore this email.'),
        'login2fa'  => __('Use the code below to finish signing in. If this was not you, please change your password immediately.'),
        'enable2fa' => __('Use the code below to confirm you want to turn on login verification for your account.'),
        'setpw'     => __('Use the code below to set a password for your account.'),
        default     => __('Welcome! Use the code below to finish creating your account.'),
    };
@endphp
<x-mail.layout :message="$message ?? null" :title="$title" :preheader="__('Your code') . ': ' . $code">
    <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#3f3f46;">
        {{ $body }}
    </p>

    <div style="text-align:center;background:#fafafa;border:2px dashed #C8413D;border-radius:14px;padding:24px;margin:0 0 24px;">
        <p style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#a1a1aa;margin:0 0 8px;">{{ __('Your code') }}</p>
        <div style="font-size:40px;font-weight:bold;letter-spacing:12px;color:#C8413D;font-family:'Courier New',Courier,monospace;">{{ $code }}</div>
    </div>

    <p style="font-size:13px;line-height:1.6;color:#71717a;margin:0;">
        {{ __('This code expires in :minutes minutes. Never share it with anyone — our staff will never ask for it.', ['minutes' => $minutes]) }}
    </p>
</x-mail.layout>
