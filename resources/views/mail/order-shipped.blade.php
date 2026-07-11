<x-mail.layout :message="$message ?? null"
               :title="$order->isPickup() ? __('Your order is ready for pickup!') : __('Your order has shipped!')"
               :preheader="$order->isPickup() ? __('Order :number is ready for collection.', ['number' => $order->order_number]) : __('Order :number is on its way.', ['number' => $order->order_number])">
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#3f3f46;">
        @if($order->isPickup())
            {{ __('Good news, :name — your order is packed and ready for collection at our showroom!', ['name' => $order->customer_name]) }}
        @else
            {{ __('Good news, :name — your order is on its way!', ['name' => $order->customer_name]) }}
        @endif
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;color:#71717a;width:40%;">{{ __('Order Number') }}</td>
            <td style="padding:8px 0;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:bold;">{{ $order->order_number }}</td>
        </tr>
        @if($order->isPickup())
        <tr>
            <td style="padding:8px 0;color:#71717a;vertical-align:top;">{{ __('Pickup location') }}</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;">{{ config('services.store.address') }}</td>
        </tr>
        @elseif($order->tracking_number)
        <tr>
            <td style="padding:8px 0;color:#71717a;">{{ __('Courier tracking number') }}</td>
            <td style="padding:8px 0;text-align:right;font-weight:bold;color:#C8413D;">{{ $order->tracking_number }}</td>
        </tr>
        @endif
    </table>

    @unless($order->isPickup())
    @if($order->tracking_number)
    <p style="margin:12px 0 0;font-size:12px;line-height:1.6;color:#a1a1aa;">
        {{ __("This is the courier company's number — use it on their website (e.g. GDEX, Ninja Van) to follow the parcel.") }}
    </p>
    @endif
    @endunless

    @if($order->isPickup())
    <p style="margin:16px 0 0;font-size:13px;line-height:1.6;color:#71717a;">
        {{ __('Bring your order number when you come — any of our staff can hand it over. See you soon!') }}
    </p>
    @endif

    <div style="text-align:center;margin-top:26px;">
        <a href="{{ url('/track-order') }}" style="display:inline-block;background:#C8413D;color:#fff;padding:13px 30px;text-decoration:none;border-radius:30px;font-weight:bold;font-size:14px;">{{ __('Track Your Order') }}</a>
    </div>

    <p style="margin-top:24px;font-size:13px;color:#888;">
        {{ __('Questions? WhatsApp us:') }} <strong>{{ config('services.store.phone_display') }}</strong>
    </p>
</x-mail.layout>
