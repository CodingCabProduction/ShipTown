<?php

namespace Database\Seeders\Demo\DataCollections;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class TransferToCorkBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sourceWarehouse = Warehouse::query()->firstOrCreate(['code' => 'DUB'], ['name' => 'Dublin']);

        $dataCollection = DataCollection::factory()
            ->create([
                'warehouse_id' => $sourceWarehouse->getKey(),
                'name' => 'stock for customer order needed in cork',
                'type' => null,
                'created_at' => now()->subHours(rand(24, 48)),
            ]);

        // not scanned yet
        DataCollectionRecord::factory()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => Product::findBySKU('45')->getKey(),
            'quantity_requested' => 1,
            'quantity_scanned' => 0,
        ]);

        DataCollectionRecord::factory()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => Product::findBySKU('46')->getKey(),
            'quantity_requested' => 5,
            'quantity_scanned' => 0,
        ]);

        DataCollectionRecord::factory()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => Product::findBySKU('47')->getKey(),
            'quantity_requested' => 54,
            'quantity_scanned' => 0,
        ]);

        // fully scanned
        DataCollectionRecord::factory()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => Product::findBySKU('48')->getKey(),
            'quantity_requested' => 6,
            'quantity_scanned' => 6,
        ]);
    }
}
