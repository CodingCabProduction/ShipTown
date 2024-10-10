<?php

namespace Tests\Modules\DataCollectorPayments;

use App\Modules\DataCollectorPayments\src\DataCollectorPaymentsServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DataCollectorPaymentsServiceProvider::enableModule();
    }

    /** @test */
    public function testBasicFunctionality()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
