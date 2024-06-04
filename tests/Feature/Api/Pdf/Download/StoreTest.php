<?php

namespace Tests\Feature\Api\Pdf\Download;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_returns_an_ok_response()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('api/pdf/download', [
            'data' => [
                'labels'  => ['label1', 'label2'],
            ],
            'template'  => '6x4in-1-per-page',
        ]);

        $response->assertOk();
    }
}
