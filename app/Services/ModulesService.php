<?php

namespace App\Services;

use App\Models\Module;
use App\Modules\BaseModuleServiceProvider;

class ModulesService
{
    private static array $modules = [
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public static function updateModulesTable(): void
    {
        Module::query()->whereNotIn('service_provider_class', self::$modules)->forceDelete();

        collect(self::$modules)->each(function (BaseModuleServiceProvider|string $module_class) {
            Module::firstOrCreate([
                'service_provider_class' => $module_class
            ], [
                'enabled' => $module_class::$autoEnable,
            ]);

            if ($module_class::$autoEnable) {
                $module_class::enableModule();
            }
        });
    }
}
