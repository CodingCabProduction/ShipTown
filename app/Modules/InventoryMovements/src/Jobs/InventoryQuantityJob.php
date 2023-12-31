<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class InventoryQuantityJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 10;

        do {
            $recordsUpdated = DB::update('
                    UPDATE inventory

                    INNER JOIN inventory_movements
                        ON inventory_movements.id = inventory.last_movement_id
                        AND (
                            inventory_movements.quantity_after != inventory.quantity
                            OR inventory_movements.created_at != inventory.last_movement_at
                        )

                    SET
                          inventory.quantity = inventory_movements.quantity_after
                        , inventory.last_movement_at = inventory_movements.created_at
                        , inventory.updated_at = now()
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
