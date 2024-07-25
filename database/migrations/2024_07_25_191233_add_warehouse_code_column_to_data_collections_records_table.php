<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->string('warehouse_code')->after('product_id')->nullable();

            $table->foreign('warehouse_code')
                ->references('code')
                ->on('warehouses')
                ->nullOnDelete();
        });
    }
};
