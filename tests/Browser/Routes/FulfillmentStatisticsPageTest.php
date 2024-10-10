<?php

namespace Tests\Browser\Routes;

use Tests\DuskTestCase;
use Throwable;

class FulfillmentStatisticsPageTest extends DuskTestCase
{
    private string $uri = '/fulfillment-statistics';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->visit($this->uri);
    }
}
