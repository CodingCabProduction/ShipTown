<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {

            $table->decimal('total_cost', 12, 4)->default(0)->after('quantity');
            $table->decimal('total_price', 12, 4)->default(0)->after('total_cost');
        });
    }
};
