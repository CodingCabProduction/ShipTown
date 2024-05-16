<?php

namespace App\Modules\Maintenance\src\Listeners;

use App\Modules\Maintenance\src\Jobs\FillTagNameInTaggableTableJob;

class EveryMinuteEventListener
{
    public function handle(): void
    {
        FillTagNameInTaggableTableJob::dispatch();
    }
}
