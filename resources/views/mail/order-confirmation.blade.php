<x-mail.layout :message="$message ?? null" :title="__('Order Confirmed!')" :preheader="__('Order :number — thank you for your purchase.', ['number' => $order->order_number])">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">
        {{ __('Thank you for your purchase, :name!', ['name' => $order->customer_name]) }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;">{{ __('Order Number') }}</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;">{{ __('Status') }}</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ ucfirst($order->status) }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#71717a;">{{ __('Payment') }}</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;">{{ ucfirst($order->payment_status) }}</td>
        </tr>
    </table>

    <h3 style="margin:24px 0 8px;color:#18181B;font-size:15px;">{{ __('Items Ordered') }}</h3>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <thead>
            <tr>
                <th style="text-align:left;padding:10px 8px 10px 0;border-bottom:2px solid #C8413D;color:#52525b;font-size:11px;text-transform:uppercase;">{{ __('Product') }}</th>
                <th style="text-align:center;padding:10px 8px;border-bottom:2px solid #C8413D;color:#52525b;font-size:11px;text-transform:uppercase;">{{ __('Qty') }}</th>
                <th style="text-align:right;padding:10px 0 10px 8px;border-bottom:2px solid #C8413D;color:#52525b;font-size:11px;text-transform:uppercase;">{{ __('Price') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td style="padding:10px 8px 10px 0;border-bottom:1px solid #f0f0f0;">{{ $item->product_name }}</td>
                <td style="padding:10px 8px;border-bottom:1px solid #f0f0f0;text-align:center;">{{ $item->quantity }}</td>
                <td style="padding:10px 0 10px 8px;border-bottom:1px solid #f0f0f0;text-align:right;">RM {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td style="padding:8px 0;color:#71717a;font-size:13px;">{{ __('Subtotal') }}</td>
                <td></td>
                <td style="padding:8px 0;text-align:right;font-size:13px;">RM {{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td style="padding:2px 0;color:#71717a;font-size:13px;">{{ __('Shipping') }}</td>
                <td></td>
                <td style="padding:2px 0;text-align:right;font-size:13px;">{{ $order->isPickup() ? __('Free — store pickup') : ($order->shipping_fee > 0 ? 'RM ' . number_format($order->shipping_fee, 2) : __('Free')) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:12px;border-top:2px solid #18181B;font-weight:bold;font-size:16px;">{{ __('Total') }}</td>
                <td style="padding-top:12px;border-top:2px solid #18181B;text-align:right;font-weight:bold;font-size:16px;color:#C8413D;">RM {{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($order->isPickup() && $order->pickup_at)
    <p style="margin:16px 0 0;font-size:14px;line-height:1.6;color:#3f3f46;">
        <strong>{{ __('Pickup time') }}:</strong> {{ $order->pickup_at->locale(app()->getLocale())->translatedFormat('D, d M Y · h:mm A') }}
    </p>
    @endif

    <div style="text-align:center;margin-top:26px;">
        <a href="{{ url('/track-order') }}" style="display:inline-block;background:#C8413D;color:#fff;padding:13px 30px;text-decoration:none;border-radius:30px;font-weight:bold;font-size:14px;">{{ __('Track Your Order') }}</a>
    </div>

    <p style="margin-top:24px;font-size:13px;color:#888;">
        {{ __('Questions? WhatsApp us:') }} <strong>{{ config('services.store.phone_display') }}</strong>
    </p>
</x-mail.layout>
