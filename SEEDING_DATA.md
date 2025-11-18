# Database Seeding Summary

This document describes the seed data that has been populated in the database.

## Users

### Admin
- **Email**: `jbhasan@gmail.com`
- **Password**: `password123`
- **Role**: Admin

### Vendors (3)
1. **TechStore Vendor**
   - Email: `vendor1@ecom.com`
   - Password: `password123`

2. **FashionHub Vendor**
   - Email: `vendor2@ecom.com`
   - Password: `password123`

3. **HomeDecor Vendor**
   - Email: `vendor3@ecom.com`
   - Password: `password123`

### Customers (10 total)
**Predefined Customers:**
- `jb_hasan@live.com` - Password: `password123`
- `jane.smith@example.com` - Password: `password123`

**Additional**: 10 randomly generated customers

## Products

### Electronics (5 products)
1. **MacBook Pro 16" M3** - $2,499.00
   - Variants: Space Gray, Silver
   - Stock: 25 units

2. **iPhone 15 Pro Max** - $1,199.00
   - Variants: Natural Titanium, Blue Titanium, White Titanium
   - Stock: 50 units

3. **Sony WH-1000XM5 Wireless Headphones** - $399.99
   - Variants: Black, Silver
   - Stock: 75 units

4. **Samsung 55" QLED 4K Smart TV** - $899.99
   - No variants
   - Stock: 30 units

5. **iPad Air 11" M2** - $749.00
   - Variants: Starlight, Space Gray, Blue
   - Stock: 40 units

### Fashion (3 products)
1. **Classic Denim Jacket** - $89.99
   - Variants: Blue/Black in sizes S, M, L
   - Stock: 100 units

2. **Premium Leather Sneakers** - $129.99
   - Variants: White/Black in sizes 8, 9, 10
   - Stock: 150 units

3. **Cotton T-Shirt Pack (3 Pack)** - $29.99
   - Variants: S, M, L
   - Stock: 200 units

### Home & Decor (4 products)
1. **Modern Coffee Table** - $299.99
   - Variants: Black Frame, Silver Frame
   - Stock: 20 units

2. **LED Desk Lamp** - $49.99
   - Variants: White, Black
   - Stock: 60 units

3. **Yoga Mat Premium** - $39.99
   - Variants: Purple, Blue, Pink
   - Stock: 80 units

4. **Ceramic Dinner Set (16 Pieces)** - $79.99
   - Variants: White, Cream
   - Stock: 45 units

### Low Stock Product (for testing alerts)
- **Limited Edition Watch** - $599.99
  - Stock: 5 units (below threshold of 10)

**Total Products**: 13 products with multiple variants

## Orders

**Total Orders**: 30 orders with various statuses

### Order Status Distribution
- **Pending**: ~13% (4 orders)
- **Processing**: ~20% (6 orders)
- **Shipped**: ~13% (4 orders)
- **Delivered**: ~33% (10 orders)
- **Cancelled**: ~7% (2 orders)

### Order Details
- Each order contains 1-4 products
- Orders include realistic addresses, payment methods, and notes
- Orders have proper tax calculations (8%) and shipping costs ($5-$20)
- Order items include product snapshots (name, SKU, prices)

## Inventory

- All products have inventory records
- Product variants have separate inventory records
- Low stock thresholds set appropriately
- One product intentionally set with low stock for testing alerts

## Usage

### To seed the database:
```bash
php artisan db:seed
```

### To reset and reseed:
```bash
php artisan migrate:fresh --seed
```

### To seed individual seeders:
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ProductSeeder
php artisan db:seed --class=OrderSeeder
```

## Test Credentials

### Admin Access
- Email: `admin@ecom.com`
- Password: `password123`
- Access: Full system access

### Vendor Access
- Email: `vendor1@ecom.com` (or vendor2, vendor3)
- Password: `password123`
- Access: Manage own products and orders

### Customer Access
- Email: `john.doe@example.com` (or any customer email)
- Password: `password123`
- Access: View products, create orders, view own orders

## Notes

- All passwords are: `password123`
- Products are distributed across vendors
- Orders are randomly assigned to customers
- Order statuses are weighted (more delivered orders than cancelled)
- Inventory quantities are realistic for testing
- One product has low stock to test alert functionality

