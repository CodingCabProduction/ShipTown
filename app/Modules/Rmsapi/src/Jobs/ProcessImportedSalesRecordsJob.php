<?php

namespace App\Modules\Rmsapi\src\Jobs;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use App\Services\InventoryService;
use App\Traits\IsMonitored;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessImportedSalesRecordsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    private int $connection_id;

    public function __construct(int $connection_id)
    {
        $this->connection_id = $connection_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        RmsapiSaleImport::query()
            ->whereNull('processed_at')
            ->where('comment', 'like', 'PM_OrderProductShipment_%')
            ->update(['reserved_at' => now(), 'processed_at' => now()]);

        $maxRunCount = 3;

        do {
            $this->processImportedRecords(200);
            $maxRunCount--;
        } while ($maxRunCount > 0 and RmsapiSaleImport::query()->whereNull('processed_at')->exists());
    }

    private function processImportedRecords(int $batch_size): void
    {
        $reservationTime = now();

        // reserve records
        RmsapiSaleImport::query()
            ->where('connection_id', $this->connection_id)
            ->where('comment', 'not like', 'PM_OrderProductShipment_%')
            ->orderBy('id')
            ->limit($batch_size)
            ->update(['reserved_at' => $reservationTime]);

        // process records
        RmsapiSaleImport::query()
            ->where([
                'connection_id' => $this->connection_id,
                'reserved_at' => $reservationTime
            ])
            ->whereNull('processed_at')
            ->where('comment', 'not like', 'PM_OrderProductShipment_%')
            ->orderBy('id')
            ->get()
            ->each(function (RmsapiSaleImport $salesRecord) {
                try {
                    retry(3, function () use ($salesRecord) {
                        DB::transaction(function () use ($salesRecord) {
                            $this->import($salesRecord);
                        });
                    }, 1000);
                } catch (Exception $e) {
                    report($e);
                    Log::emergency($e->getMessage(), $e->getTrace());
                }
            });
    }

    private function import(RmsapiSaleImport $salesRecord)
    {
        $unique_reference_id = implode(':', [
            'rms_transaction', $salesRecord->transaction_number,
            'store_id', data_get($salesRecord->raw_import, 'store_id', 0),
            'entry_id', $salesRecord->transaction_entry_id
        ]);

        if (InventoryMovement::query()->where('custom_unique_reference_id', $unique_reference_id)->exists()) {
            $salesRecord->update(['processed_at' => now()]);
            return;
        }

        $product = Product::query()
            ->whereHas('aliases', function ($join) use ($salesRecord) {
                $join->where('alias', $salesRecord->sku);
            })
            ->first();

        $inventory = Inventory::query()
            ->where('product_id', $product->id)
            ->where('warehouse_id', $salesRecord->rmsapiConnection->warehouse_id)
            ->first();

        InventoryService::adjustQuantity(
            $inventory,
            $salesRecord->quantity * -1,
            'rms_sale',
            $unique_reference_id
        );

        $salesRecord->update(['processed_at' => now()]);
    }
}