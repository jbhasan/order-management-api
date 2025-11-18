# Low Stock Alert System

## When Alerts Are Sent

Low stock alerts are automatically sent in the following scenarios:

### 1. **When Orders Are Confirmed** ✅

When an order status changes from `pending` to `processing` (order confirmation):

-   Inventory is deducted for each order item
-   After deduction, the system checks if the remaining stock is below the threshold
-   If stock is low, an alert is sent to:
    -   The product vendor
    -   The admin user

**Example Flow:**

```
Order Created → Order Confirmed → Inventory Deducted → Check Stock → Alert Sent (if low)
```

### 2. **When Products Are Created** ✅

When a new product is created with initial inventory:

-   If the initial quantity is below the low stock threshold
-   Alert is sent immediately to vendor and admin

**Example:**

```json
POST /api/v1/products
{
    "name": "New Product",
    "quantity": 5,  // Below threshold of 10
    "low_stock_threshold": 10
}
```

→ Alert sent immediately

### 3. **When Products Are Updated** ✅

When product inventory is updated via the API:

-   If the new quantity falls below the threshold
-   Alert is sent to vendor and admin

**Example:**

```json
PUT /api/v1/products/1
{
    "quantity": 8  // Below threshold
}
```

→ Alert sent

### 4. **When Inventory Is Manually Updated** ✅

When inventory is updated directly via the inventory endpoint:

-   System checks stock level after update
-   Alert is sent if below threshold

**Example:**

```json
PUT /api/v1/inventory/1
{
    "quantity": 7,
    "low_stock_threshold": 10
}
```

→ Alert sent

## Alert Logic

### Conditions for Sending Alert:

1. **Stock is Low**: `available_quantity <= low_stock_threshold`

    - `available_quantity = quantity - reserved_quantity`

2. **No Existing Unresolved Alert**:

    - Checks if there's already an active (unresolved) alert for this product/variant
    - Prevents duplicate alerts

3. **Alert Recipients**:
    - Product vendor (if product has a vendor)
    - Admin user

### Alert Prevention:

-   **Duplicate Prevention**: Only one unresolved alert per product/variant
-   **Alert Resolution**: Alerts are marked as resolved when stock is replenished above threshold

## How It Works

### Job Queue Flow:

```
1. Inventory Change Detected
   ↓
2. CheckLowStockJob dispatched (async)
   ↓
3. Job checks if stock is low
   ↓
4. If low and no existing alert:
   - Creates LowStockAlert record
   - Dispatches SendLowStockAlertEmail job
   ↓
5. Email sent to vendor and admin
```

### Database Records:

-   **LowStockAlert** table stores alert history
-   Tracks: product, variant, current quantity, threshold, resolution status

## Testing Low Stock Alerts

### Method 1: Create Product with Low Stock

```bash
POST /api/v1/products
{
    "name": "Test Product",
    "sku": "TEST-001",
    "price": 99.99,
    "quantity": 5,  # Below default threshold of 10
    "low_stock_threshold": 10
}
```

### Method 2: Update Inventory to Low Stock

```bash
PUT /api/v1/inventory/1
{
    "quantity": 8,
    "low_stock_threshold": 10
}
```

### Method 3: Confirm Order That Reduces Stock Below Threshold

```bash
# 1. Create order with product that has stock = 15, threshold = 10
POST /api/v1/orders
{
    "items": [{"product_id": 1, "quantity": 10}]
}

# 2. Confirm order (reduces stock to 5, below threshold)
POST /api/v1/orders/1/confirm
```

## Queue Configuration

**Important**: Make sure the queue worker is running for alerts to be sent:

```bash
php artisan queue:work
```

Or use supervisor/systemd for production.

## Email Configuration

Ensure your `.env` file has proper mail configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@ecom.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Monitoring Alerts

### View All Low Stock Alerts:

```bash
GET /api/v1/inventory/low-stock
```

### Check Alert History:

Query the `low_stock_alerts` table:

```sql
SELECT * FROM low_stock_alerts
WHERE is_resolved = false;
```

## Alert Email Template

The alert email includes:

-   Product name and SKU
-   Current available quantity
-   Low stock threshold
-   Call to action to restock

Email template: `resources/views/emails/low-stock-alert.blade.php`

## Best Practices

1. **Set Appropriate Thresholds**:

    - High-turnover products: Lower threshold
    - Slow-moving products: Higher threshold

2. **Monitor Queue**:

    - Ensure queue worker is running
    - Monitor failed jobs

3. **Review Alerts Regularly**:

    - Check unresolved alerts
    - Resolve alerts when stock is replenished

4. **Test Email Configuration**:
    - Use Mailtrap or similar for development
    - Verify emails are being sent

## Troubleshooting

### Alerts Not Being Sent?

1. **Check Queue Worker**:

    ```bash
    php artisan queue:work
    ```

2. **Check Failed Jobs**:

    ```bash
    php artisan queue:failed
    ```

3. **Check Logs**:

    ```bash
    tail -f storage/logs/laravel.log
    ```

4. **Verify Email Configuration**:

    - Check `.env` mail settings
    - Test email sending: `php artisan tinker` → `Mail::raw('test', fn($m) => $m->to('test@example.com')->subject('test'));`

5. **Check Alert Conditions**:
    - Verify `available_quantity <= threshold`
    - Check if alert already exists (won't send duplicate)

### Alert Sent Multiple Times?

-   The system prevents duplicate alerts
-   Only one unresolved alert per product/variant
-   Resolve existing alerts before expecting new ones
