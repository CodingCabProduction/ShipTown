<?php

namespace App\Jobs;

use App\Abstracts\UniqueJob;
use App\Events\EveryMinuteEvent;

class DispatchEveryMinuteEventJob extends UniqueJob
{
    public function handle(): void
    {
        EveryMinuteEvent::dispatch();
    }
}
