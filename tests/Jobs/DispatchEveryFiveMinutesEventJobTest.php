<?php

namespace Tests\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class DispatchEveryFiveMinutesEventJobTest extends JobTestAbstract
{
    public function test_job()
   {
        App\Jobs\DispatchEveryFiveMinutesEventJob::dispatchSync();

        $this->assertTrue(true, 'Job did not throw any exceptions');
    }
}
