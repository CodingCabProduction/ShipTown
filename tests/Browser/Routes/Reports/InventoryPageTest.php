<?php

namespace Tests\Browser\Routes\Reports;

use Tests\DuskTestCase;
use Throwable;

class InventoryPageTest extends DuskTestCase
{
    private string $uri = '/reports/inventory';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->visit($this->uri);
    }
}
