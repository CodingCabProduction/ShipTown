<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_id')->after('product_id')->nullable();

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->nullOnDelete();
        });
    }
};
