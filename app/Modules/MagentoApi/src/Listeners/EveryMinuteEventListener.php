<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Modules\MagentoApi\src\Jobs\CheckIfSyncIsRequiredJob;
use App\Modules\MagentoApi\src\Jobs\GetProductIdsJob;

class EveryMinuteEventListener
{
    public function handle(): void
    {
        CheckIfSyncIsRequiredJob::dispatch();
        GetProductIdsJob::dispatch();
    }
}
