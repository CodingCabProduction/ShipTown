<?php

namespace Tests\Browser\Routes\Settings;

use Tests\DuskTestCase;
use Throwable;

class NavigationMenuPageTest extends DuskTestCase
{
    private string $uri = '/settings/navigation-menu';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
