<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Jobs\DispatchEveryTenMinutesEventJob;
use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        Log::debug('heartbeat', ['code' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Ten Minutes heartbeat missed',
            'expires_at' => now()->addMinutes(15),
            'auto_heal_job_class' => DispatchEveryTenMinutesEventJob::class
        ]);
    }
}
