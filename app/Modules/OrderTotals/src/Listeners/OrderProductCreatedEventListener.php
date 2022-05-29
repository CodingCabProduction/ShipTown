<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Modules\OrderTotals\src\Jobs\UpdateOrderTotalsJob;

class OrderProductCreatedEventListener
{
    public function handle(OrderProductCreatedEvent $event)
    {
        UpdateOrderTotalsJob::dispatchNow($event->orderProduct->order_id);
    }
}