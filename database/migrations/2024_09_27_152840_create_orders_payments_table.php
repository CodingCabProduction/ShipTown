<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders_payments', function (Blueprint $table) {
            $table->id();
            $table->dateTime('paid_at')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('name');
            $table->decimal('amount', 20);
            $table->json('additional_fields')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }
};
