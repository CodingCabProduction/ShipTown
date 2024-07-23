<?php

use App\Modules\QuantityDiscounts\src\QuantityDiscountsServiceProvider;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        QuantityDiscountsServiceProvider::installModule();
    }
};
