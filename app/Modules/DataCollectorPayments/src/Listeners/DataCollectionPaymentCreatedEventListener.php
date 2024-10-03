<?php

namespace App\Modules\DataCollectorPayments\src\Listeners;

use App\Events\DataCollectionPayment\DataCollectionPaymentCreatedEvent;
use App\Modules\DataCollector\src\Jobs\RecountTotalsJob;

class DataCollectionPaymentCreatedEventListener
{
    public function handle(DataCollectionPaymentCreatedEvent $event): void
    {
        RecountTotalsJob::dispatch($event->dataCollectionPayment->transaction_id);
    }
}
