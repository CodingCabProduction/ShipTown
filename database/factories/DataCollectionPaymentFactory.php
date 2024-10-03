<?php

namespace Database\Factories;

use App\Models\DataCollection;
use App\Modules\DataCollectorPayments\src\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataCollectionPaymentFactory extends Factory
{
    public function definition(): array
    {
        /** @var PaymentType $paymentType */
        $paymentType = PaymentType::factory()->create();

        /** @var DataCollection $transaction */
        $transaction = DataCollection::factory()->create([
            'type' => 'App\Models\DataCollectionTransaction',
        ]);

        return [
            'payment_type_id' => $paymentType->id,
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'transaction_id' => $transaction->id,
        ];
    }
}
