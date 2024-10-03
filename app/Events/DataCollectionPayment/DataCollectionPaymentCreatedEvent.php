<?php

namespace App\Events\DataCollectionPayment;

use App\Models\DataCollectionPayment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataCollectionPaymentCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DataCollectionPayment $dataCollectionPayment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DataCollectionPayment $dataCollectionPayment)
    {
        $this->dataCollectionPayment = $dataCollectionPayment;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('channel-name');
    }
}
