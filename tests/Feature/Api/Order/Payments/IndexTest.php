<?php

namespace Tests\Feature\Api\Order\Payments;

use App\Models\OrderPayment;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function testIfCallReturnsOk()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('payments.index'));

        OrderPayment::factory()->count(5)->create();

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'order_id',
                    'amount',
                    'paid_at',
                    'additional_fields',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
