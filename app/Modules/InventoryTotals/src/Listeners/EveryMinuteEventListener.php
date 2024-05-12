<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryTotalsByWarehouseTagJob;
use App\Modules\InventoryTotals\src\Jobs\RecalculateInventoryTotalsTableJob;

class EveryMinuteEventListener
{
    public function handle(): void
    {
//        RecalculateInventoryRecordsJob::dispatch();
        RecalculateInventoryTotalsTableJob::dispatch();
        RecalculateInventoryTotalsByWarehouseTagJob::dispatch();
    }
}
