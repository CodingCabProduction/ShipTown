<?php

namespace Tests\Feature\Api\Order\Shipments;

use App\Models\Order;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson(route('orders.shipments.store'), [
            'order_id' => $order['id'],
            'shipping_number' => '123',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [],
        ]);
    }
}
