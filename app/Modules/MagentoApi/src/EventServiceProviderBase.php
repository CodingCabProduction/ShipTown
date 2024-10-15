<?php

namespace App\Modules\MagentoApi\src;

use App\Events\EveryDayEvent;
use App\Events\EveryHourEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
use App\Events\Product\ProductPriceUpdatedEvent;
use App\Events\Product\ProductTagAttachedEvent;
use App\Events\Product\ProductTagDetachedEvent;
use App\Models\ManualRequestJob;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class InventoryQuantityReservedServiceProvider.
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    public static string $module_name = 'eCommerce - Magento 2.0 API Price Sync';

    public static string $module_description = 'Module provides ability to sync full & special prices with Magento 2';

    public static string $settings_link = '/settings/modules/magento-api';

    public static bool $autoEnable = false;

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

        EveryHourEvent::class => [
            Listeners\EveryHourEventListener::class,
        ],

        EveryDayEvent::class => [
            Listeners\EveryDayEventListener::class,
        ],

        ProductTagAttachedEvent::class => [
            Listeners\ProductTagAttachedEventListener::class,
        ],

        ProductTagDetachedEvent::class => [
            Listeners\ProductTagDetachedEventListener::class,
        ],

        ProductPriceUpdatedEvent::class => [
            Listeners\ProductPriceUpdatedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2.0 API Price Sync - CheckIfSyncIsRequiredJob',
            'job_class' => Jobs\CheckIfSyncIsRequiredJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2.0 API Price Sync - EnsureProductPriceIdIsFilledJob',
            'job_class' => Jobs\EnsureProductPriceIdIsFilledJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2.0 API Price Sync - EnsureProductRecordsExistJob',
            'job_class' => Jobs\EnsureProductRecordsExistJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2.0 API Price Sync - FetchBasePricesJob',
            'job_class' => Jobs\FetchBasePricesJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2.0 API Price Sync - FetchSpecialPricesJob',
            'job_class' => Jobs\FetchSpecialPricesJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2.0 API Price Sync - SyncProductBasePricesJob',
            'job_class' => Jobs\SyncProductBasePricesJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2.0 API Price Sync - SyncProductBasePricesBulkJob',
            'job_class' => Jobs\SyncProductBasePricesBulkJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2.0 API Price Sync - SyncProductSalePricesBulkJob',
            'job_class' => Jobs\SyncProductSalePricesBulkJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2.0 API Price Sync - SyncProductSalePricesJob',
            'job_class' => Jobs\SyncProductSalePricesJob::class,
        ], ['job_class'], ['job_name']);

        return parent::enabling();
    }

    public static function disabling(): bool
    {
        ManualRequestJob::query()->whereIn('job_class', [
            Jobs\CheckIfSyncIsRequiredJob::class,
            Jobs\EnsureProductPriceIdIsFilledJob::class,
            Jobs\EnsureProductRecordsExistJob::class,
            Jobs\FetchBasePricesJob::class,
            Jobs\FetchSpecialPricesJob::class,
            Jobs\SyncProductBasePricesJob::class,
            Jobs\SyncProductSalePricesJob::class,
        ])->delete();

        return parent::disabling();
    }
}
