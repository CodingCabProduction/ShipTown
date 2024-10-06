<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\RepublishWebhooksForSyncRequired;

class EveryTenMinutesEventListener
{
    public function handle(): void
    {
        RepublishWebhooksForSyncRequired::dispatch();
    }
}
