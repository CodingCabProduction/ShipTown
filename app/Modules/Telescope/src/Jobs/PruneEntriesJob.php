<?php

namespace App\Modules\Telescope\src\Jobs;

use App\Abstracts\UniqueJob;
use DB;
use Illuminate\Support\Facades\Log;

class PruneEntriesJob extends UniqueJob
{
    public function handle(): bool
    {
        DB::table('telescope_entries')->where('created_at', '<', now()->subHours(config('telescope.hours')))->delete();

        Log::info('Telescope pruned');

        return true;
    }
}
