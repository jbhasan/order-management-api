<?php

namespace Tests\Feature\Api\V1;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->token = auth('api')->login($this->customer);
    }

    public function test_customer_can_create_order(): void
    {
        $product = Product::factory()->create();
        $product->inventory()->create(['quantity' => 100]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/orders', [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                    ],
                ],
                'shipping_address' => '123 Main St',
                'billing_address' => '123 Main St',
                'tax' => 10,
                'shipping_cost' => 5,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'order_number',
                'status',
                'total',
            ]);
    }

    public function test_customer_can_view_own_orders(): void
    {
        Order::factory()->count(3)->create(['customer_id' => $this->customer->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}

