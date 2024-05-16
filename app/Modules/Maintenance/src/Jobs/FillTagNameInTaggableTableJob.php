<?php

namespace App\Modules\Maintenance\src\Jobs;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FillTagNameInTaggableTableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        do {
            $recordsUpdated = DB::affectingStatement('
                UPDATE taggables
                LEFT JOIN tags ON taggables.tag_id = tags.id
                SET taggables.tag_name = tags.name->>"$.en"
                WHERE taggables.tag_name IS NULL

                LIMIT 5000;
            ');

            usleep(100000); // 0.1 second
        } while ($recordsUpdated > 0);
    }
}
