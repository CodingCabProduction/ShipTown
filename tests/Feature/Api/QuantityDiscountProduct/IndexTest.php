<?php

namespace Tests\Feature\Api\QuantityDiscountProduct;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscountsProduct;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private string $uri = 'api/quantity-discount-product';

    /** @test */
    public function testIfCallReturnsOk()
    {
        QuantityDiscountsProduct::factory()->count(5)->create();

        $user = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson($this->uri, []);

        ray($response->json());

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id'
                ],
            ],
        ]);
    }

    /** @test */
    public function testUserAccess()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->getJson($this->uri, []);

        $response->assertSuccessful();
    }
}
