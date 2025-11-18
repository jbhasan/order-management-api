<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::with('variants')->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No customers or products found. Please run UserSeeder and ProductSeeder first.');
            return;
        }

        // Create orders with different statuses
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $statusWeights = [
            'pending' => 2,
            'processing' => 3,
            'shipped' => 2,
            'delivered' => 5,
            'cancelled' => 1,
        ];

        // Create 30 orders
        for ($i = 0; $i < 30; $i++) {
            $customer = $customers->random();
            $status = $this->getWeightedRandomStatus($statusWeights);

            // Select random products for order
            $orderProducts = $products->random(rand(1, 4));
            $subtotal = 0;
            $items = [];

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $variant = $product->variants->isNotEmpty() ? $product->variants->random() : null;
                $unitPrice = $variant?->price ?? $product->price;
                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;

                $items[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            $tax = round($subtotal * 0.08, 2); // 8% tax
            $shippingCost = rand(5, 20);
            $total = $subtotal + $tax + $shippingCost;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $customer->id,
                'status' => $status,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_address' => $this->getRandomAddress(),
                'billing_address' => $this->getRandomAddress(),
                'payment_method' => $this->getRandomPaymentMethod(),
                'payment_status' => $status === 'cancelled' ? 'pending' : ($status === 'pending' ? 'pending' : 'paid'),
                'notes' => rand(0, 1) ? $this->getRandomNote() : null,
                'shipped_at' => $status === 'shipped' || $status === 'delivered' ? now()->subDays(rand(1, 10)) : null,
                'delivered_at' => $status === 'delivered' ? now()->subDays(rand(1, 5)) : null,
                'cancelled_at' => $status === 'cancelled' ? now()->subDays(rand(1, 3)) : null,
            ]);

            // Create order items
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_variant_id' => $item['variant']?->id,
                    'product_name' => $item['product']->name,
                    'product_sku' => $item['variant']?->sku ?? $item['product']->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }
        }

        $this->command->info('Created 30 orders with various statuses.');
    }

    private function getWeightedRandomStatus(array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        $currentWeight = 0;

        foreach ($weights as $status => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $status;
            }
        }

        return 'pending';
    }

    private function getRandomAddress(): string
    {
		$streets = ['Road', 'Avenue', 'Lane'];
        $street = rand(100, 9999) . ' ' . $streets[array_rand($streets)];
		$cities = ['Dhaka', 'Chittagong', 'Khulna', 'Rajshahi', 'Barisal', 'Sylhet', 'Mymensingh', 'Rangpur', 'Comilla', 'Jessore'];
		$city = $cities[array_rand($cities)];
		$zip = rand(1000, 9999);

		return "{$street}, {$city}, Bangladesh, {$zip}";
    }

    private function getRandomPaymentMethod(): string
    {
        $methods = ['credit_card', 'cash_on_delivery', 'bank_transfer', 'debit_card'];
        return $methods[array_rand($methods)];
    }

    private function getRandomNote(): string
    {
        $notes = [
            'Please leave at front door',
            'Handle with care',
            'Gift wrapping requested',
            'Contact before delivery',
            'Leave with neighbor if not home',
        ];
        return $notes[array_rand($notes)];
    }
}
