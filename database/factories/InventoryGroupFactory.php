<?php

namespace Database\Factories;

use App\Models\Product;
use App\Modules\InventoryGroups\src\Models\InventoryGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class InventoryGroupFactory extends Factory
{
    protected $model = InventoryGroup::class;

    public function definition(): array
    {
        return [
            'group_name' => $this->faker->name(),
            'product_id' => Product::factory(),
            'total_quantity_in_stock' => $this->faker->randomFloat(),
            'total_quantity_reserved' => $this->faker->randomFloat(),
            'total_quantity_available' => $this->faker->randomFloat(),
            'total_quantity_incoming' => $this->faker->randomFloat(),
            'total_quantity_required' => $this->faker->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
