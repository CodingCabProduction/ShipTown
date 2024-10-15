<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_magento2api_products', function (Blueprint $table) {
            $table->string('magento_product_id')->nullable()->after('exists_in_magento');
            $table->string('magento_product_type')->nullable()->after('magento_product_id');
        });
    }
};
