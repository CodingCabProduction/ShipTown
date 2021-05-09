<?php

namespace App\Modules\InventoryReservations\src\Jobs;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalculateQuantityReservedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('');
        $checkedProductIds = [];

        $reservingStatusCodes = OrderStatus::whereReservesStock(true)
            ->select(['code'])
            ->get();

        OrderProduct::whereStatusCodeIn($reservingStatusCodes)
            ->select([
                'product_id',
                DB::raw('sum(quantity_to_ship) as new_quantity_reserved')
            ])
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->get()
            ->each(function ($orderProduct) use (&$checkedProductIds) {
                $checkedProductIds[] = $orderProduct->product_id;

                $inventory = Inventory::firstOrCreate([
                    'product_id' => $orderProduct->product_id,
                    'location_id' => 999,
                ]);

                if ($inventory->quantity_reserved != $orderProduct->getAttribute('new_quantity_reserved')) {
                    $orderProduct->product->log('Recalculated quantity_reserved');
                    $inventory->quantity_reserved = $orderProduct->getAttribute('new_quantity_reserved');
                    $inventory->save();
                }
            });

            Inventory::whereLocationId(999)
                ->where('quantity_reserved', '!=', 0)
                ->whereNotIn('product_id', $checkedProductIds)
                ->get()
                ->each(function (Inventory $inventory) {
                    $inventory->product->log('Resetting quantity reserved');
                    $inventory->quantity_reserved = 0;
                    $inventory->save();
                });
    }
}