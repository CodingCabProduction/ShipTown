<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collections', function (Blueprint $table) {
            $table->decimal('total_outstanding', 20)
                ->nullable()
                ->after('total_sold_price')
                ->storedAs('total_sold_price - total_paid');
        });
    }
};
