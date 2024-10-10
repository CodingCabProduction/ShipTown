<?php

namespace Tests\Browser\Routes\Settings;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class OrderStatusesPageTest extends DuskTestCase
{
    private string $uri = '/settings/order-statuses';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
