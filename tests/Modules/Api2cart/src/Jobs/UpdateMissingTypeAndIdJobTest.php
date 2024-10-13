<?php

namespace Tests\Modules\Api2cart\src\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class UpdateMissingTypeAndIdJobTest extends JobTestAbstract
{
    public function test_job()
    {
        App\Modules\Api2cart\src\Jobs\UpdateMissingTypeAndIdJob::dispatchSync();
    }
}
