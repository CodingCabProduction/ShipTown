<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Events\Inventory\RecalculateInventoryRequestEvent;
use Illuminate\Support\Facades\DB;

class RecalculateInventoryRequestEventListener
{
    public function handle(RecalculateInventoryRequestEvent $event): void
    {
        DB::affectingStatement("
            UPDATE inventory_totals
            SET inventory_totals.recount_required = 1
            WHERE inventory_totals.product_id IN (SELECT DISTINCT product_id FROM inventory WHERE id IN (
                ".$event->inventoryRecordsIds->implode(',') ."
            ))
        ");

        DB::affectingStatement("
            UPDATE inventory_totals_by_warehouse_tag
            SET inventory_totals_by_warehouse_tag.recalc_required = 1
            WHERE inventory_totals_by_warehouse_tag.product_id IN (SELECT DISTINCT product_id FROM inventory WHERE id IN (
                ".$event->inventoryRecordsIds->implode(',') ."
            ))
        ");
    }
}
