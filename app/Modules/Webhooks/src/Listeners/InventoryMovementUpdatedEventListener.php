<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\InventoryMovement\InventoryMovementUpdatedEvent;
use App\Models\InventoryMovement;
use App\Modules\Webhooks\src\Models\PendingWebhook;

class InventoryMovementUpdatedEventListener
{
    public function handle(InventoryMovementUpdatedEvent $event): void
    {
        PendingWebhook::query()->firstOrCreate([
            'model_class' => InventoryMovement::class,
            'model_id' => $event->inventoryMovement->getKey(),
            'reserved_at' => null,
            'published_at' => null,
        ]);
    }
}
