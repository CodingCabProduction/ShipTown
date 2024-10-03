<?php

namespace Tests\Feature\Api\DataCollectorPayments\DataCollectorPayment;

use App\Models\DataCollectionPayment;
use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private string $uri = 'api/data-collector-payments/';

    /** @test */
    public function testIfCallReturnsOk()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('admin', 'api'));

        /** @var DataCollectionPayment $paymentToUpdate */
        $paymentToUpdate = DataCollectionPayment::factory()->create();

        $response = $this->actingAs($user, 'api')->putJson($this->uri . $paymentToUpdate->id, [
            'amount' => 100.99,
        ]);

        ray($response->json());

        $response->assertOk();

        $this->assertDatabaseHas(
            'data_collection_payments',
            [
                'id' => $paymentToUpdate->id,
                'amount' => 100.99,
            ]
        );
    }
}
