<?php

namespace Tests\Browser\Routes;

use Tests\DuskTestCase;
use Throwable;

class QuickConnectPageTest extends DuskTestCase
{
    private string $uri = '/quick-connect';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->visit($this->uri);
    }
}
