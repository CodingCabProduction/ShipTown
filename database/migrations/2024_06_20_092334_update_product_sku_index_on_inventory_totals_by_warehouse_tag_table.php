<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('inventory_totals_by_warehouse_tag', function (Blueprint $table) {
                $table->dropForeign('inventory_totals_by_warehouse_tag_product_sku_foreign');
            });
        } catch (Exception $e) {
            report($e);
        }

        Schema::table('inventory_totals_by_warehouse_tag', function (Blueprint $table) {
            $table->foreign('product_sku')
                ->references('sku')
                ->on('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
