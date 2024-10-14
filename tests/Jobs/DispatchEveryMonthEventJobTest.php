<?php

namespace Tests\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class DispatchEveryMonthEventJobTest extends JobTestAbstract
{
    public function test_job()
   {
        App\Jobs\DispatchEveryMonthEventJob::dispatchSync();

        $this->assertTrue(true, 'Job did not throw any exceptions');
    }
}
