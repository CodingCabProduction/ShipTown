<?php

namespace Tests\Modules\Telescope\src\Jobs;

use App\Modules\Telescope\src\Jobs\PruneEntriesJob;
use Tests\TestCase;

class PruneEntriesJobTest extends TestCase
{
    /** @test */
    public function test_job()
    {
        PruneEntriesJob::dispatchSync();

        $this->assertTrue(true,'Job ran successfully');
    }
}
