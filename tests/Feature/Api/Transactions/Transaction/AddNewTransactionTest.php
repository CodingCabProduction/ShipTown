<?php

namespace Tests\Feature\Api\Transactions\Transaction;

use Tests\TestCase;
use App\Models\Transaction;
use App\User;

class AddNewTransactionTest extends TestCase
{
    private string $uri = '/api/transactions/';

    /** @test */
    public function testIfCallReturnsOk()
    {
        $user = User::factory()->create()->assignRole('user');

        $transaction = Transaction::factory()->create();

        $rawData = [
            'entries' => [
                [
                    'barcode' => 'ABC123',
                    'quantity' => 2,
                    'cost_price' => 10.00,
                    'full_price' => 20.00,
                    'sold_price' => 15.00,
                    'current_price' => 18.00,
                    'total_cost_price' => 20.00,
                    'total_sold_price' => 30.00,
                    'price_source_id' => 0,
                    'price_source' => 'SALE_PRICE'
                ],
                [
                    'barcode' => 'ABC123',
                    'quantity' => 1,
                    'cost_price' => 10.00,
                    'full_price' => 20.00,
                    'sold_price' => 15.00,
                    'current_price' => 18.00,
                    'total_cost_price' => 10.00,
                    'total_sold_price' => 15.00,
                    'price_source_id' => 0,
                    'price_source' => 'SALE_PRICE'
                ],
                [
                    'barcode' => 'XYZ789',
                    'quantity' => 1,
                    'cost_price' => 5.00,
                    'full_price' => 10.00,
                    'sold_price' => 8.00,
                    'current_price' => 9.00,
                    'total_cost_price' => 5.00,
                    'total_sold_price' => 8.00,
                    'price_source_id' => 0,
                    'price_source' => 'SALE_PRICE'
                ],
            ]
        ];

        $response = $this->actingAs($user, 'api')->putJson($this->uri . $transaction->getKey(), [
            'raw_data' => $rawData
        ]);

        $response->assertSuccessful();

        $updatedTransaction = $response->json('data');

        $this->assertCount(2, $updatedTransaction['raw_data']['entries']);

        $this->assertEquals([
            [
                'barcode' => 'ABC123',
                'quantity' => 3,
                'cost_price' => 10.00,
                'full_price' => 20.00,
                'sold_price' => 15.00,
                'current_price' => 18.00,
                'total_cost_price' => 30.00,
                'total_sold_price' => 45.00,
                'price_source_id' => 0,
                'price_source' => 'SALE_PRICE'
            ],
            [
                'barcode' => 'XYZ789',
                'quantity' => 1,
                'cost_price' => 5.00,
                'full_price' => 10.00,
                'sold_price' => 8.00,
                'current_price' => 9.00,
                'total_cost_price' => 5.00,
                'total_sold_price' => 8.00,
                'price_source_id' => 0,
                'price_source' => 'SALE_PRICE'
            ]
        ], $updatedTransaction['raw_data']['entries']);
    }
}
