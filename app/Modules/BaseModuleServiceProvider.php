<?php

namespace App\Modules;

use App\Models\Module;
use Exception;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class BaseModuleServiceProvider.
 */
abstract class BaseModuleServiceProvider extends EventServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '';

    /**
     * @var string
     */
    public static string $module_description = '';

    /**
     * @var string
     */
    public static string $settings_link = '';

    /**
     * Should we automatically enable it
     * When module first registered.
     *
     * @var bool
     */
    public static bool $autoEnable = false;

    /**
     * Register any events for your application.
     *
     * @throws Exception
     *
     * @return void
     */
    public function boot()
    {
        if (empty(static::$module_name) or empty(static::$module_description)) {
            throw new Exception('Module "'.get_called_class().'" missing name or description');
        }

        parent::boot();
    }

    public static function loaded(): bool
    {
        // this method is fired when module booted and enabled
        // return false if you want to prevent loading
        return true;
    }

    public static function installing(): bool
    {
        // this method is fired when module is being installed
        // return false if you want to prevent installing
        return true;
    }

    public static function enabling(): bool
    {
        // this method is fired when module is being enabled
        // return false if you want to prevent enabling
        return true;
    }

    public static function disabling(): bool
    {
        // this method is fired when module is being disabled
        // return false if you want to prevent disabling
        return true;
    }

    /**
     * @return bool
     */
    public static function isEnabled(): bool
    {
        try {
            $module = Module::firstOrCreate([
                'service_provider_class' => get_called_class(),
            ], [
                'enabled' => self::$autoEnable,
            ]);

            return $module->enabled;
        } catch (Exception $exception) {
            report($exception);
            return false;
        }
    }

    public static function isDisabled(): bool
    {
        return ! self::isEnabled();
    }

    /**
     * @return bool
     */
    public static function enableModule(): bool
    {
        $module = Module::firstOrCreate(['service_provider_class' => get_called_class()], ['enabled' => false]);

        if ($module->enabled) {
            return true;
        }

        $module->enabled = true;

        if (! $module->save()) {
            return false;
        }

        app()->singleton(get_called_class(), get_called_class());

        App::register(get_called_class());

        get_called_class()::loaded();

        return true;
    }

    /**
     * @return bool
     */
    public static function uninstallModule(): bool
    {
        try {
            /** @var Module $module */
            $module = Module::query()->where(['service_provider_class' => get_called_class()])->first();

            $module?->forceDelete();

            return true;
        } catch (Exception $exception) {
            report($exception);
            return false;
        }
    }

    /**
     * @return bool
     */
    public static function installModule(): bool
    {
        try {
            /** @var BaseModuleServiceProvider $moduleServiceProvider */
            $moduleServiceProvider = get_called_class();

            if (!$moduleServiceProvider::installing()) {
                return false;
            }

            Module::query()->firstOrCreate([
                'service_provider_class' => $moduleServiceProvider
            ], [
                'enabled' => $moduleServiceProvider::$autoEnable,
            ]);

            return true;
        } catch (Exception $exception) {
            Log::emergency($exception);
            return false;
        }
    }

    public static function disableModule(): void
    {
        $module = Module::query()->firstOrCreate([
            'service_provider_class' => get_called_class()
        ], [
            'enabled' => false
        ]);

        if ($module->enabled) {
            $module->update(['enabled' => false]);
        }
    }
}
