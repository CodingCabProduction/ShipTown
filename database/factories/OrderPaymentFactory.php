<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderPaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paid_at' => $this->faker->date(),
            'name' => $this->faker->creditCardType(),
            'amount' => $this->faker->randomFloat(2, 5, 10),
            'additional_fields' => [
                'id' => $this->faker->uuid,
                'key' => 'value',
            ],
            'order_id' => function () {
                return Order::factory()->create()->getKey();
            },
        ];
    }
}
