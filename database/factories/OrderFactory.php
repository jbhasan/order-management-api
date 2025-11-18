<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => \App\Models\Order::generateOrderNumber(),
            'customer_id' => \App\Models\User::factory(),
            'status' => 'pending',
            'subtotal' => fake()->randomFloat(2, 50, 500),
            'tax' => fake()->randomFloat(2, 5, 50),
            'shipping_cost' => fake()->randomFloat(2, 5, 20),
            'total' => fake()->randomFloat(2, 60, 570),
            'shipping_address' => fake()->address(),
            'billing_address' => fake()->address(),
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'payment_status' => 'pending',
            'notes' => null,
        ];
    }
}
