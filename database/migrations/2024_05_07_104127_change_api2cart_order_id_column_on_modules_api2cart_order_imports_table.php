<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_api2cart_order_imports', function (Blueprint $table) {
            $table->bigInteger('api2cart_order_id')->change();
        });
    }
};
