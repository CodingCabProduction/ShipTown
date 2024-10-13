<?php

namespace Tests\Modules\Api2cart\src\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class SyncVariantsJobTest extends JobTestAbstract
{
    public function test_job()
    {
        App\Modules\Api2cart\src\Jobs\SyncVariantsJob::dispatchSync();
    }
}
