<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_totals', function (Blueprint $table) {
            $table->boolean('recount_required')->default(true);
        });
    }
};
