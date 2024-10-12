<?php

namespace Database\Factories;

use App\Models\OrderAddress;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataCollectionFactory extends Factory
{
    public function definition(): array
    {
        $warehouse = Warehouse::first() ?? Warehouse::factory()->create();
        $orderAddresses = OrderAddress::factory(2)->create();

        return [
            'warehouse_code' => $warehouse->code,
            'warehouse_id' => $warehouse->getKey(),
            'name' => $this->faker->word(),
            'shipping_address_id' => $orderAddresses[0]->getKey(),
            'billing_address_id' => $orderAddresses[1]->getKey(),
        ];
    }
}
