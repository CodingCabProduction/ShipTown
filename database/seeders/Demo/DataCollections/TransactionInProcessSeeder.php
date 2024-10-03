<?php

namespace Database\Seeders\Demo\DataCollections;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionTransaction;
use Illuminate\Database\Seeder;

class TransactionInProcessSeeder extends Seeder
{
    public function run(): void
    {
        $uuid = 'TRANSACTION_IN_PROGRESS_FOR_USER_1_Artur Hanusek';

        DataCollection::query()
            ->where('custom_uuid', $uuid)
            ->update(['name' => null, 'custom_uuid' => null]);

        $dataCollection = DataCollection::factory()->create([
            'type' => DataCollectionTransaction::class,
            'custom_uuid' => $uuid,
        ]);

        DataCollectionRecord::factory()
            ->count(rand(3, 5))
            ->create([
                'data_collection_id' => $dataCollection->getKey(),
                'warehouse_id' => 1,
                'warehouse_code' => 'WHS',
            ]);
    }
}
