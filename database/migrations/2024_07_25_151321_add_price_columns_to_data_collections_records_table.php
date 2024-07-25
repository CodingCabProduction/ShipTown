<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->decimal('unit_cost', 10, 2)->nullable()->after('quantity_to_scan');
            $table->decimal('unit_sold_price', 10, 2)->nullable()->after('unit_cost');
            $table->decimal('unit_discount', 10, 2)->nullable()->after('unit_sold_price');
            $table->decimal('unit_full_price', 10, 2)->nullable()->after('unit_discount');
            $table->string('price_source')->nullable()->after('unit_full_price');
        });
    }
};
