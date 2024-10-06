<?php

namespace App\Modules\Rmsapi\src;

use App\Events\EveryDayEvent;
use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\Rmsapi\src\Jobs\CleanupImportTablesJob;
use App\Modules\Rmsapi\src\Jobs\ImportAllJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Jobs\UpdateImportedSalesRecordsJob;

class RmsapiModuleServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'eCommerce - RMSAPI Integration';

    public static string $module_description = 'Provides connectivity to Microsoft RMS 2.0';

    public static string $settings_link = '/settings/rmsapi';

    public static bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        InventoryUpdatedEvent::class => [
            Listeners\InventoryUpdatedEventListener::class,
        ],

        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        EveryFiveMinutesEvent::class => [
            Listeners\EveryFiveMinutesEventListener::class,
        ],

        EveryTenMinutesEvent::class => [
            Listeners\EveryTenMinutesEventListener::class,
        ],

        EveryDayEvent::class => [
            Listeners\EveryDayEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        CleanupImportTablesJob::dispatch();

        ImportAllJob::dispatch();

        UpdateImportedSalesRecordsJob::dispatch();
        ProcessImportedProductRecordsJob::dispatch();
        ProcessImportedSalesRecordsJob::dispatch();

        return parent::enabling();
    }
}
