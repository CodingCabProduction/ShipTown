<?php

namespace Database\Factories\Modules\DataCollectorPayments\src\Models;

use App\Modules\DataCollectorPayments\src\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentTypeFactory extends Factory
{
    protected $model = PaymentType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->currencyCode,
            'name' => $this->faker->unique()->creditCardType,
        ];
    }
}
