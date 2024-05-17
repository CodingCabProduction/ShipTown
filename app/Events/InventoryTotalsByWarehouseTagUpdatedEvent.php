<?php

namespace App\Events;

use App\Modules\InventoryTotals\src\Models\InventoryTotalByWarehouseTag;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryTotalsByWarehouseTagUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public InventoryTotalByWarehouseTag $inventoryTotalByWarehouseTag;

    public function __construct(InventoryTotalByWarehouseTag $inventoryTotalByWarehouseTag)
    {
        $this->inventoryTotalByWarehouseTag = $inventoryTotalByWarehouseTag;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
