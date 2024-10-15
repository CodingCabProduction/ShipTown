<?php

namespace Tests\Modules\MagentoApi\src\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class RecheckExistsInMagentoJobTest extends JobTestAbstract
{
    public function test_job()
   {
        App\Modules\MagentoApi\src\Jobs\RecheckExistsInMagentoJob::dispatchSync();

        $this->assertTrue(true, 'Job did not throw any exceptions');
    }
}
