<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modules_quantity_discounts_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quantity_discount_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('quantity_discount_id')
                ->references('id')
                ->on('modules_quantity_discounts')
                ->onDelete('cascade');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules_quantity_discounts_products');
    }
};
