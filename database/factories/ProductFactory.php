<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => null,
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'sku' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{4}'),
            'price' => fake()->randomFloat(2, 10, 1000),
            'is_active' => true,
            'attributes' => null,
            'image_url' => null,
        ];
    }
}
