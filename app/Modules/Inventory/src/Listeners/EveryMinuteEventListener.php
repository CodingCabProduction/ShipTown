<?php

namespace App\Modules\Inventory\src\Listeners;

use App\Modules\Inventory\src\Jobs\DispatchRecalculateInventoryRecordsJob;

class EveryMinuteEventListener
{
    public function handle(): void
    {
        DispatchRecalculateInventoryRecordsJob::dispatch();
    }
}
