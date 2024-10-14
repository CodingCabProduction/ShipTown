<?php

namespace Tests\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class DispatchEveryTenMinutesEventJobTest extends JobTestAbstract
{
    public function test_job()
   {
        App\Jobs\DispatchEveryTenMinutesEventJob::dispatchSync();

        $this->assertTrue(true, 'Job did not throw any exceptions');
    }
}
