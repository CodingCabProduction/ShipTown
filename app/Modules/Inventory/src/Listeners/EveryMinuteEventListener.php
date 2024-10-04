<?php

namespace App\Modules\Inventory\src\Listeners;

use App\Modules\Inventory\src\Jobs\RecalculateInventoryRecordsJob;

class EveryMinuteEventListener
{
    public function handle(): void
    {
        RecalculateInventoryRecordsJob::dispatch();
    }
}
