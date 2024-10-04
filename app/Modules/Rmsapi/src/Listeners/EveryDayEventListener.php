<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\CleanupImportTablesJob;

class EveryDayEventListener
{
    public function handle()
    {
        CleanupImportTablesJob::dispatch();
    }
}
