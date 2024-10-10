<?php

namespace Tests\Browser\Routes;

use Tests\DuskTestCase;
use Throwable;

class PicklistPageTest extends DuskTestCase
{
    private string $uri = '/picklist';

    /**
     * @throws Throwable
     */
    public function testBasics(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
