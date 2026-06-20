{{-- Always English, regardless of the site's active locale — see
     OrderRefundProcessedMail's constructor (->locale('en')). No __() here, by design. --}}
<x-mail.layout :message="$message ?? null" title="Refund Sent" preheader="Your refund for order {{ $order->order_number }} has been sent.">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">
        Hi {{ $order->customer_name }}, your refund has been sent.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;width:40%;">Order Number</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#71717a;">Refund Amount</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;color:#16a34a;">RM {{ number_format($order->refund_amount, 2) }}</td>
        </tr>
    </table>

    <p style="margin-top:20px;font-size:13px;line-height:1.6;color:#71717a;background:#fafafa;border:1px solid #f0f0f0;border-radius:10px;padding:14px 16px;">
        This refund has been sent to your original payment method. If you don't see it shortly, please get in touch with us.
    </p>

    <p style="margin-top:24px;font-size:13px;color:#888;">
        Questions? WhatsApp us: <strong>{{ config('services.store.phone_display') }}</strong>
    </p>
</x-mail.layout>
