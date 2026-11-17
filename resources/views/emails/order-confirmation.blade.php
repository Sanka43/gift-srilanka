<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #374151; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #0f172a;">Thank you for your order!</h1>
    <p>Your order has been placed successfully.</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Order ID</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">#{{ $order->id }}</td></tr>
        <tr><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Total Amount</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">LKR {{ number_format($order->total_amount, 2) }}</td></tr>
        <tr><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Delivery Address</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">{{ $order->address }}, {{ $order->district }}</td></tr>
        @if($order->delivery_date)
            <tr><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Requested Delivery</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">{{ $order->delivery_date->format('M d, Y') }}</td></tr>
        @endif
        @if($order->delivery_time)
            <tr><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Requested Time</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">{{ substr($order->delivery_time, 0, 5) }}</td></tr>
        @endif
        @if($order->special_note)
            <tr><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Special Note</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">{{ $order->special_note }}</td></tr>
        @endif
        <tr><td style="padding: 8px 0;"><strong>Estimated Delivery</strong></td><td style="padding: 8px 0;">{{ $order->estimated_delivery_date ? $order->estimated_delivery_date->format('M d, Y') : 'To be confirmed' }}</td></tr>
    </table>

    <p style="margin-top: 24px; color: #6b7280; font-size: 14px;">We will contact you for delivery. For any questions, please reply to this email.</p>
    <p style="margin-top: 16px;">— Gifts Sri Lanka</p>
</body>
</html>
