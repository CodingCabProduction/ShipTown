<?php

namespace App\Modules\Inventory\src;

use App\Events\EveryMinuteEvent;
use App\Modules\BaseModuleServiceProvider;

class InventoryServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Inventory';

    public static string $module_description = 'Provides inventory management functionality.';

    public static bool $autoEnable = true;

    protected $listen = [
        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],
    ];
}
