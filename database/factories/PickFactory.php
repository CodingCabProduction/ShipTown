<?php

namespace Database\Factories;

use App\Models\Product;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PickFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::query()->inRandomOrder()->first() ?? Product::factory()->create();

        $user = User::query()->inRandomOrder()->first() ?? User::factory()->create();

        $skippingPick = (rand(1, 20) === 1);

        return [
            'product_id'               => $product->getKey(),
            'sku_ordered'              => $product->sku,
            'name_ordered'             => $product->name,
            'user_id'                  => $user->getKey(),
            'quantity_picked'          => $skippingPick ? 0 : $this->faker->numberBetween(1, 50),
            'quantity_skipped_picking' => $skippingPick ? $this->faker->numberBetween(1, 50) : 0,
        ];
    }
}
