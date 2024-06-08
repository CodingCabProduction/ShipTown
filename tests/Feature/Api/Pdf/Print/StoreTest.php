<?php

namespace Tests\Feature\Api\Pdf\Print;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function store_returns_user_printer_id_missing_error()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('api/pdf/print', [
            'data' => [
                'labels'  => ['label1', 'label2'],
            ],
            'template'  => 'shelf-labels/6x4in-1-per-page',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function store_returns_an_ok_response()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('api/pdf/print', [
            'data' => [
                'labels'  => ['label1', 'label2'],
            ],
            'template'  => 'shelf-labels/6x4in-1-per-page',
            'printer_id' => 1,
        ]);

        $this->assertDatabaseCount('modules_printnode_print_jobs', 1);

        $response->assertSuccessful();
    }
}
