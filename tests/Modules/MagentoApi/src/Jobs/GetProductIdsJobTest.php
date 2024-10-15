<?php

namespace Tests\Modules\MagentoApi\src\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class GetProductIdsJobTest extends JobTestAbstract
{
    public function test_job()
   {
        App\Modules\MagentoApi\src\Jobs\GetProductIdsJob::dispatchSync();

        $this->assertTrue(true, 'Job did not throw any exceptions');
    }
}
