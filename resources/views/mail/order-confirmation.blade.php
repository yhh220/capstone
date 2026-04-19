<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f5; margin: 0; padding: 20px; color: #27272a; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(24, 24, 27, 0.12); border: 1px solid #e4e4e7; }
        .header { background: linear-gradient(135deg, #18181B, #E11D48); padding: 32px; text-align: center; color: #fff; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 8px 0 0; opacity: 0.9; font-size: 14px; }
        .body-content { padding: 32px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .info-label { color: #71717a; }
        .info-value { font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px; }
        .items-table th { text-align: left; padding: 10px 8px; border-bottom: 2px solid #E11D48; color: #52525b; font-size: 12px; text-transform: uppercase; }
        .items-table td { padding: 10px 8px; border-bottom: 1px solid #f0f0f0; }
        .total-row td { font-weight: bold; font-size: 16px; border-top: 2px solid #18181B; padding-top: 12px; }
        .track-btn { display: inline-block; background: #E11D48; color: #fff; padding: 14px 32px; text-decoration: none; border-radius: 30px; font-weight: bold; font-size: 14px; margin-top: 16px; }
        .footer { background: #18181B; color: #a1a1aa; padding: 24px 32px; text-align: center; font-size: 12px; line-height: 1.6; }
        .emoji { font-size: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Order Confirmed!</h1>
            <p>Thank you for your purchase, {{ $order->customer_name }}!</p>
        </div>

        <div class="body-content">
            <div class="info-row">
                <span class="info-label">Order Number</span>
                <span class="info-value">{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tracking Number</span>
                <span class="info-value">{{ $order->tracking_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="info-row" style="border-bottom: none;">
                <span class="info-label">Payment</span>
                <span class="info-value">{{ ucfirst($order->payment_status) }}</span>
            </div>

            <h3 style="margin-top: 24px; color: #18181B;">Items Ordered</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">RM {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2">Total</td>
                        <td style="text-align: right; color: #E11D48;">RM {{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ url('/track-order') }}" class="track-btn">Track Your Order</a>
            </div>

            <p style="margin-top: 24px; font-size: 13px; color: #888;">
                Questions? WhatsApp us: <strong>016-915 0917</strong>
            </p>
        </div>

        <div class="footer">
            <strong>Win Win Car Audio Auto Accessories</strong><br>
            No. 22, Ground Floor, Jalan Dinar CU3/C,<br>
            Taman Subang Perdana, Seksyen U3,<br>
            40150 Shah Alam, Selangor<br><br>
            📞 016-915 0917
        </div>
    </div>
</body>
</html>
