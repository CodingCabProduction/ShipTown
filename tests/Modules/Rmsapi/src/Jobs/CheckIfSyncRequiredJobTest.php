<?php

namespace Tests\Modules\Rmsapi\src\Jobs;

use App\Modules\Rmsapi\src\Jobs\CheckIfSyncRequiredJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Tests\TestCase;

class CheckIfSyncRequiredJobTest extends TestCase
{
    public function test_basic_functionality()
    {
        $record = RmsapiProductImport::factory()->create();

        ProcessImportedProductRecordsJob::dispatch();

        CheckIfSyncRequiredJob::dispatch();

        ray()->table(RmsapiProductImport::all()->toArray());

        $this->assertDatabaseMissing('modules_rmsapi_products_imports', [
            'sync_required' => null,
        ]);
    }

    public function test_sync_not_required()
    {
        /** @var RmsapiProductImport $record */
        $record = RmsapiProductImport::factory()->create([
            'quantity_on_hand' => 10,
            'restock_level' => 5,
            'reorder_point' => 2,
        ]);

        ProcessImportedProductRecordsJob::dispatch();

        $record->refresh()->inventory()->update([
            'quantity' => 10,
            'restock_level' => 5,
            'reorder_point' => 2,
        ]);

        CheckIfSyncRequiredJob::dispatch();

        ray(RmsapiProductImport::all()->toArray());
        ray($record->inventory->toArray());

        $this->assertDatabaseHas('modules_rmsapi_products_imports', [
            'id' => $record->getKey(),
            'sync_required' => false,
        ]);
    }
}
