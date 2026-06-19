<x-mail.layout :message="$message ?? null" :title="__('Your order has shipped!')" :preheader="__('Order :number is on its way.', ['number' => $order->order_number])">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">
        {{ __('Good news, :name — your order is on its way!', ['name' => $order->customer_name]) }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;width:40%;">{{ __('Order Number') }}</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        @if($order->tracking_number)
        <tr>
            <td style="padding:8px 0;color:#71717a;">{{ __('Tracking Number') }}</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;color:#C8413D;">{{ $order->tracking_number }}</td>
        </tr>
        @endif
    </table>

    <div style="text-align:center;margin-top:26px;">
        <a href="{{ url('/track-order') }}" style="display:inline-block;background:#C8413D;color:#fff;padding:13px 30px;text-decoration:none;border-radius:30px;font-weight:bold;font-size:14px;">{{ __('Track Your Order') }}</a>
    </div>

    <p style="margin-top:24px;font-size:13px;color:#888;">
        {{ __('Questions? WhatsApp us:') }} <strong>{{ config('services.store.phone_display') }}</strong>
    </p>
</x-mail.layout>
