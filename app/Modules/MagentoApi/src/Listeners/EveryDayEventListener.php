<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Modules\MagentoApi\src\Jobs\RecheckExistsInMagentoJob;

class EveryDayEventListener
{
    public function handle(): void
    {
        RecheckExistsInMagentoJob::dispatch();
    }
}
