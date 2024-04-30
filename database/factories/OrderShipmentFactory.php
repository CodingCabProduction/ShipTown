<?php

namespace Database\Factories;

use App\Models\Order;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderShipmentFactory extends Factory
{
    public function definition(): array
    {
        $shipping_number = $this->faker->toUpper(implode('', [
            $this->faker->randomLetter(),
            $this->faker->randomLetter(),
            '100',
            $this->faker->randomNumber(8)
        ]));

        $order = Order::query()->inRandomOrder()->first() ?? Order::factory()->create();
        $user = User::query()->inRandomOrder()->first() ?? User::factory()->create();

        return [
            'order_id'        => $order->getKey(),
            'carrier'         => $this->faker->randomElement(['DPD', 'UPS', 'SEUR', 'DHL', 'DPD Ireland', 'DPD UK']),
            'shipping_number' => $shipping_number,
            'tracking_url'    => $this->faker->url(),
            'user_id'         => $user->getKey(),
        ];
    }
}
