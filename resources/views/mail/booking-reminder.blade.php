<x-mail.layout :message="$message ?? null" :title="__('Booking Reminder')" :preheader="__('Your booking :ref is tomorrow.', ['ref' => $booking->reference])">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">
        {{ __('Hi :name, this is a friendly reminder that your booking is tomorrow.', ['name' => $booking->customer_name]) }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;width:38%;">{{ __('Reference') }}</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $booking->reference }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;">{{ __('Service') }}</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $booking->service?->name ?: __('General visit') }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#71717a;">{{ __('Date & Time') }}</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;color:#C8413D;">{{ $booking->start_at?->format('D, d M Y · g:i A') }}</td>
        </tr>
    </table>

    <p style="margin:22px 0 0;font-size:13px;color:#71717a;line-height:1.6;">
        {{ __('Need to reschedule or cancel? Use the link below or WhatsApp us — no problem at all.') }}
    </p>

    <div style="text-align:center;margin-top:20px;">
        <a href="{{ url('/booking/track') }}" style="display:inline-block;background:#C8413D;color:#fff;padding:13px 30px;text-decoration:none;border-radius:30px;font-weight:bold;font-size:14px;">{{ __('Track / Manage Booking') }}</a>
    </div>

    <p style="margin-top:24px;font-size:13px;color:#888;">
        {{ __('Questions? WhatsApp us:') }} <strong>{{ config('services.store.phone_display') }}</strong>
    </p>
</x-mail.layout>
