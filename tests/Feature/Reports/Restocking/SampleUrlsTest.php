<?php

namespace Tests\Feature\Reports\Restocking;

use App\User;
use Tests\TestCase;

class SampleUrlsTest extends TestCase
{
    public function getUrlsToTest(): array
    {
        return [
            '/reports/restocking?filter[warehouse_code]=83TS&filename=restocking_wh_to_83ts.csv&sort=-quantity_required&filter[quantity_required_between]=1,9999&filter[restock_level_between]=1,9999&filter[inventory_source_warehouse_code]=99&filter[warehouse_quantity_between]=1,999999&select=warehouse_code,product_sku,product_name,quantity_required,quantity_available,quantity_incoming,reorder_point,restock_level,warehouse_quantity',
        ];
    }

    /** @test */
    public function test_url()
    {
        collect($this->getUrlsToTest())
            ->each(function ($url) {
                $this->actingAs(User::factory()->create())
                    ->get($url)
                    ->assertStatus(200);
            });
    }
}
