<?php

namespace Tests\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class DispatchEveryWeekEventJobTest extends JobTestAbstract
{
    public function test_job()
   {
        App\Jobs\DispatchEveryWeekEventJob::dispatchSync();

        $this->assertTrue(true, 'Job did not throw any exceptions');
    }
}
