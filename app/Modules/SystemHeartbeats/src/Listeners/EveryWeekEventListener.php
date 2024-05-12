<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Jobs\DispatchEveryWeekEventJob;
use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class EveryWeekEventListener
{
    public function handle()
    {
        Log::debug('heartbeat', ['code' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Week heartbeat missed',
            'expires_at' => now()->addWeek()->addHour(),
            'auto_heal_job_class' => DispatchEveryWeekEventJob::class
        ]);
    }
}
