<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_collection_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_type_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->decimal('amount', 20)->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('payment_type_id')
                ->references('id')
                ->on('payment_types')
                ->cascadeOnDelete();

            $table->foreign('transaction_id')
                ->references('id')
                ->on('data_collections')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_collection_payments');
    }
};
