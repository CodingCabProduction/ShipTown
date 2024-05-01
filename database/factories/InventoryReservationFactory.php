<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quantity_reserved' => $this->faker->randomNumber(2),
        ];
    }
}
