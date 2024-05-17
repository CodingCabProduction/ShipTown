<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('configurations', 'database_version')) {
            return;
        }

        Schema::table('configurations', function (Blueprint $table) {
            $table->string('database_version')->default('0.0.0');
        });
    }
};
