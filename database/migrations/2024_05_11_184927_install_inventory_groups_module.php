<?php

use App\Modules\InventoryGroups\src\InventoryGroupsServiceProvider;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        InventoryGroupsServiceProvider::installModule();
    }
};
