<?php

namespace Tests\Browser\Routes\Settings;

use Tests\DuskTestCase;
use Throwable;

class WarehousesPageTest extends DuskTestCase
{
    private string $uri = '/settings/warehouses';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
