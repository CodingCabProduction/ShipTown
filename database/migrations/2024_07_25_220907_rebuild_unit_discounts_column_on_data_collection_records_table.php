<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->dropColumn('unit_discount');
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->double('unit_discount', 20)
                ->storedAs('unit_full_price - unit_sold_price')
                ->comment('unit_full_price - unit_sold_price')
                ->after('unit_sold_price')
                ->nullable();
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->double('total_discount', 20)
                ->storedAs('quantity_scanned * (unit_full_price - unit_sold_price)')
                ->comment('quantity_scanned * (unit_full_price - unit_sold_price)')
                ->after('price_source')
                ->nullable();
        });
    }
};
