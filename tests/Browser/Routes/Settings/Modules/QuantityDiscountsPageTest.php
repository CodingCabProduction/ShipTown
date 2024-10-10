<?php

namespace Tests\Browser\Routes\Settings\Modules;

use Tests\DuskTestCase;
use Throwable;

class QuantityDiscountsPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/quantity-discounts';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicAdminAccessTest($this->uri, true);
    }
}
