<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\ModuleController;

use App\Models\Module;
use App\Modules\BaseModuleServiceProvider;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TestModule extends BaseModuleServiceProvider {
    public static string $module_name = 'Test Module';
    public static string $module_description = "Test Description";
}

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private function simulationTest(): TestResponse
    {
        TestModule::enableModule();

        $module = Module::where(['service_provider_class' => TestModule::class])->first();

        return $this->put(route('api.settings.modules.update', $module));
    }

    /** @test */
    public function test_update_call_returns_ok()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function test_update_call_should_be_loggedin()
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }

    public function test_update_call_should_loggedin_as_admin()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->simulationTest();

        $response->assertForbidden();
    }
}