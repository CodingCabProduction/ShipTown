<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\CheckIfSyncRequiredJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Jobs\UpdateImportedSalesRecordsJob;

class EveryMinuteEventListener
{
    public function handle()
    {
        CheckIfSyncRequiredJob::dispatch();
        UpdateImportedSalesRecordsJob::dispatch();
        ProcessImportedSalesRecordsJob::dispatch();
    }
}
