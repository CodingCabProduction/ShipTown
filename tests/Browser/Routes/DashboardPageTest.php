<?php

namespace Tests\Browser\Routes;

use App\Models\Configuration;
use Tests\DuskTestCase;
use Throwable;

class DashboardPageTest extends DuskTestCase
{
    private string $uri = '/dashboard';

    /**
     * @throws Throwable
     */
    public function testBasicScenario(): void
    {
        Configuration::query()->update(['ecommerce_connected' => true]);

        $this->visit($this->uri)
            ->assertSee('Orders - Packed')
            ->assertSee('Orders - Active')
            ->assertSee('Active Orders By Age');
    }
}
