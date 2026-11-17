<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back in stock</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #374151; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #0f172a;">Good news!</h1>
    <p>The item you asked to be notified about is back in stock.</p>

    <p style="margin: 20px 0; font-size: 18px; font-weight: 600;">{{ $product->name }}</p>
    <p style="margin-bottom: 24px;">LKR {{ number_format($product->effective_price, 0) }}</p>

    <p>
        <a href="{{ route('products.show', $product->slug) }}" style="display: inline-block; padding: 12px 24px; background: #22c55e; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">View product</a>
    </p>

    <p style="margin-top: 24px; color: #6b7280; font-size: 14px;">— Gifts Sri Lanka</p>
</body>
</html>
