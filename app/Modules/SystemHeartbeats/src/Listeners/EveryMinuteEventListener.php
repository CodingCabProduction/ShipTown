<?php

namespace App\Modules\SystemHeartbeats\src\Listeners;

use App\Models\Heartbeat;
use Illuminate\Support\Facades\Log;

class EveryMinuteEventListener
{
    public function handle()
    {
        Log::debug('heartbeat', ['code' => self::class]);

        Heartbeat::query()->updateOrCreate([
            'code' => self::class,
        ], [
            'error_message' => 'Every Minute heartbeat missed',
            'expires_at' => now()->addMinutes(20)
        ]);
    }
}
