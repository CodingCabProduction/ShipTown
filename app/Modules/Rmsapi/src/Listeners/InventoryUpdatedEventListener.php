<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use Illuminate\Support\Facades\DB;

class InventoryUpdatedEventListener
{
    public function handle(InventoryUpdatedEvent $event): void
    {
        DB::table('modules_rmsapi_products_imports')
            ->where('inventory_id', $event->inventory->getKey())
            ->update(['sync_required' => null]);
    }
}
