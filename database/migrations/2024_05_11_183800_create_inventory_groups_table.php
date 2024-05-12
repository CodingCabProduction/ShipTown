<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->string('group_name');
            $table->boolean('recount_required')->default(true);
            $table->decimal('total_quantity_in_stock');
            $table->decimal('total_quantity_reserved');
            $table->decimal('total_quantity_available');
            $table->decimal('total_quantity_incoming');
            $table->decimal('total_quantity_required');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
        });
    }
};
