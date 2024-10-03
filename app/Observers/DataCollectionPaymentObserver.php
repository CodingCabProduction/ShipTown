<?php

namespace App\Observers;

use App\Events\DataCollectionPayment\DataCollectionPaymentCreatedEvent;
use App\Models\DataCollectionPayment;

class DataCollectionPaymentObserver
{
    public function created(DataCollectionPayment $dataCollectionPayment): void
    {
        DataCollectionPaymentCreatedEvent::dispatch($dataCollectionPayment);
    }
}
