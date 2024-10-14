<?php

namespace Tests\Modules\InventoryMovements\src\Jobs;

use App\Modules\InventoryMovements\src\Jobs\InventoryQuantityCheckJob;
use Tests\TestCase;

class InventoryQuantityCheckJobTest extends TestCase
{
    /** @test */
    public function test_job()
    {
        InventoryQuantityCheckJob::dispatch();
        $this->markTestIncomplete('This test has not been implemented yet');
    }
}
