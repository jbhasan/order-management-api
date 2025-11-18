<?php

namespace Tests\Feature\Api\V1;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->token = auth('api')->login($this->admin);
    }

    public function test_can_update_product_with_variant_quantities(): void
    {
        // Create product with variants
        $product = Product::factory()->create();
        $variant1 = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Variant 1',
            'sku' => 'VAR-001-1',
            'price' => 99.99,
            'is_active' => true,
        ]);
        $variant2 = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Variant 2',
            'sku' => 'VAR-001-2',
            'price' => 109.99,
            'is_active' => true,
        ]);

        // Create inventory for variants
        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant1->id,
            'quantity' => 50,
            'low_stock_threshold' => 10,
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant2->id,
            'quantity' => 30,
            'low_stock_threshold' => 10,
        ]);

        // Update product with variant quantities
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/v1/products/{$product->id}", [
                'variants' => [
                    [
                        'id' => $variant1->id,
                        'quantity' => 100,
                        'low_stock_threshold' => 15,
                    ],
                    [
                        'id' => $variant2->id,
                        'quantity' => 80,
                    ],
                ],
            ]);

        $response->assertStatus(200);

        // Verify variant inventories are updated
        $inventory1 = Inventory::where('product_id', $product->id)
            ->where('product_variant_id', $variant1->id)
            ->first();

        $inventory2 = Inventory::where('product_id', $product->id)
            ->where('product_variant_id', $variant2->id)
            ->first();

        $this->assertEquals(100, $inventory1->quantity);
        $this->assertEquals(15, $inventory1->low_stock_threshold);
        $this->assertEquals(80, $inventory2->quantity);
    }

    public function test_can_update_product_base_quantity_when_no_variants(): void
    {
        $product = Product::factory()->create();

        Inventory::create([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 50,
            'low_stock_threshold' => 10,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/v1/products/{$product->id}", [
                'quantity' => 200,
            ]);

        $response->assertStatus(200);

        $inventory = Inventory::where('product_id', $product->id)
            ->whereNull('product_variant_id')
            ->first();

        $this->assertEquals(200, $inventory->quantity);
    }
}

