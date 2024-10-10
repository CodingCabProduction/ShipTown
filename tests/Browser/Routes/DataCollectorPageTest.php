<?php

namespace Tests\Browser\Routes;

use Tests\DuskTestCase;
use Throwable;

class DataCollectorPageTest extends DuskTestCase
{
    private string $uri = '/data-collector';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
