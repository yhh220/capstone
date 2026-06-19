@props(['title' => null, 'preheader' => null, 'message' => null])
@php
    $store = config('services.store');
    // Embed the logo inline (CID) when sent as mail so it renders in any client —
    // even from localhost. Falls back to a public URL if rendered without a message.
    $logo = $message ? $message->embed(public_path('winwin-apple-touch-icon.png')) : asset('winwin-apple-touch-icon.png');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @isset($title)<title>{{ $title }}</title>@endisset
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:'Segoe UI',Helvetica,Arial,sans-serif;color:#27272a;">
    @isset($preheader)
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;mso-hide:all;">{{ $preheader }}</div>
    @endisset
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:24px 12px;">
        <tr><td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border:1px solid #e4e4e7;border-radius:16px;overflow:hidden;">
                {{-- Header --}}
                <tr><td align="center" style="background:linear-gradient(135deg,#0C0C0E,#C8413D);padding:26px 32px;">
                    <img src="{{ $logo }}" width="46" height="46" alt="" style="display:block;border-radius:11px;margin:0 auto 10px;">
                    <div style="color:#ffffff;font-size:18px;font-weight:800;letter-spacing:0.5px;text-transform:uppercase;">{{ $store['seo_name'] ?? 'Win Win Car Audio' }}</div>
                    @isset($title)<div style="color:rgba(255,255,255,0.85);font-size:13px;margin-top:5px;">{{ $title }}</div>@endisset
                </td></tr>
                {{-- Body --}}
                <tr><td style="padding:32px;">
                    {{ $slot }}
                </td></tr>
                {{-- Footer --}}
                <tr><td style="background:#0C0C0E;color:#a1a1aa;padding:24px 32px;text-align:center;font-size:12px;line-height:1.7;">
                    <strong style="color:#ffffff;">{{ $store['name'] ?? 'Win Win Car Audio Auto Accessories' }}</strong><br>
                    {{ $store['address'] ?? '' }}<br>
                    📞 {{ $store['phone_display'] ?? '' }} &nbsp;·&nbsp; ✉️ {{ $store['email'] ?? '' }}<br>
                    <span style="color:#71717a;">&copy; {{ date('Y') }} · {{ __('This is an automated message — please do not reply.') }}</span>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
