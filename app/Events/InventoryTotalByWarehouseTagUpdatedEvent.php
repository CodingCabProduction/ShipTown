<?php

namespace App\Events;

use App\Modules\InventoryTotals\src\Models\InventoryTotalByWarehouseTag;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryTotalByWarehouseTagUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public InventoryTotalByWarehouseTag $inventoryTotalByWarehouseTag)
    {
        //
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
