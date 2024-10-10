<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('modules_webhooks_pending_webhooks', 'message')) {
            Schema::table('modules_webhooks_pending_webhooks', function (Blueprint $table) {
                $table->dropColumn('message');
            });
        }

        if (Schema::hasColumn('modules_webhooks_pending_webhooks', 'available_at')) {
            Schema::table('modules_webhooks_pending_webhooks', function (Blueprint $table) {
                $table->dropColumn('available_at');
            });
        }
    }
};
