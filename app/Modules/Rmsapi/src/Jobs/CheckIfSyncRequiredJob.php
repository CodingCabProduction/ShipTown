<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckIfSyncRequiredJob extends UniqueJob
{
    public function handle(): void
    {
        do {
            $recordsAffected = DB::affectingStatement('
                WITH tempTable AS (
                        SELECT modules_rmsapi_products_imports.id

                        FROM modules_rmsapi_products_imports

                        WHERE modules_rmsapi_products_imports.sync_required IS NULL
                            AND modules_rmsapi_products_imports.processed_at IS NOT NULL

                        LIMIT 500
                )

                UPDATE modules_rmsapi_products_imports

                INNER JOIN tempTable ON tempTable.id = modules_rmsapi_products_imports.id

                LEFT JOIN inventory
                  ON inventory.id = modules_rmsapi_products_imports.inventory_id

                SET modules_rmsapi_products_imports.sync_required = (
                        modules_rmsapi_products_imports.quantity_on_hand != inventory.quantity
                        OR modules_rmsapi_products_imports.restock_level != inventory.restock_level
                        OR modules_rmsapi_products_imports.reorder_point != inventory.reorder_point
                    ),
                    modules_rmsapi_products_imports.updated_at = NOW()
           ');

            usleep(100000); // 0.1 second
            Log::info('Rmsapi - Job processing', [
                'job' => self::class,
                'recordsAffected' => $recordsAffected,
            ]);
        } while ($recordsAffected > 0);
    }
}
