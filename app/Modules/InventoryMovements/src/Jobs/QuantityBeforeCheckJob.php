<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuantityBeforeCheckJob extends UniqueJob
{
    private Carbon $date;
    private int $batchSize = 1000;

    public function __construct($date = null)
    {
        $this->date = $date ?? Carbon::now();
    }

    public function handle(): void
    {
        do {
            $idsToUpdate = DB::table('inventory_movements')
                ->join('inventory_movements as previous_movement', function ($join) {
                    $join->on('inventory_movements.inventory_id', '=', 'previous_movement.inventory_id')
                        ->whereRaw('inventory_movements.sequence_number - 1 = previous_movement.sequence_number')
                        ->where(function ($query) {
                            $query->where('inventory_movements.quantity_before', '!=', 'previous_movement.quantity_after')
                                ->orWhere('inventory_movements.occurred_at', '<', 'previous_movement.occurred_at');
                        });
                })
                ->whereBetween('inventory_movements.occurred_at', [$this->date->toDateTimeLocalString(), $this->date->addDay()->toDateTimeLocalString()])
                ->limit($this->batchSize)
                ->pluck('inventory_movements.id');

            $recordsUpdated = DB::table('inventory_movements')
                ->whereIn('id', $idsToUpdate)
                ->update(['sequence_number' => null]);

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated,
            ]);

            usleep(100000); // 0.1 seconds
        } while ($recordsUpdated > 0);
    }
}
