<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('data_collections', function (Blueprint $table) {
            $table->decimal('total_paid', 20)->nullable()->default(0)->after('total_sold_price');
        });
    }
};
