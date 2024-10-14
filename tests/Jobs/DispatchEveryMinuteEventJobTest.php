<?php

namespace Tests\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class DispatchEveryMinuteEventJobTest extends JobTestAbstract
{
    public function test_job()
   {
        App\Jobs\DispatchEveryMinuteEventJob::dispatchSync();

        $this->assertTrue(true, 'Job did not throw any exceptions');
    }
}
