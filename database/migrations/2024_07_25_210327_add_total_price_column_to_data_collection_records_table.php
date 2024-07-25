<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->decimal('total_price', 20)
                ->after('price_source')
                ->storedAs('quantity_scanned * unit_sold_price')
                ->comment('quantity_scanned * unit_price')
                ->index();
        });
    }
};
