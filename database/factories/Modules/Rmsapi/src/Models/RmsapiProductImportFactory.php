<?php

namespace Database\Factories\Modules\Rmsapi\src\Models;

use App\Models\Product;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RmsapiProductImportFactory extends Factory
{
    protected $model = RmsapiProductImport::class;

    public function definition(): array
    {
        /** @var RmsapiConnection $connection */
        $connection = RmsapiConnection::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        $random_department = $this->faker->randomElement(['household', 'bedding', 'fashion', 'baby']);
        $random_category = $this->faker->randomElement(['category1', 'category2', 'category3', 'category4']);
        $quantity_on_hand = rand(0, 1000);
        $quantity_on_order = rand(10, 20);
        $quantity_committed = rand(0, $quantity_on_hand);
        $quantity_available = $quantity_on_hand - $quantity_committed;
        $reorder_point = rand(0, 100);
        $restock_level = rand(0, $reorder_point);

        $price = rand(1, 100000) / 100;
        $price_a = rand(1, 100000) / 100;
        $price_b = rand(1, 100000) / 100;
        $price_c = rand(1, 100000) / 100;

        return [
            'connection_id' => $connection->getKey(),
            'warehouse_id' => $connection->warehouse_id,
            'warehouse_code' => $connection->location_id,
            'sku' => $product->sku,
            'name' => $product->name,
            'rms_product_id' => rand(1, 1000),
            'quantity_on_hand' => $quantity_on_hand,
            'quantity_on_order' => $quantity_on_order,
            'quantity_available' => $quantity_available,
            'quantity_committed' => $quantity_committed,
            'reorder_point' => $reorder_point,
            'restock_level' => $restock_level,
            'price' => $price,
            'price_a' => $price_a,
            'raw_import' => json_decode('{
                "id":1,
                "cost":110.34,
                "price":'. $price .',
                "active":1,
                "item_id":1,
                "price_a":'. $price_a .',
                "price_b":'. $price_b .',
                "price_c":'. $price_c .',
                "item_code":"'. $product->sku .'",
                "sale_price":0,
                "category_id":9,
                "description":"R/C Glider",
                "is_web_item":'.rand(0, 1).',
                "supplier_id":2,
                "date_created":"2003-06-03 17:17:23",
                "department_id":5,
                "department_name":"'.$random_department.'",
                "category_name":"'.$random_category.'",
                "reorder_point":'.$reorder_point.',
                "restock_level":'.$restock_level.',
                "sale_end_date":null,
                "food_stampable":0,
                "db_change_stamp":'.rand(1000, 100000).',
                "sale_start_date":null,
                "quantity_on_hand":'.$quantity_on_hand.',
                "quantity_on_order":'. $quantity_on_order .',
                "sub_description_1":"",
                "sub_description_2":"",
                "sub_description_3":"",
                "quantity_available":'.$quantity_available.',
                "quantity_committed":'.$quantity_committed.',
                "quantity_discount_id":0,
                "supplier_code":"supplier234",
                "supplier_name":"ShipTown Ltd",
                "rmsmobile_shelve_location":"'.Str::upper($this->faker->randomLetter()).$this->faker->numberBetween(0, 9).'",
                "aliases": [
                     {
                         "id": 1,
                         "alias": "'.$this->faker->unique()->ean13().'",
                         "item_id": 1
                     },
                     {
                         "id": 2,
                         "alias": "'.$this->faker->unique()->ean13().'",
                         "item_id": 1
                     }
                ]
            }
            '),
        ];
    }
}
