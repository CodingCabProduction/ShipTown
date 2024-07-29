<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modules_quantity_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type', 50)->nullable();
            $table->json('configuration')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules_quantity_discounts');
    }
};
