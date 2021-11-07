<?php

namespace App\Modules\BoxTop\src;

use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class BoxTopServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'BoxTop API Integration';

    /**
     * @var string
     */
    public static string $module_description = 'Integrates with https://api.boxtrax.com/Help';

    /**
     * @var bool
     */
    public bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

    ];
}