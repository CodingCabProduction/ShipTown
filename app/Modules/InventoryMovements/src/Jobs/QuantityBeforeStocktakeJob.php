<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class QuantityBeforeStocktakeJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 50;

        do {
            $maxRounds--;

            Log::debug('QuantityBeforeStocktakeJob round starting', ['rounds_left' => $maxRounds]);

            Schema::dropIfExists('tempTable');

            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                    SELECT
                       inventory_movements.id as movement_id,
                       previous_movement.quantity_after as quantity_before_expected,
                       inventory_movements.quantity_after - previous_movement.quantity_after as quantity_delta_expected,
                       inventory_movements.quantity_after as quantity_after_expected,
                       inventory_movements.quantity_before,
                       inventory_movements.quantity_delta,
                       inventory_movements.quantity_after

                    FROM inventory_movements
                    LEFT JOIN inventory_movements as previous_movement
                      ON previous_movement.id = inventory_movements.previous_movement_id

                    WHERE inventory_movements.type = "stocktake"
                    AND previous_movement.quantity_after != inventory_movements.quantity_before

                    LIMIT 1000;
            ');

            $recordsUpdated = DB::update('
                UPDATE inventory_movements
                INNER JOIN tempTable
                  ON tempTable.movement_id = inventory_movements.id

                SET inventory_movements.quantity_before = tempTable.quantity_before_expected,
                  inventory_movements.quantity_delta = tempTable.quantity_delta_expected
            ');


            Log::debug('QuantityBeforeStocktakeJob round finished', ['rounds_left' => $maxRounds, 'recordsUpdated' => $recordsUpdated]);

            sleep(1);
        } while ($recordsUpdated > 0 && $maxRounds > 0);
    }
}
