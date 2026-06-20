<x-mail.layout :message="$message ?? null" :title="'Your order has been delivered!'" :preheader="'Order ' . $order->order_number . ' has been delivered.'">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">
        Hi {{ $order->customer_name }}, great news — your order has been delivered!
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;width:40%;">Order Number</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#71717a;">Total</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;">RM {{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    <p style="margin-top:20px;font-size:14px;line-height:1.6;color:#3f3f46;">
        We hope you're happy with your purchase! If you have any questions or issues, please don't hesitate to contact us.
    </p>

    <p style="margin-top:8px;font-size:13px;color:#888;">
        Questions? WhatsApp us: <strong>{{ config('services.store.phone_display') }}</strong>
    </p>
</x-mail.layout>
