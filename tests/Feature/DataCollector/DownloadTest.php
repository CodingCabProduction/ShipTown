<?php

namespace Tests\Feature\DataCollector;

use App\User;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    /** @test */
    public function test_if_works()
    {
        $this
            ->actingAs(User::factory()->create())
            ->get('/data-collector/2?warehouse_code=DUB&select=product_sku,product_name,total_transferred_in,total_transferred_out,quantity_requested,quantity_to_scan,quantity_scanned,inventory_quantity,product_price,product_sale_price,product_sale_price_start_date,product_sale_price_end_date,product_cost,last7days_sales,last14days_sales,last28days_sales&filter%5Bdata_collection_id%5D=2&filename=Transfer%20from%20Warehouse.csv')
            ->assertSuccessful();
    }
}
