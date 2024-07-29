<?php

namespace Tests\Feature\Api\QuantityDiscounts;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private string $uri = 'api/quantity-discounts/';

    /** @test */
    public function testIfCallReturnsOk()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('admin', 'api'));

        $response = $this->actingAs($user, 'api')->postJson($this->uri, [
            'name' => 'Test Discount',
            'type' => 'BUY_X_GET_Y_FOR_Z_PRICE',
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $this->assertDatabaseHas('modules_quantity_discounts', ['id' => $response->json('data.id')]);
    }
}
