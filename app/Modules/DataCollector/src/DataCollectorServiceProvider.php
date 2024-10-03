<?php

namespace App\Modules\DataCollector\src;

use App\Events\DataCollectionPayment\DataCollectionPaymentCreatedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordDeletedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\DataCollector\src\Jobs\DispatchCollectionsTasksJob;

/**
 * Class InventoryQuantityReservedServiceProvider.
 */
class DataCollectorServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Data Collector - Transform Actions';

    public static string $module_description = 'Provides bulk actions for data collections such a Transfer In, Transfer Out, Transfer To and Import As Stocktake';

    public static string $settings_link = '/modules/data-collector';

    public static bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        DataCollectionRecordCreatedEvent::class => [
            Listeners\DataCollectionRecordCreatedEventListener::class,
        ],

        DataCollectionRecordUpdatedEvent::class => [
            Listeners\DataCollectionRecordUpdatedEventListener::class,
        ],

        DataCollectionRecordDeletedEvent::class => [
            Listeners\DataCollectionRecordDeletedEventListener::class,
        ],

        DataCollectionPaymentCreatedEvent::class => [
            \App\Modules\DataCollectorPayments\src\Listeners\DataCollectionPaymentCreatedEventListener::class,
        ],

        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        EveryTenMinutesEvent::class => [
            Listeners\EveryTenMinutesEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        DispatchCollectionsTasksJob::dispatch();

        return parent::enabling();
    }
}
