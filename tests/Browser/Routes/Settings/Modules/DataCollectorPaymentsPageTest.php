<?php

namespace Tests\Browser\Routes\Settings\Modules;

use Tests\DuskTestCase;
use Throwable;

class DataCollectorPaymentsPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/data-collector-payments';

    /**
     * @throws Throwable
     */
    public function testPage()
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
