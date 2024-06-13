<?php

namespace App\Modules\InventoryQuantityIncoming\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Models\DataCollectionTransferIn;
use App\Modules\InventoryQuantityIncoming\src\Jobs\RecalculateInventoryQuantityIncomingJob;

class DataCollectionRecordCreatedEventListener
{
    public function handle(DataCollectionRecordCreatedEvent $event): void
    {
        $record = $event->dataCollectionRecord;

        if ($record->dataCollection->type === DataCollectionTransferIn::class) {
            RecalculateInventoryQuantityIncomingJob::dispatchSync(
                $record->product_id,
                $record->dataCollection->warehouse_id
            );
        }
    }
}
