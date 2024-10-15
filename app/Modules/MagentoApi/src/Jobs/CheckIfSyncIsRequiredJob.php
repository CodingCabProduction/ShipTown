<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckIfSyncIsRequiredJob extends UniqueJob
{
    public function handle(): void
    {
        do {
            $recordsAffected = DB::affectingStatement('
                WITH tempTable AS (
                        SELECT modules_magento2api_products.id

                        FROM modules_magento2api_products

                        WHERE exists_in_magento = 1
                            AND (
                               (base_price_sync_required IS NULL AND base_prices_fetched_at IS NOT NULL AND magento_price IS NOT NULL)
                               OR (special_price_sync_required IS NULL AND special_prices_fetched_at IS NOT NULL AND magento_sale_price IS NOT NULL)
                            )

                        LIMIT 100
                )

                UPDATE modules_magento2api_products

                INNER JOIN tempTable ON tempTable.id = modules_magento2api_products.id

                LEFT JOIN products_prices
                  ON products_prices.id = modules_magento2api_products.product_price_id

                SET modules_magento2api_products.base_price_sync_required = NOT (modules_magento2api_products.magento_price = products_prices.price),
                    modules_magento2api_products.special_price_sync_required = NOT (
                            IFNULL(modules_magento2api_products.magento_sale_price, 0) = products_prices.sale_price
                        AND date(IFNULL(modules_magento2api_products.magento_sale_price_start_date, "2000-01-01")) = date(products_prices.sale_price_start_date)
                        AND date(IFNULL(modules_magento2api_products.magento_sale_price_end_date, "2000-01-01")) = date(products_prices.sale_price_end_date)
                    ),
                    modules_magento2api_products.updated_at = NOW()
           ');

            usleep(100000); // 0.1 second
            Log::info('Job processing', [
                'job' => self::class,
                'recordsAffected' => $recordsAffected,
            ]);
        } while ($recordsAffected > 0);
    }
}
