<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckIfSyncIsRequiredJob extends UniqueJob
{
    public function handle(): void
    {
        do {
            $recordsAffected = DB::affectingStatement("
                WITH tempTable AS (
                        SELECT modules_magento2api_products.id

                        FROM modules_magento2api_products

                        WHERE base_price_sync_required IS NULL
                            OR special_price_sync_required IS NOT NULL

                        LIMIT 500
                )

                UPDATE modules_magento2api_products

                INNER JOIN tempTable ON tempTable.id = modules_magento2api_products.id

                LEFT JOIN products_prices
                  ON products_prices.id = modules_magento2api_products.product_price_id

                SET modules_magento2api_products.base_price_sync_required = (modules_magento2api_products.magento_price != products_prices.price),
                    modules_magento2api_products.special_price_sync_required = (
                        modules_magento2api_products.magento_sale_price != products_prices.sale_price
                        AND modules_magento2api_products.magento_sale_price_start_date != products_prices.sale_price_start_date
                        AND modules_magento2api_products.magento_sale_price_end_date != products_prices.sale_price_end_date
                    ),
                    modules_magento2api_products.updated_at = NOW()
           ");

            usleep(100000); // 0.1 second
            Log::info('Job processing', [
                'job' => self::class,
                'recordsAffected' => $recordsAffected,
            ]);
        } while ($recordsAffected > 0);
    }
}