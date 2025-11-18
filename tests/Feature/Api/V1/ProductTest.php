<?php

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->token = auth('api')->login($this->admin);
    }

    public function test_admin_can_create_product(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/products', [
                'name' => 'Test Product',
                'sku' => 'TEST-001',
                'price' => 99.99,
                'description' => 'Test description',
                'quantity' => 100,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'sku',
                'price',
            ]);
    }

    public function test_can_list_products(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
            ]);
    }

    public function test_can_show_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'name' => $product->name,
            ]);
    }
}

