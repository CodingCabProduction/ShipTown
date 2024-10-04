<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\CheckIfSyncRequiredJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Jobs\RepublishWebhooksForSyncRequired;
use App\Modules\Rmsapi\src\Jobs\UpdateImportedSalesRecordsJob;

class EveryMinuteEventListener
{
    public function handle()
    {
        CheckIfSyncRequiredJob::dispatch();
        RepublishWebhooksForSyncRequired::dispatch();
        UpdateImportedSalesRecordsJob::dispatch();
        ProcessImportedSalesRecordsJob::dispatch();
    }
}
