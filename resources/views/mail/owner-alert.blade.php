<x-mail.layout :message="$message ?? null" :title="$heading" :preheader="$heading">
    <p style="margin:0 0 18px;font-size:13px;color:#71717a;">
        {{ __('A new submission just came in from your website.') }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        @foreach ($rows as $label => $value)
            @if (filled($value))
                <tr>
                    <td style="padding:8px 0;border-bottom:1px solid #f4f4f5;font-size:12px;color:#a1a1aa;width:38%;vertical-align:top;text-transform:uppercase;letter-spacing:.04em;">{{ $label }}</td>
                    <td style="padding:8px 0;border-bottom:1px solid #f4f4f5;font-size:14px;color:#18181b;vertical-align:top;">{{ $value }}</td>
                </tr>
            @endif
        @endforeach
    </table>

    @if ($actionUrl)
        <div style="margin-top:24px;">
            <a href="{{ $actionUrl }}" style="display:inline-block;background:#C8413D;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:11px 22px;border-radius:10px;">
                {{ $actionLabel ?? __('Open in Admin') }}
            </a>
        </div>
    @endif
</x-mail.layout>
