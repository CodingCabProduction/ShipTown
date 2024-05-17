<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_totals_by_warehouse_tag', function (Blueprint $table) {
            $table->string('product_sku')->nullable()->after('id');

            $table->foreign('product_sku')
                ->references('sku')
                ->on('products');
        });
    }
};
