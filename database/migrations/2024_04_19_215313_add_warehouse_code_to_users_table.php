<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('warehouse_code')->nullable()->after('name');

            $table->foreign('warehouse_code')
                ->references('code')
                ->on('warehouses')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }
};
