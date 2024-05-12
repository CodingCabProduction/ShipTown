<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionStocktake;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\StocktakeSuggestion;
use App\Modules\Inventory\src\Jobs\DispatchRecalculateInventoryRecordsJob;
use App\Modules\InventoryMovements\src\Jobs\SequenceNumberJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportAsStocktakeJob extends UniqueJob
{
    public int $dataCollection_id;

    public function __construct(int $dataCollection_id)
    {
        $this->dataCollection_id = $dataCollection_id;
    }

    public function uniqueId(): string
    {
        return implode('-', [get_class($this), $this->dataCollection_id]);
    }

    public function handle(): void
    {
        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::withTrashed()->findOrFail($this->dataCollection_id);

        if ($dataCollection->deleted_at === null) {
            $dataCollection->delete();
        }

        $dataCollection->update(['type' => DataCollectionStocktake::class]);

        do {
            $dataCollectionRecords = $dataCollection->records()
                ->whereNull('is_processed')
                ->limit(100)
                ->get();

            if ($dataCollectionRecords->isEmpty()) {
                $dataCollection->update(['currently_running_task' => null]);
                Bus::chain([
                    new SequenceNumberJob(),
                    new DispatchRecalculateInventoryRecordsJob(),
                ])->dispatch();
                return;
            }

            $inventoryMovementRecords = $dataCollectionRecords->map(function (DataCollectionRecord $record) use ($dataCollection) {
                $custom_uuid = implode('-', ['source_data_collections_records_id', $record->getKey()]);

                return [
                    'custom_unique_reference_id' => $custom_uuid,
                    'sequence_number' => null,
                    'occurred_at' => $dataCollection->deleted_at ?? now()->utc()->toDateTimeLocalString(),
                    'inventory_id' => $record->inventory_id,
                    'type' => InventoryMovement::TYPE_STOCKTAKE,
                    'product_id' => $record->product_id,
                    'warehouse_id' => $dataCollection->warehouse->id,
                    'warehouse_code' => $dataCollection->warehouse->code,
                    'quantity_before' => $record->inventory->quantity,
                    'quantity_delta' => $record->quantity_scanned - $record->inventory->quantity,
                    'quantity_after' => $record->quantity_scanned,
                    'description' => Str::substr('Data Collection - ' . $dataCollection->name, 0, 255),
                    'user_id' => Auth::id(),
                    'created_at' => now()->utc()->toDateTimeLocalString(),
                    'updated_at' => now()->utc()->toDateTimeLocalString(),
                ];
            });

            DB::transaction(function () use ($dataCollection, $inventoryMovementRecords, $dataCollectionRecords) {
                InventoryMovement::query()->upsert($inventoryMovementRecords->toArray(), ['custom_unique_reference_id'], ['sequence_number', 'quantity_after', 'updated_at', 'description']);

                StocktakeSuggestion::query()->whereIn('inventory_id', $dataCollectionRecords->pluck('inventory_id'))->delete();

                DataCollectionRecord::query()->whereIn('id', $dataCollectionRecords->pluck('id'))->update(['is_processed' => true]);

                Inventory::query()->upsert($dataCollectionRecords->map(function (DataCollectionRecord $record) use ($dataCollection) {
                    return [
                        'id' => $record->inventory_id,
                        'quantity' => $record->quantity_scanned,
                        'warehouse_id' => $dataCollection->warehouse_id,
                        'warehouse_code' => $dataCollection->warehouse->code,
                        'product_id' => $record->product_id,
                        'last_movement_at' => now()->utc()->toDateTimeLocalString(),
                        'last_counted_at' => now()->utc()->toDateTimeLocalString(),
                    ];
                })->toArray(), ['id'], ['quantity', 'last_movement_at', 'last_counted_at']);
            });
        } while ($dataCollectionRecords->isNotEmpty());
    }
}
