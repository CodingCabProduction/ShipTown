<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAliasFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::query()->inRandomOrder()->first();

        if (empty($product)) {
            $product = Product::factory()->create();
        }

        return [
            'product_id' => $product->getKey(),
            'alias'      => $this->faker->ean13(),
        ];
    }
}
