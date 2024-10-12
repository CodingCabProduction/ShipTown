<?php

namespace Tests\Modules\Inventory\src\Jobs;

use App\Modules\Inventory\src\Jobs\RecalculateInventoryRecordsJob;
use Tests\TestCase;

class DispatchRecalculateInventoryRecordsJobTest extends TestCase
{
    public function test_basic_functionality()
    {
        RecalculateInventoryRecordsJob::dispatch();

        $this->assertTrue(true, 'We ran the job without errors');
    }
}
