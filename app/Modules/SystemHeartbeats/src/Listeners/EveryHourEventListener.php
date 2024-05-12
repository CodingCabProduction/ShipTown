<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Jobs\DispatchEveryHourEventJobs;
use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class EveryHourEventListener
{
    public function handle()
    {
        Log::debug('heartbeat', ['event' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Hour Event heartbeat missed',
            'expires_at' => now()->addHour()->addMinutes(15),
            'auto_heal_job_class' => DispatchEveryHourEventJobs::class
        ]);
    }
}
