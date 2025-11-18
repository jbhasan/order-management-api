<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->words(2, true),
            'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{4}-[A-Z]'),
            'price' => fake()->randomFloat(2, 10, 500),
            'attributes' => ['size' => fake()->randomElement(['S', 'M', 'L']), 'color' => fake()->colorName()],
            'is_active' => true,
        ];
    }
}
