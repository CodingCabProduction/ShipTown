<?php

namespace Tests\Browser\Routes\Settings\Modules;

use Tests\DuskTestCase;
use Throwable;

class StocktakeSuggestionsPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/stocktake-suggestions';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
