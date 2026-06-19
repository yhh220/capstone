<x-mail.layout :message="$message ?? null" :title="$isReset ? __('Reset your password') : __('Verify your email')" :preheader="__('Your code') . ': ' . $code">
    <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#3f3f46;">
        {{ $isReset
            ? __('Use the code below to reset your password. If you did not request this, you can safely ignore this email.')
            : __('Welcome! Use the code below to finish creating your account.') }}
    </p>

    <div style="text-align:center;background:#fafafa;border:2px dashed #C8413D;border-radius:14px;padding:24px;margin:0 0 24px;">
        <p style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#a1a1aa;margin:0 0 8px;">{{ __('Your code') }}</p>
        <div style="font-size:40px;font-weight:bold;letter-spacing:12px;color:#C8413D;font-family:'Courier New',Courier,monospace;">{{ $code }}</div>
    </div>

    <p style="font-size:13px;line-height:1.6;color:#71717a;margin:0;">
        {{ __('This code expires in :minutes minutes. Never share it with anyone — our staff will never ask for it.', ['minutes' => $minutes]) }}
    </p>
</x-mail.layout>
