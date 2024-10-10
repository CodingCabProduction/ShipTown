<?php

namespace App\Modules\DataCollectorPayments\src;

use App\Events\DataCollectionPayment\DataCollectionPaymentCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

class DataCollectorPaymentsServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Data Collector - Payments';

    public static string $module_description = 'Module provides an ability to add payment for the transaction.';

    public static string $settings_link = '/settings/modules/data-collector-payments';

    public static bool $autoEnable = true;

    protected $listen = [
        DataCollectionPaymentCreatedEvent::class => [
            Listeners\DataCollectionPaymentCreatedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        return parent::enabling();
    }
}
