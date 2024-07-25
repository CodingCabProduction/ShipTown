<?php

namespace App\Modules\Maintenance\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class FillInventoryIdInProductsPricesTableJob extends UniqueJob
{
    public function handle(): void
    {
        do {
            $recordsAffected = DB::affectingStatement('
                WITH tempTable AS (
                    SELECT
                        products_prices.id as product_price_id,
                        inventory.id as inventory_id
                    FROM
                        products_prices

                    LEFT JOIN inventory
                        ON inventory.product_id = products_prices.product_id
                        AND inventory.warehouse_id = products_prices.warehouse_id

                    WHERE
                        inventory_id IS NULL

                   LIMIT 5000
                )

                UPDATE products_prices

                INNER JOIN tempTable
                   ON tempTable.product_price_id = products_prices.id

               SET products_prices.inventory_id = tempTable.inventory_id
            ');

            usleep(200000); // 200ms
        } while ($recordsAffected > 0);
    }
}
