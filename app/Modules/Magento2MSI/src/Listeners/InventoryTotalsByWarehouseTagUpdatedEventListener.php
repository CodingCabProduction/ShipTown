<?php

namespace App\Modules\Magento2MSI\src\Listeners;

use App\Events\InventoryTotalsByWarehouseTagUpdatedEvent;
use Illuminate\Support\Facades\DB;

class InventoryTotalsByWarehouseTagUpdatedEventListener
{
    public function handle(InventoryTotalsByWarehouseTagUpdatedEvent $event): void
    {
        DB::affectingStatement("
            UPDATE modules_magento2msi_inventory_source_items
            SET sync_required = null
            WHERE inventory_totals_by_warehouse_tag_id IN (
                ".$event->inventoryTotalByWarehouseTags->pluck('id')->implode(',') ."
            )
        ");
    }
}
