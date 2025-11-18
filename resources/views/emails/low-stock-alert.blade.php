<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alert</title>
</head>
<body>
    <h1>Low Stock Alert</h1>
    <p>This is to notify you that the following product is running low on stock:</p>
    <p><strong>Product:</strong> {{ $product->name }}</p>
    <p><strong>SKU:</strong> {{ $product->sku }}</p>
    <p><strong>Current Quantity:</strong> {{ $inventory->available_quantity }}</p>
    <p><strong>Threshold:</strong> {{ $inventory->low_stock_threshold }}</p>
    <p>Please restock this item as soon as possible.</p>
</body>
</html>

