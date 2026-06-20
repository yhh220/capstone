{{-- Always English, regardless of the site's active locale — see OrderCancelledMail's
     constructor (->locale('en')). Matches invoice.blade.php's convention: no __() here. --}}
<x-mail.layout :message="$message ?? null" title="Order Cancelled" preheader="Order {{ $order->order_number }} has been cancelled.">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">
        Hi {{ $order->customer_name }}, your order has been cancelled.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;width:40%;">Order Number</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;{{ $order->refund_amount !== null ? 'border-bottom:1px solid #f0f0f0;' : '' }}color:#71717a;">Cancelled On</td>
            <td style="padding:8px 0;{{ $order->refund_amount !== null ? 'border-bottom:1px solid #f0f0f0;' : '' }}text-align:right;font-weight:bold;">{{ $order->cancelled_at?->format('d M Y, h:i A') }}</td>
        </tr>
        @if($order->refund_amount !== null)
        <tr>
            <td style="padding:8px 0;color:#71717a;">Refund</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;color:#C8413D;">RM {{ number_format($order->refund_amount, 2) }} ({{ rtrim(rtrim((string) $order->refund_percentage, '0'), '.') }}%)</td>
        </tr>
        @endif
    </table>

    @if($order->refund_amount !== null)
    <p style="margin-top:20px;font-size:13px;line-height:1.6;color:#71717a;background:#fafafa;border:1px solid #f0f0f0;border-radius:10px;padding:14px 16px;">
        This refund has been recorded for our team to process. You'll get a separate email once it has actually been sent.
    </p>
    @endif

    <div style="text-align:center;margin-top:26px;">
        <a href="{{ url('/my-account') }}" style="display:inline-block;background:#C8413D;color:#fff;padding:13px 30px;text-decoration:none;border-radius:30px;font-weight:bold;font-size:14px;">View My Orders</a>
    </div>

    <p style="margin-top:24px;font-size:13px;color:#888;">
        Questions? WhatsApp us: <strong>{{ config('services.store.phone_display') }}</strong>
    </p>
</x-mail.layout>
