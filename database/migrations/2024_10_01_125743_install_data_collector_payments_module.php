<?php

use App\Modules\DataCollectorPayments\src\DataCollectorPaymentsServiceProvider;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DataCollectorPaymentsServiceProvider::enableModule();
    }
};
