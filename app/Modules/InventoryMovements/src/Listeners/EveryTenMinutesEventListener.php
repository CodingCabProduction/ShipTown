<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Modules\InventoryMovements\src\Jobs\InventoryLastMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\InventoryQuantityJob;
use App\Modules\InventoryMovements\src\Jobs\PreviousMovementIdJob;
use App\Modules\InventoryMovements\src\Jobs\QuantityBeforeJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        PreviousMovementIdJob::dispatch();
        QuantityBeforeJob::dispatch();
        InventoryLastMovementIdJob::dispatch();
        InventoryQuantityJob::dispatch();
    }
}