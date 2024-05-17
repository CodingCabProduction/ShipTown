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
            SET
                modules_magento2msi_inventory_source_items.sync_required = null,
                modules_magento2msi_inventory_source_items.inventory_source_items_fetched_at = null
            WHERE inventory_totals_by_warehouse_tag_id = ?
        ", [$event->inventoryTotalByWarehouseTag->id]);
    }
}
