<!DOCTYPE html>
<html>
<head>
    <title>Order Status Update</title>
</head>
<body>
    <h1>Order Status Update</h1>
    <p>Dear {{ $order->customer->name }},</p>
    <p>Your order status has been updated.</p>
    <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
    <p><strong>Previous Status:</strong> {{ ucfirst($oldStatus) }}</p>
    <p><strong>New Status:</strong> {{ ucfirst($newStatus) }}</p>
    @if($newStatus === 'shipped')
        <p>Your order has been shipped!</p>
    @elseif($newStatus === 'delivered')
        <p>Your order has been delivered. Thank you for shopping with us!</p>
    @endif
</body>
</html>

