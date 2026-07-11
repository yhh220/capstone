@php
    $isPdf = $pdf ?? false;
    $paid = $order->payment_status === 'paid';
    $cancelled = $order->status === 'cancelled';
    $refunded = $cancelled && $order->refund_amount !== null && (float) $order->refund_amount > 0;
@endphp
{{-- Invoices are a formal financial document, kept in fixed English regardless of
     the site's UI language — so no __() translation calls here, by design. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #27272a; margin: 0; background: #f4f4f5; font-size: 13px; }
        .sheet { max-width: 760px; margin: 24px auto; background: #fff; padding: 40px; border: 1px solid #e4e4e7; }
        .head { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        .head td { vertical-align: top; }
        .brand { font-size: 20px; font-weight: bold; color: #C8413D; }
        .muted { color: #71717a; font-size: 12px; line-height: 1.6; }
        .doc-title { font-size: 26px; font-weight: bold; letter-spacing: 1px; text-align: right; color: #18181b; }
        .meta { text-align: right; font-size: 12px; color: #52525b; line-height: 1.7; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-paid { background: #dcfce7; color: #15803d; }
        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-cancelled { background: #fee2e2; color: #b91c1c; }
        .notice { border-radius: 8px; padding: 12px 14px; font-size: 12px; line-height: 1.6; margin-bottom: 20px; }
        .notice-cancelled { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .section-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #a1a1aa; margin-bottom: 4px; }
        table.items { width: 100%; border-collapse: collapse; margin: 8px 0 0; }
        table.items th { text-align: left; font-size: 11px; text-transform: uppercase; color: #52525b; border-bottom: 2px solid #C8413D; padding: 8px 6px; }
        table.items td { padding: 9px 6px; border-bottom: 1px solid #f0f0f0; font-size: 12px; }
        .right { text-align: right; }
        .totals { width: 100%; margin-top: 14px; }
        .totals td { padding: 4px 6px; font-size: 13px; }
        .totals .grand td { border-top: 2px solid #18181b; padding-top: 10px; font-size: 16px; font-weight: bold; }
        .grand .amt { color: #C8413D; }
        .foot { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e4e4e7; color: #a1a1aa; font-size: 11px; text-align: center; line-height: 1.7; }
        .toolbar { max-width: 760px; margin: 16px auto 0; text-align: right; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: bold; text-decoration: none; }
        .btn-red { background: #C8413D; color: #fff; }
        .btn-grey { background: #e4e4e7; color: #27272a; }
        @media print { .toolbar { display: none; } body { background: #fff; } .sheet { border: 0; margin: 0; max-width: 100%; } }
    </style>
</head>
<body>
    @unless($isPdf)
    <div class="toolbar">
        <a href="{{ route('invoice.pdf', $order->order_number) }}" download="invoice-{{ $order->order_number }}.pdf" class="btn btn-red">Download PDF</a>
        <a href="javascript:window.print()" class="btn btn-grey">Print</a>
    </div>
    @endunless

    <div class="sheet">
        <div style="background:#fef3c7; border:1px solid #fcd34d; color:#b45309; text-align:center; padding:8px 12px; border-radius:8px; font-size:11px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; margin-bottom:24px;">
            For demo / testing only — not a valid tax invoice
        </div>

        @if($cancelled)
        <div class="notice notice-cancelled">
            <strong>This order was cancelled</strong> on {{ $order->cancelled_at?->format('d M Y, h:i A') ?? 'an unrecorded date' }}.
            @if($order->cancellation_reason)
                Reason: {{ $order->cancellation_reason }}.
            @endif
            @if($refunded)
                A refund of RM {{ number_format($order->refund_amount, 2) }} ({{ number_format($order->refund_percentage, 0) }}%) was issued{{ $order->refunded_at ? ' on ' . $order->refunded_at->format('d M Y, h:i A') : '' }}.
            @else
                No refund was issued for this order.
            @endif
        </div>
        @endif
        <table class="head">
            <tr>
                <td>
                    <div class="brand">{{ config('services.store.name') }}</div>
                    <div class="muted">
                        {{ config('services.store.address') }}<br>
                        {{ config('services.store.phone_display') }} · {{ config('services.store.email') }}
                    </div>
                </td>
                <td>
                    <div class="doc-title">INVOICE</div>
                    <div class="meta">
                        <strong>{{ $order->order_number }}</strong><br>
                        {{ $order->created_at->format('d M Y, h:i A') }}<br>
                        @if($cancelled)
                            <span class="badge badge-cancelled">Cancelled</span>
                        @else
                            <span class="badge {{ $paid ? 'badge-paid' : 'badge-pending' }}">{{ $paid ? 'Paid' : 'Unpaid' }}</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="head">
            <tr>
                <td>
                    <div class="section-label">Bill To</div>
                    <div>
                        <strong>{{ $order->customer_name }}</strong><br>
                        <span class="muted">
                            {{ $order->customer_email }}<br>
                            {{ $order->customer_phone }}
                            @if($order->isPickup())
                                <br>Store pickup — {{ config('services.store.address') }}
                            @elseif(is_array($order->shipping_address))
                                <br>{{ collect($order->shipping_address)->filter()->implode(', ') }}
                            @endif
                        </span>
                    </div>
                </td>
                <td class="right">
                    <div class="section-label">Payment Method</div>
                    <div>{{ $order->payment_method }}</div>
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="right">Qty</th>
                    <th class="right">Unit Price</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">RM {{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">RM {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td></td>
                <td class="right muted" style="width:160px;">Subtotal</td>
                <td class="right" style="width:120px;">RM {{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="right muted">Shipping</td>
                <td class="right">{{ $order->isPickup() ? 'Free (store pickup)' : ($order->shipping_fee > 0 ? 'RM ' . number_format($order->shipping_fee, 2) : 'Free') }}</td>
            </tr>
            <tr class="grand">
                <td></td>
                <td class="right">Total</td>
                <td class="right amt">RM {{ number_format($order->total_amount, 2) }}</td>
            </tr>
            @if($refunded)
            <tr>
                <td></td>
                <td class="right muted">Refunded</td>
                <td class="right" style="color:#b91c1c;">-RM {{ number_format($order->refund_amount, 2) }}</td>
            </tr>
            @endif
        </table>

        <div class="foot">
            Thank you for your order!<br>
            {{ config('services.store.name') }} · {{ config('services.store.phone_display') }}
        </div>
    </div>
</body>
</html>
