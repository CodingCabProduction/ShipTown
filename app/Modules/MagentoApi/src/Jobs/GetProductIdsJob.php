<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Helpers\TemporaryTable;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetProductIdsJob extends UniqueJob
{
    public function handle(): void
    {
        MagentoConnection::query()
            ->get()
            ->each(function (MagentoConnection $connection) {
                MagentoProduct::query()
                    ->where('connection_id', $connection->getKey())
                    ->whereNull('exists_in_magento')
                    ->with('product')
                    ->chunkById(50, function (Collection $products) use ($connection) {
                        try {
                            usleep(100000); // Sleep for 0.1 seconds to avoid rate limiting

                            return $this->getProductIds($connection, $products);
                        } catch (Exception $exception) {
                            report($exception);
                            throw $exception;
                        }
                    });
            });
    }

    protected function getProductIds(MagentoConnection $connection, Collection $products): bool
    {
        $response = MagentoApi::getProducts(
            $connection->base_url,
            $connection->api_access_token,
            $products->pluck('product.sku')->toArray()
        );

        if ($response->failed()) {
            Log::error('Magento2 Price Sync - Failed to fetch product IDs', [
                'connection_id' => $connection->getKey(),
                'response' => $response->json(),
            ]);

            return false;
        }

        $map = collect($response->json('items'))
            ->map(function ($item) use ($connection) {
                return [
                    'connection_id' => $connection->getKey(),
                    'sku' => $item['sku'],
                    'exists_in_magento' => true,
                    'magento_product_id' => $item['id'],
                    'magento_product_type' => $item['type_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

        TemporaryTable::fromArray('tempTable_MagentoProductIds', $map->toArray(), function ($table) {
            $table->temporary();
            $table->unsignedBigInteger('connection_id')->index();
            $table->string('sku')->index();
            $table->boolean('exists_in_magento');
            $table->unsignedBigInteger('magento_product_id');
            $table->string('magento_product_type');
            $table->timestamps();
        });

        DB::statement('
            UPDATE modules_magento2api_products
            INNER JOIN tempTable_MagentoProductIds
                ON tempTable_MagentoProductIds.sku = modules_magento2api_products.sku
                AND tempTable_MagentoProductIds.connection_id = modules_magento2api_products.connection_id
            SET modules_magento2api_products.exists_in_magento = tempTable_MagentoProductIds.exists_in_magento,
                modules_magento2api_products.magento_product_id = tempTable_MagentoProductIds.magento_product_id,
                modules_magento2api_products.magento_product_type = tempTable_MagentoProductIds.magento_product_type,
                modules_magento2api_products.updated_at = tempTable_MagentoProductIds.updated_at
        ');

        MagentoProduct::query()
            ->whereIn('id', $products->pluck('id'))
            ->whereNull('exists_in_magento')
            ->update([
                'exists_in_magento' => false,
                'updated_at' => now(),
            ]);

        Log::info('Magento2 Price - Fetched ProductIDs', [
            'connection' => $connection->getKey(),
            'count' => $map->count(),
        ]);

        return true;
    }
}
