<?php

namespace Tests\Feature\Http\Controllers\Api\Modules\Rmsapi\RmsapiConnectionController;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('api.modules.rmsapi.connections.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }
}
