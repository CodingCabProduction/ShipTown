<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\Magento2MSI\src\Api\MagentoApi;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Exception;
use Illuminate\Support\Collection;
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
                    'product_id' => 0,
                    'exists_in_magento' => true,
                    'magento_product_id' => $item['id'],
                    'magento_product_type' => $item['type_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

        MagentoProduct::query()->upsert($map->toArray(), ['connection_id', 'sku'], [
            'magento_product_id',
            'magento_product_type',
            'product_id',
            'exists_in_magento',
            'updated_at',
        ]);

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
