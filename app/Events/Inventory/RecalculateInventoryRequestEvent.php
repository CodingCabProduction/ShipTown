<?php

namespace App\Events\Inventory;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class RecalculateInventoryRequestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Collection $inventoryRecordsIds;

    public Collection $inventory;

    public function __construct($inventoryRecordsIds, $inventory = null)
    {
        $this->inventoryRecordsIds = $inventoryRecordsIds;
        $this->inventory = $inventory;
    }

    public function broadcastOn(): Channel|PrivateChannel
    {
        return new PrivateChannel('channel-name');
    }
}
