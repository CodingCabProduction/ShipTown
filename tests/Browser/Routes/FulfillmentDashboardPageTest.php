<?php

namespace Tests\Browser\Routes;

use Tests\DuskTestCase;
use Throwable;

class FulfillmentDashboardPageTest extends DuskTestCase
{
    private string $uri = '/fulfillment-dashboard';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
