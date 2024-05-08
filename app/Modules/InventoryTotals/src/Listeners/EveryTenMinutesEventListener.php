<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\EnsureInventoryTotalsRecordsExistJob;

class EveryTenMinutesEventListener
{
    public function handle(): void
    {
        EnsureInventoryTotalsRecordsExistJob::dispatch();
    }
}
