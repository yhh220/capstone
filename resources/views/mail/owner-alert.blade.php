<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $heading }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:'Segoe UI',Helvetica,Arial,sans-serif;color:#18181b;">
    <div style="max-width:560px;margin:0 auto;padding:24px 16px;">
        <div style="background:#ffffff;border:1px solid #e4e4e7;border-radius:16px;overflow:hidden;">
            <div style="background:#18181b;padding:18px 24px;">
                <span style="color:#ffffff;font-size:15px;font-weight:700;letter-spacing:.02em;">
                    {{ config('services.store.seo_name', 'Win Win Car Audio') }}
                </span>
            </div>
            <div style="padding:24px;">
                <h1 style="margin:0 0 4px;font-size:19px;font-weight:700;color:#18181b;">{{ $heading }}</h1>
                <p style="margin:0 0 18px;font-size:13px;color:#71717a;">
                    A new submission just came in from your website.
                </p>

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                    @foreach ($rows as $label => $value)
                        @if (filled($value))
                            <tr>
                                <td style="padding:8px 0;border-bottom:1px solid #f4f4f5;font-size:12px;color:#a1a1aa;width:38%;vertical-align:top;text-transform:uppercase;letter-spacing:.04em;">
                                    {{ $label }}
                                </td>
                                <td style="padding:8px 0;border-bottom:1px solid #f4f4f5;font-size:14px;color:#18181b;vertical-align:top;">
                                    {{ $value }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>

                @if ($actionUrl)
                    <div style="margin-top:24px;">
                        <a href="{{ $actionUrl }}" style="display:inline-block;background:#f43f5e;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:11px 22px;border-radius:10px;">
                            {{ $actionLabel ?? 'Open in Admin' }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <p style="margin:16px 4px 0;font-size:11px;color:#a1a1aa;line-height:1.5;">
            You are receiving this because you are the shop owner. To protect your inbox, these alerts are capped — during a sudden surge of submissions, only the first few are emailed and the rest are visible in the admin panel.
        </p>
    </div>
</body>
</html>
