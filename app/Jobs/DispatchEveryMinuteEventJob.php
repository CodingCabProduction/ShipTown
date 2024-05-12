<?php

namespace App\Jobs;

use App\Abstracts\UniqueJob;
use App\Events\EveryMinuteEvent;
use Exception;

class DispatchEveryMinuteEventJob extends UniqueJob
{
    public function handle(): void
    {
        try {
            EveryMinuteEvent::dispatch();
        } catch (Exception $e) {
            report($e);
        }
    }
}
