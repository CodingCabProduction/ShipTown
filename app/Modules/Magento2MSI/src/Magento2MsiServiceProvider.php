<?php

namespace App\Modules\Magento2MSI\src;

use App\Events\EveryDayEvent;
use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryHourEvent;
use App\Events\Inventory\RecalculateInventoryRequestEvent;
use App\Events\Product\ProductTagAttachedEvent;
use App\Events\Product\ProductTagDetachedEvent;
use App\Models\ManualRequestJob;
use App\Modules\BaseModuleServiceProvider;

class Magento2MsiServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'eCommerce - Magento 2 MSI';

    public static string $module_description = 'Module provides connectivity to Magento 2 API - Multi Source Inventory';

    public static string $settings_link = '/settings/modules/magento2msi';

    public static bool $autoEnable = false;

    protected $listen = [
        RecalculateInventoryRequestEvent::class => [
            Listeners\RecalculateInventoryRequestEventListener::class,
        ],

        EveryFiveMinutesEvent::class => [
            Listeners\EveryFiveMinutesEventListener::class
        ],

        EveryHourEvent::class => [
            Listeners\EveryHourEventListener::class
        ],

        ProductTagAttachedEvent::class => [
            Listeners\ProductTagAttachedEventListener::class,
        ],

        ProductTagDetachedEvent::class => [
            Listeners\ProductTagDetachedEventListener::class,
        ],

        EveryDayEvent::class => [
            Listeners\EveryDayEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - AssignInventorySourceJob',
            'job_class' => Jobs\AssignInventorySourceJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - CheckIfSyncIsRequiredJob',
            'job_class' => Jobs\CheckIfSyncIsRequiredJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - EnsureProductRecordsExistJob',
            'job_class' => Jobs\EnsureProductRecordsExistJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - FetchStockItemsJob',
            'job_class' => Jobs\FetchStockItemsJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - GetProductIdsJob',
            'job_class' => Jobs\GetProductIdsJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - SyncProductInventoryJob',
            'job_class' => Jobs\SyncProductInventoryJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - EnsureInventoryGroupIdIsNotNullJob',
            'job_class' => Jobs\EnsureProductRecordsExistJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - EnsureInventoryGroupIdIsNotNullJob',
            'job_class' => Jobs\RecheckIfProductsExistInMagentoJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - RemoveProductsWithoutRequiredTagJob',
            'job_class' => Jobs\RemoveProductsWithoutRequiredTagJob::class,
        ], ['job_class'], ['job_name']);

        return parent::enabling();
    }
}
