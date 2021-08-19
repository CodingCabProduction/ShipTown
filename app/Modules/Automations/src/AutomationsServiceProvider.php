<?php

namespace App\Modules\Automations\src;

use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

class AutomationsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Automations';

    /**
     * @var string
     */
    public static string $module_description = 'Provides user managed automations';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderCreatedEvent::class => [
            Listeners\EventsListener::class
        ],

        OrderUpdatedEvent::class => [
            Listeners\EventsListener::class
        ],
    ];

    public function boot()
    {
        parent::boot();

        $this->mergeConfigFrom(__DIR__.'/../config/automations.php', 'automations');
    }
}