<?php

namespace Tests\Browser\Routes\Settings\Modules;

use Tests\DuskTestCase;
use Throwable;

class DpdUkPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/dpd-uk';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
