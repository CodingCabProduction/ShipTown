<?php

namespace Database\Seeders\Demo;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class ProductsPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::query()
            ->chunkById(50, function ($productsList) {
                foreach ($productsList as $product) {
                    $randomPrice = rand(1, 100) - (0.05 * rand(0, 1));

                    $salesPrice = $randomPrice * ([1, 1, 2/3, 0.5][rand(0, 3)]);
                    $saleStartDate = today()->subDays(rand(10, 30));
                    $salePriceEndDate = today()->subDays(9)->addDays(rand(0, 20));

                    ProductPrice::query()
                        ->where('product_id', '=', $product->getKey())
                        ->update([
                            'price' => $randomPrice,
                            'sale_price' => $salesPrice,
                            'sale_price_start_date' => $saleStartDate,
                            'sale_price_end_date' => $salePriceEndDate,
                        ]);
                }
            });
    }
}
