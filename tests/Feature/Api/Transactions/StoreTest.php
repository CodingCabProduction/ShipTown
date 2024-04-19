<?php

namespace Tests\Feature\Api\Transactions;

use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private string $uri = '/api/transactions';

    /** @test */
    public function testIfCallReturnsOk()
    {
        $user = User::factory()->create()->assignRole('user');

        $response = $this->actingAs($user, 'api')->postJson($this->uri, [
            'raw_data' => []
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'id'
        ]);
    }
}
