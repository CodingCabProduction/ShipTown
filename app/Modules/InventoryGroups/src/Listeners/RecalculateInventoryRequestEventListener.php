<?php

namespace App\Modules\InventoryGroups\src\Listeners;

use App\Events\Inventory\RecalculateInventoryRequestEvent;
use Illuminate\Support\Facades\DB;

class RecalculateInventoryRequestEventListener
{
    public function handle(RecalculateInventoryRequestEvent $event): void
    {
        DB::affectingStatement("
            UPDATE inventory_groups
            SET recount_required = 1
            WHERE product_id IN (SELECT DISTINCT product_id FROM inventory WHERE id IN (
                ".$event->inventoryRecordsIds->implode(',') ."
            ))
        ");
    }
}
