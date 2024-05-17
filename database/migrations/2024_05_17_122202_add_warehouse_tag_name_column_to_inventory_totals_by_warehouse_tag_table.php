<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_totals_by_warehouse_tag', function (Blueprint $table) {
            $table->string('warehouse_tag_name')->nullable()->after('id');

            $table->index('warehouse_tag_name');
        });
    }
};
