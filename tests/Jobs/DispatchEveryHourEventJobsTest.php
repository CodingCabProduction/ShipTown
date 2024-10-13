<?php

namespace Tests\Jobs;

use App\Abstracts\JobTestAbstract;

class DispatchEveryHourEventJobsTest extends JobTestAbstract
{
    public function test_job()
    {
        $job = self::createJob();

        $job::dispatch();
    }
}
