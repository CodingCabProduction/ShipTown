<?php

namespace Tests\Feature\Api\Transactions\Transaction;

use App\Models\Transaction;
use App\User;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private string $uri = '/api/transactions/';

    /** @test */
    public function testIfCallReturnsOk()
    {
        $user = User::factory()->create()->assignRole('user');

        $transaction = Transaction::factory()->create();
        $response = $this->actingAs($user, 'api')->putJson($this->uri . $transaction->getKey(), [
            'raw_data' => []
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'id'
            ],
        ]);
    }
}
