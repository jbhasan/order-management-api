<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Order Confirmation</h1>
    <p>Dear {{ $order->customer->name }},</p>
    <p>Thank you for your order!</p>
    <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
    <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
    <h2>Order Items:</h2>
    <ul>
        @foreach($order->items as $item)
            <li>{{ $item->product_name }} - Quantity: {{ $item->quantity }} - ${{ number_format($item->total_price, 2) }}</li>
        @endforeach
    </ul>
    <p>We will send you another email when your order ships.</p>
</body>
</html>

