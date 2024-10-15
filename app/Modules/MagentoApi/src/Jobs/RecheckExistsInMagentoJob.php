<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecheckExistsInMagentoJob extends UniqueJob
{
    public function handle(): void
    {
        do {
            $recordsAffected = DB::affectingStatement('
                UPDATE modules_magento2api_products

                SET `exists_in_magento` = null

                WHERE `exists_in_magento` = 0
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsAffected' => $recordsAffected,
            ]);

            usleep(100000); // 0.1 second
        } while ($recordsAffected > 0);
    }
}
