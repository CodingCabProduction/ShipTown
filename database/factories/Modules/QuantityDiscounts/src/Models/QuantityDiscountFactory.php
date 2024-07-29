<?php

namespace Database\Factories\Modules\QuantityDiscounts\src\Models;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuantityDiscountFactory extends Factory
{
    protected $model = QuantityDiscount::class;

    public function definition(): array
    {
        return [
            'name' => 'Test Quantity Discount',
            'type' => 'BUY_X_GET_Y_FOR_Z_PRICE',
            'configuration' => []
        ];
    }
}
