<x-mail.layout :message="$message ?? null" :title="'Booking Confirmed!'" :preheader="'Your booking ' . $booking->reference . ' is confirmed.'">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">
        Hi {{ $booking->customer_name }}, your booking is confirmed — we look forward to seeing you!
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;width:38%;">Reference</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $booking->reference }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;">Service</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $booking->service?->name ?: 'General visit' }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#71717a;">Date & Time</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;">{{ $booking->start_at?->format('D, d M Y · g:i A') }}</td>
        </tr>
    </table>

    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:14px 16px;margin:22px 0;font-size:13px;color:#166534;">
        Your slot is confirmed. See you then!
    </div>

    <p style="margin-top:8px;font-size:13px;color:#888;">
        Questions? WhatsApp us: <strong>{{ config('services.store.phone_display') }}</strong>
    </p>
</x-mail.layout>
