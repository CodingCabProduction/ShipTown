<?php

namespace App\Modules\InventoryGroups\src;

use App\Events\Inventory\RecalculateInventoryRequestEvent;
use App\Modules\BaseModuleServiceProvider;

class InventoryGroupsServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Inventory Groups';

    public static string $module_description = 'Provides inventory groups for products';

    public static bool $autoEnable = true;

    protected $listen = [
        RecalculateInventoryRequestEvent::class => [
            Listeners\RecalculateInventoryRequestEventListener::class,
        ],
    ];
}
