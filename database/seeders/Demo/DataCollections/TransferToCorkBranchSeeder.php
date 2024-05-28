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
            'product_id' => Product::inRandomOrder('45')->first()->getKey(),
            'quantity_requested' => 1,
            'quantity_scanned' => 0,
        ]);

        DataCollectionRecord::factory()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => Product::inRandomOrder('46')->first()->getKey(),
            'quantity_requested' => 5,
            'quantity_scanned' => 0,
        ]);

        DataCollectionRecord::factory()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => Product::inRandomOrder('47')->first()->getKey(),
            'quantity_requested' => 54,
            'quantity_scanned' => 0,
        ]);

        // fully scanned
        DataCollectionRecord::factory()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => Product::inRandomOrder('48')->first()->getKey(),
            'quantity_requested' => 6,
            'quantity_scanned' => 6,
        ]);
    }
}
