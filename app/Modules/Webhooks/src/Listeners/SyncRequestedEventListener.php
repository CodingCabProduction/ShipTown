<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Modules\Webhooks\src\Services\WebhooksService;

class SyncRequestedEventListener
{
    public function handle()
    {
        WebhooksService::dispatchJobs();
    }
}
