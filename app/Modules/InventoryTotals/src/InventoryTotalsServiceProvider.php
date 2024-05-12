<?php

namespace App\Modules\InventoryTotals\src;

use App\Events\EveryDayEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\Inventory\RecalculateInventoryRequestEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Events\Warehouse\WarehouseTagAttachedEvent;
use App\Models\ManualRequestJob;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class InventoryQuantityReservedServiceProvider.
 */
class InventoryTotalsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Inventory Totals';

    /**
     * @var string
     */
    public static string $module_description = 'Tracks inventory totals for each product';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        EveryTenMinutesEvent::class => [
            Listeners\EveryTenMinutesEventListener::class,
        ],

        EveryDayEvent::class => [
            Listeners\EveryDayEventListener::class,
        ],

        ProductCreatedEvent::class => [
            Listeners\ProductCreatedEventListener::class,
        ],

        InventoryUpdatedEvent::class => [
            Listeners\InventoryUpdatedEventListener::class,
        ],

        WarehouseTagAttachedEvent::class => [
            Listeners\WarehouseTagAttachedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        ManualRequestJob::query()->upsert([
            'job_name' => 'Inventory Totals - EnsureInventoryTotalsByWarehouseTagRecordsExistJob',
            'job_class' => Jobs\EnsureInventoryTotalsByWarehouseTagRecordsExistJob::class,
        ], ['job_class'], ['job_name']);

        return true;
    }
}
