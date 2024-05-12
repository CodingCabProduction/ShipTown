<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Jobs\DispatchEveryDayEventJob;
use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class EveryDayEventListener
{
    public function handle()
    {
        Log::debug('heartbeat', ['event' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Day heartbeat missed',
            'expires_at' => now()->addDays()->addHour(),
            'auto_heal_job_class' => DispatchEveryDayEventJob::class
        ]);
    }
}
