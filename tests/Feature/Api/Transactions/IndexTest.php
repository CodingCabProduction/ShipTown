<?php

namespace Tests\Feature\Api\Transactions;

use App\Models\Transaction;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private string $uri = '/api/transactions';

    /** @test */
    public function testIfCallReturnsOk()
    {
        $user = User::factory()->create()->assignRole('user');

        Transaction::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson($this->uri);

        ray($response->json());

        $response->assertSuccessful();

        $this->assertCount(1, $response->json('data'), 'No records returned');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id'
                ],
            ],
        ]);
    }
}
