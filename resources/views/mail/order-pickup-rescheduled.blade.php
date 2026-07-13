<x-mail.layout :title="'Your pickup time has been updated'" :preheader="'Your collection time for '.$order->order_number.' has changed.'">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">Hi {{ $order->customer_name }}, your store-pickup time has been updated by our team.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr><td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;width:40%;">Order Number</td><td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $order->order_number }}</td></tr>
        <tr><td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;">Pickup time</td><td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $order->pickup_at?->translatedFormat('D, d M Y · h:mm A') }}</td></tr>
        <tr><td style="padding:8px 0;color:#71717a;vertical-align:top;">Pickup location</td><td style="padding:8px 0;text-align:right;font-weight:bold;">{{ config('services.store.address') }}</td></tr>
    </table>

    <p style="margin:16px 0 0;font-size:13px;line-height:1.6;color:#71717a;">Please bring your order number when you collect. If this time no longer works for you, contact us on WhatsApp.</p>
</x-mail.layout>
