<?php

namespace Tests\Feature\Api\DataCollectorPaymentTypes;

use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Faker\Factory as Faker;

class StoreTest extends TestCase
{
    private string $uri = 'api/data-collector-payment-types/';

    /** @test */
    public function testIfCallReturnsOk()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('admin', 'api'));

        $faker = Faker::create();

        $response = $this->actingAs($user, 'api')->postJson($this->uri, [
            'name' => $faker->word,
            'code' => strtoupper($faker->unique()->word)
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $this->assertDatabaseHas('payment_types', ['id' => $response->json('data.id')]);
    }
}
