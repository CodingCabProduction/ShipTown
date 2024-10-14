<?php

namespace Tests\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class DispatchEveryHourEventJobsTest extends JobTestAbstract
{
    public function test_job()
   {
        App\Jobs\DispatchEveryHourEventJobs::dispatchSync();

        $this->assertTrue(true, 'Job did not throw any exceptions');
    }
}
