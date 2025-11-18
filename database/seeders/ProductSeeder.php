<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = User::where('role', 'vendor')->get();

        // Electronics Products
        $electronicsProducts = [
            [
                'name' => 'MacBook Pro 16" M3',
                'description' => 'Powerful 16-inch MacBook Pro with M3 chip, 18GB RAM, 512GB SSD. Perfect for professionals and creatives.',
                'sku' => 'MBP-16-M3-512',
                'price' => 2499.00,
                'attributes' => ['brand' => 'Apple', 'category' => 'Laptops', 'processor' => 'M3'],
                'variants' => [
                    ['name' => 'Space Gray', 'sku' => 'MBP-16-M3-512-SG', 'price' => 2499.00, 'attributes' => ['color' => 'Space Gray']],
                    ['name' => 'Silver', 'sku' => 'MBP-16-M3-512-SL', 'price' => 2499.00, 'attributes' => ['color' => 'Silver']],
                ],
                'quantity' => 25,
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Latest iPhone with A17 Pro chip, 256GB storage, Titanium design. Features advanced camera system.',
                'sku' => 'IPH-15-PM-256',
                'price' => 1199.00,
                'attributes' => ['brand' => 'Apple', 'category' => 'Smartphones', 'storage' => '256GB'],
                'variants' => [
                    ['name' => 'Natural Titanium', 'sku' => 'IPH-15-PM-256-NT', 'price' => 1199.00, 'attributes' => ['color' => 'Natural Titanium']],
                    ['name' => 'Blue Titanium', 'sku' => 'IPH-15-PM-256-BT', 'price' => 1199.00, 'attributes' => ['color' => 'Blue Titanium']],
                    ['name' => 'White Titanium', 'sku' => 'IPH-15-PM-256-WT', 'price' => 1199.00, 'attributes' => ['color' => 'White Titanium']],
                ],
                'quantity' => 50,
            ],
            [
                'name' => 'Sony WH-1000XM5 Wireless Headphones',
                'description' => 'Industry-leading noise cancellation with exceptional sound quality. 30-hour battery life.',
                'sku' => 'SNY-WH-1000XM5',
                'price' => 399.99,
                'attributes' => ['brand' => 'Sony', 'category' => 'Audio', 'type' => 'Over-ear'],
                'variants' => [
                    ['name' => 'Black', 'sku' => 'SNY-WH-1000XM5-BK', 'price' => 399.99, 'attributes' => ['color' => 'Black']],
                    ['name' => 'Silver', 'sku' => 'SNY-WH-1000XM5-SL', 'price' => 399.99, 'attributes' => ['color' => 'Silver']],
                ],
                'quantity' => 75,
            ],
            [
                'name' => 'Samsung 55" QLED 4K Smart TV',
                'description' => 'Crystal clear 4K UHD picture with Quantum Dot technology. Smart TV with voice control.',
                'sku' => 'SAM-55-QLED-4K',
                'price' => 899.99,
                'attributes' => ['brand' => 'Samsung', 'category' => 'TVs', 'size' => '55"'],
                'quantity' => 30,
            ],
            [
                'name' => 'iPad Air 11" M2',
                'description' => 'Thin and light iPad with M2 chip. Perfect for work and creativity. 256GB storage.',
                'sku' => 'IPAD-AIR-11-M2-256',
                'price' => 749.00,
                'attributes' => ['brand' => 'Apple', 'category' => 'Tablets', 'storage' => '256GB'],
                'variants' => [
                    ['name' => 'Starlight', 'sku' => 'IPAD-AIR-11-M2-256-ST', 'price' => 749.00, 'attributes' => ['color' => 'Starlight']],
                    ['name' => 'Space Gray', 'sku' => 'IPAD-AIR-11-M2-256-SG', 'price' => 749.00, 'attributes' => ['color' => 'Space Gray']],
                    ['name' => 'Blue', 'sku' => 'IPAD-AIR-11-M2-256-BL', 'price' => 749.00, 'attributes' => ['color' => 'Blue']],
                ],
                'quantity' => 40,
            ],
        ];

        // Fashion Products
        $fashionProducts = [
            [
                'name' => 'Classic Denim Jacket',
                'description' => 'Timeless denim jacket made from premium cotton. Perfect for casual wear.',
                'sku' => 'DEN-JKT-CLASSIC',
                'price' => 89.99,
                'attributes' => ['brand' => 'FashionHub', 'category' => 'Jackets', 'material' => 'Cotton'],
                'variants' => [
                    ['name' => 'Blue - Small', 'sku' => 'DEN-JKT-CLASSIC-BL-S', 'price' => 89.99, 'attributes' => ['color' => 'Blue', 'size' => 'S']],
                    ['name' => 'Blue - Medium', 'sku' => 'DEN-JKT-CLASSIC-BL-M', 'price' => 89.99, 'attributes' => ['color' => 'Blue', 'size' => 'M']],
                    ['name' => 'Blue - Large', 'sku' => 'DEN-JKT-CLASSIC-BL-L', 'price' => 89.99, 'attributes' => ['color' => 'Blue', 'size' => 'L']],
                    ['name' => 'Black - Small', 'sku' => 'DEN-JKT-CLASSIC-BK-S', 'price' => 89.99, 'attributes' => ['color' => 'Black', 'size' => 'S']],
                    ['name' => 'Black - Medium', 'sku' => 'DEN-JKT-CLASSIC-BK-M', 'price' => 89.99, 'attributes' => ['color' => 'Black', 'size' => 'M']],
                ],
                'quantity' => 100,
            ],
            [
                'name' => 'Premium Leather Sneakers',
                'description' => 'Comfortable leather sneakers with cushioned insoles. Perfect for everyday wear.',
                'sku' => 'SNK-LEATHER-PREM',
                'price' => 129.99,
                'attributes' => ['brand' => 'FashionHub', 'category' => 'Footwear', 'material' => 'Leather'],
                'variants' => [
                    ['name' => 'White - Size 8', 'sku' => 'SNK-LEATHER-PREM-WH-8', 'price' => 129.99, 'attributes' => ['color' => 'White', 'size' => '8']],
                    ['name' => 'White - Size 9', 'sku' => 'SNK-LEATHER-PREM-WH-9', 'price' => 129.99, 'attributes' => ['color' => 'White', 'size' => '9']],
                    ['name' => 'White - Size 10', 'sku' => 'SNK-LEATHER-PREM-WH-10', 'price' => 129.99, 'attributes' => ['color' => 'White', 'size' => '10']],
                    ['name' => 'Black - Size 8', 'sku' => 'SNK-LEATHER-PREM-BK-8', 'price' => 129.99, 'attributes' => ['color' => 'Black', 'size' => '8']],
                    ['name' => 'Black - Size 9', 'sku' => 'SNK-LEATHER-PREM-BK-9', 'price' => 129.99, 'attributes' => ['color' => 'Black', 'size' => '9']],
                ],
                'quantity' => 150,
            ],
            [
                'name' => 'Cotton T-Shirt Pack (3 Pack)',
                'description' => 'Soft cotton t-shirts in assorted colors. Pack of 3. Machine washable.',
                'sku' => 'TSH-COTTON-3PK',
                'price' => 29.99,
                'attributes' => ['brand' => 'FashionHub', 'category' => 'T-Shirts', 'material' => 'Cotton'],
                'variants' => [
                    ['name' => 'Small', 'sku' => 'TSH-COTTON-3PK-S', 'price' => 29.99, 'attributes' => ['size' => 'S']],
                    ['name' => 'Medium', 'sku' => 'TSH-COTTON-3PK-M', 'price' => 29.99, 'attributes' => ['size' => 'M']],
                    ['name' => 'Large', 'sku' => 'TSH-COTTON-3PK-L', 'price' => 29.99, 'attributes' => ['size' => 'L']],
                ],
                'quantity' => 200,
            ],
        ];

        // Home & Decor Products
        $homeProducts = [
            [
                'name' => 'Modern Coffee Table',
                'description' => 'Sleek modern coffee table with glass top and metal legs. Perfect for contemporary living rooms.',
                'sku' => 'TBL-COFFEE-MODERN',
                'price' => 299.99,
                'attributes' => ['brand' => 'HomeDecor', 'category' => 'Furniture', 'material' => 'Glass & Metal'],
                'variants' => [
                    ['name' => 'Black Frame', 'sku' => 'TBL-COFFEE-MODERN-BK', 'price' => 299.99, 'attributes' => ['color' => 'Black']],
                    ['name' => 'Silver Frame', 'sku' => 'TBL-COFFEE-MODERN-SL', 'price' => 299.99, 'attributes' => ['color' => 'Silver']],
                ],
                'quantity' => 20,
            ],
            [
                'name' => 'LED Desk Lamp',
                'description' => 'Adjustable LED desk lamp with touch control and USB charging port. Eye-friendly lighting.',
                'sku' => 'LMP-LED-DESK',
                'price' => 49.99,
                'attributes' => ['brand' => 'HomeDecor', 'category' => 'Lighting', 'type' => 'LED'],
                'variants' => [
                    ['name' => 'White', 'sku' => 'LMP-LED-DESK-WH', 'price' => 49.99, 'attributes' => ['color' => 'White']],
                    ['name' => 'Black', 'sku' => 'LMP-LED-DESK-BK', 'price' => 49.99, 'attributes' => ['color' => 'Black']],
                ],
                'quantity' => 60,
            ],
            [
                'name' => 'Yoga Mat Premium',
                'description' => 'Non-slip yoga mat with carrying strap. Extra thick for comfort and support.',
                'sku' => 'YGA-MAT-PREM',
                'price' => 39.99,
                'attributes' => ['brand' => 'HomeDecor', 'category' => 'Fitness', 'thickness' => '6mm'],
                'variants' => [
                    ['name' => 'Purple', 'sku' => 'YGA-MAT-PREM-PU', 'price' => 39.99, 'attributes' => ['color' => 'Purple']],
                    ['name' => 'Blue', 'sku' => 'YGA-MAT-PREM-BL', 'price' => 39.99, 'attributes' => ['color' => 'Blue']],
                    ['name' => 'Pink', 'sku' => 'YGA-MAT-PREM-PI', 'price' => 39.99, 'attributes' => ['color' => 'Pink']],
                ],
                'quantity' => 80,
            ],
            [
                'name' => 'Ceramic Dinner Set (16 Pieces)',
                'description' => 'Elegant ceramic dinner set for 4 people. Dishwasher and microwave safe.',
                'sku' => 'DIN-SET-CERAMIC-16',
                'price' => 79.99,
                'attributes' => ['brand' => 'HomeDecor', 'category' => 'Dinnerware', 'pieces' => '16'],
                'variants' => [
                    ['name' => 'White', 'sku' => 'DIN-SET-CERAMIC-16-WH', 'price' => 79.99, 'attributes' => ['color' => 'White']],
                    ['name' => 'Cream', 'sku' => 'DIN-SET-CERAMIC-16-CR', 'price' => 79.99, 'attributes' => ['color' => 'Cream']],
                ],
                'quantity' => 45,
            ],
        ];

        // Assign products to vendors
        $vendorIndex = 0;
        $allProducts = array_merge($electronicsProducts, $fashionProducts, $homeProducts);

        foreach ($allProducts as $productData) {
            $vendor = $vendors[$vendorIndex % $vendors->count()];
            $variants = $productData['variants'] ?? [];
            $quantity = $productData['quantity'] ?? 50;
            unset($productData['variants']);
            unset($productData['quantity']);

            $product = Product::create([
                ...$productData,
                'vendor_id' => $vendor->id,
                'is_active' => true,
            ]);

            // Create inventory for product
            Inventory::create([
                'product_id' => $product->id,
                'product_variant_id' => null,
                'quantity' => $quantity,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 10,
                'location' => 'Warehouse A',
            ]);

            // Create variants
            foreach ($variants as $variantData) {
                $variantQuantity = $variantData['quantity'] ?? 25;
                unset($variantData['quantity']);

                $variant = ProductVariant::create([
                    ...$variantData,
                    'product_id' => $product->id,
                    'is_active' => true,
                ]);

                // Create inventory for variant
                Inventory::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $variantQuantity,
                    'reserved_quantity' => 0,
                    'low_stock_threshold' => 5,
                    'location' => 'Warehouse A',
                ]);
            }

            $vendorIndex++;
        }

        // Create some products with low stock for testing alerts
        $lowStockProduct = Product::create([
            'vendor_id' => $vendors->first()->id,
            'name' => 'Limited Edition Watch',
            'description' => 'Limited edition watch with low stock',
            'sku' => 'WCH-LTD-ED',
            'price' => 599.99,
            'is_active' => true,
            'attributes' => ['brand' => 'TechStore', 'category' => 'Watches'],
        ]);

        Inventory::create([
            'product_id' => $lowStockProduct->id,
            'product_variant_id' => null,
            'quantity' => 5, // Low stock
            'reserved_quantity' => 0,
            'low_stock_threshold' => 10,
            'location' => 'Warehouse A',
        ]);
    }
}
