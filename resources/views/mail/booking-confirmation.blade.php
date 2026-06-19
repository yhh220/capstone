<x-mail.layout :message="$message ?? null" :title="__('Booking Received')" :preheader="__('Booking :ref — we will confirm shortly.', ['ref' => $booking->reference])">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">
        {{ __('Hi :name, we have received your booking request. Our team will confirm it shortly.', ['name' => $booking->customer_name]) }}
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
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;">{{ __('Date & Time') }}</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $booking->start_at?->format('D, d M Y · g:i A') }}</td>
        </tr>
        @if($booking->vehicle_model)
        <tr>
            <td style="padding:8px 0;color:#71717a;">{{ __('Vehicle') }}</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;">{{ trim(($booking->vehicle_model ?? '') . ' ' . ($booking->vehicle_plate ?? '')) }}</td>
        </tr>
        @endif
    </table>

    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:12px;padding:14px 16px;margin:22px 0;font-size:13px;color:#b45309;">
        {{ __('This is a request — your slot is confirmed once our team reviews it. We will be in touch via WhatsApp/phone.') }}
    </div>

    <div style="text-align:center;margin-top:24px;">
        <a href="{{ url('/booking/track') }}" style="display:inline-block;background:#C8413D;color:#fff;padding:13px 30px;text-decoration:none;border-radius:30px;font-weight:bold;font-size:14px;">{{ __('Track / Manage Booking') }}</a>
    </div>

    <p style="margin-top:24px;font-size:13px;color:#888;">
        {{ __('Questions? WhatsApp us:') }} <strong>{{ config('services.store.phone_display') }}</strong>
    </p>
</x-mail.layout>
