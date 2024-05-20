<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InventoryTotalsByWarehouseTagUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Collection $inventoryTotalByWarehouseTags;

    public function __construct(Collection $inventoryTotalsByWarehouseTag)
    {
        $this->inventoryTotalByWarehouseTags = $inventoryTotalsByWarehouseTag;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
