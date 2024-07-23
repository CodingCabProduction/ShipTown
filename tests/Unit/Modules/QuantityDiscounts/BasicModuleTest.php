<?php

namespace Tests\Unit\Modules\QuantityDiscounts;

use App\Modules\QuantityDiscounts\src\QuantityDiscountsServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        QuantityDiscountsServiceProvider::enableModule();
    }

    /** @test */
    public function testBasicFunctionality()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
