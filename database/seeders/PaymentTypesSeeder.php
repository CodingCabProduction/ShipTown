<?php

namespace Database\Seeders;

use App\Modules\DataCollectorPayments\src\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTypes = [
            [
                'code' => 'CASH',
                'name' => 'Cash',
            ],
            [
                'code' => 'CREDIT_CARD',
                'name' => 'Credit Card',
            ],
            [
                'code' => 'CHEQUE',
                'name' => 'Cheque',
            ],
        ];

        foreach ($paymentTypes as $paymentType) {
            PaymentType::create($paymentType);
        }
    }
}
