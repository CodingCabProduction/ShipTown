<?php

namespace App\Modules\InventoryMovementsStatistics\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class AccountForNewSalesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->update7DaysSalesStatistics();
        $this->update14DaysSalesStatistics();
        $this->update28DaysSalesStatistics();
    }

    private function update7DaysSalesStatistics(): void
    {
        DB::unprepared("
            DROP TEMPORARY TABLE IF EXISTS temp_itemMovementsStatistics_3982179371_7days;

            CREATE TEMPORARY TABLE temp_itemMovementsStatistics_3982179371_7days AS (
                SELECT inventory_id,
                       sum(quantity_sold) as quantity_sold,
                       max(sold_at) as max_sold_at
                FROM modules_inventory_movements_statistics_last28days_sale_movements
                WHERE included_in_7days IS NULL
                  AND sold_at > date_sub(now(), interval 7 day)
                GROUP BY inventory_id
            );

            INSERT INTO inventory_movements_statistics (inventory_id, product_id, warehouse_code, warehouse_id, created_at, updated_at)
                SELECT inventory.id, inventory.product_id, inventory.warehouse_code, inventory.warehouse_id, now(), now()
                FROM temp_itemMovementsStatistics_3982179371_7days as tempTable
                LEFT JOIN inventory_movements_statistics
                    ON inventory_movements_statistics.inventory_id = tempTable.inventory_id
                LEFT JOIN inventory
                    ON inventory.id = tempTable.inventory_id
                WHERE inventory_movements_statistics.inventory_id is null;

            UPDATE inventory_movements_statistics
                RIGHT JOIN temp_itemMovementsStatistics_3982179371_7days as tempTable
                ON inventory_movements_statistics.inventory_id = tempTable.inventory_id
            SET
                quantity_sold_last_7_days = IFNULL(quantity_sold_last_7_days, 0) + quantity_sold;

            UPDATE modules_inventory_movements_statistics_last28days_sale_movements
            LEFT JOIN temp_itemMovementsStatistics_3982179371_7days as tempTable
                ON tempTable.inventory_id = modules_inventory_movements_statistics_last28days_sale_movements.inventory_id
            SET included_in_7days = 1
            WHERE included_in_7days IS NULL
              AND modules_inventory_movements_statistics_last28days_sale_movements.sold_at <= tempTable.max_sold_at;

            DROP TEMPORARY TABLE IF EXISTS temp_itemMovementsStatistics_3982179371_7days;
        ");
    }

    private function update14DaysSalesStatistics(): void
    {
        DB::unprepared("
            DROP TEMPORARY TABLE IF EXISTS temp_itemMovementsStatistics_3982179371_14days;

            CREATE TEMPORARY TABLE temp_itemMovementsStatistics_3982179371_14days AS (
                SELECT inventory_id,
                       sum(quantity_sold) as quantity_sold,
                       max(sold_at) as max_sold_at
                FROM modules_inventory_movements_statistics_last28days_sale_movements
                WHERE included_in_14days IS NULL
                  AND sold_at > date_sub(now(), interval 14 day)
                GROUP BY inventory_id
            );

            INSERT INTO inventory_movements_statistics (inventory_id, product_id, warehouse_code, warehouse_id, created_at, updated_at)
                SELECT inventory.id, inventory.product_id, inventory.warehouse_code, inventory.warehouse_id, now(), now()
                FROM temp_itemMovementsStatistics_3982179371_14days as tempTable
                LEFT JOIN inventory_movements_statistics
                    ON inventory_movements_statistics.inventory_id = tempTable.inventory_id
                LEFT JOIN inventory
                    ON inventory.id = tempTable.inventory_id
                WHERE inventory_movements_statistics.inventory_id is null;

            UPDATE inventory_movements_statistics
                RIGHT JOIN temp_itemMovementsStatistics_3982179371_14days as tempTable
                ON inventory_movements_statistics.inventory_id = tempTable.inventory_id
            SET
                quantity_sold_last_14_days = IFNULL(quantity_sold_last_14_days, 0) + quantity_sold;

            UPDATE modules_inventory_movements_statistics_last28days_sale_movements
            LEFT JOIN temp_itemMovementsStatistics_3982179371_14days as tempTable
                ON tempTable.inventory_id = modules_inventory_movements_statistics_last28days_sale_movements.inventory_id
            SET included_in_14days = 1
            WHERE included_in_14days IS NULL
              AND modules_inventory_movements_statistics_last28days_sale_movements.sold_at <= tempTable.max_sold_at;

            DROP TEMPORARY TABLE IF EXISTS temp_itemMovementsStatistics_3982179371_14days;
        ");
    }

    private function update28DaysSalesStatistics(): void
    {
        DB::unprepared("
            DROP TEMPORARY TABLE IF EXISTS temp_itemMovementsStatistics_3982179371_28days;

            CREATE TEMPORARY TABLE temp_itemMovementsStatistics_3982179371_28days AS (
                SELECT inventory_id,
                       sum(quantity_sold) as quantity_sold,
                       max(sold_at) as max_sold_at
                FROM modules_inventory_movements_statistics_last28days_sale_movements
                WHERE included_in_28days IS NULL
                  AND sold_at > date_sub(now(), interval 28 day)
                GROUP BY inventory_id
            );

            INSERT INTO inventory_movements_statistics (inventory_id, product_id, warehouse_code, warehouse_id, created_at, updated_at)
                SELECT inventory.id, inventory.product_id, inventory.warehouse_code, inventory.warehouse_id, now(), now()
                FROM temp_itemMovementsStatistics_3982179371_28days as tempTable
                LEFT JOIN inventory_movements_statistics
                    ON inventory_movements_statistics.inventory_id = tempTable.inventory_id
                LEFT JOIN inventory
                    ON inventory.id = tempTable.inventory_id
                WHERE inventory_movements_statistics.inventory_id is null;

            UPDATE inventory_movements_statistics
                RIGHT JOIN temp_itemMovementsStatistics_3982179371_28days as tempTable
                ON inventory_movements_statistics.inventory_id = tempTable.inventory_id
            SET
                quantity_sold_last_28_days = IFNULL(quantity_sold_last_28_days, 0) + quantity_sold;

            UPDATE modules_inventory_movements_statistics_last28days_sale_movements
            LEFT JOIN temp_itemMovementsStatistics_3982179371_28days as tempTable
                ON tempTable.inventory_id = modules_inventory_movements_statistics_last28days_sale_movements.inventory_id
            SET included_in_28days = 1
            WHERE included_in_28days IS NULL
              AND modules_inventory_movements_statistics_last28days_sale_movements.sold_at <= tempTable.max_sold_at;

            DROP TEMPORARY TABLE IF EXISTS temp_itemMovementsStatistics_3982179371_28days;
        ");
    }
}