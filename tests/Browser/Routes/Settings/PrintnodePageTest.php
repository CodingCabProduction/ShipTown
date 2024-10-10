<?php

namespace Tests\Browser\Routes\Settings;

use Tests\DuskTestCase;
use Throwable;

class PrintnodePageTest extends DuskTestCase
{
    private string $uri = '/settings/printnode';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
