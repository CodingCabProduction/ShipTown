<?php

namespace Tests\Browser\Routes\Reports;

use Tests\DuskTestCase;
use Throwable;

class InventoryTransfersPageTest extends DuskTestCase
{
    private string $uri = '/reports/inventory-transfers';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->visit($this->uri);
    }
}
