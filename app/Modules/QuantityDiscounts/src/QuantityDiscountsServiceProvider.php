<?php

namespace App\Modules\QuantityDiscounts\src;

use App\Modules\BaseModuleServiceProvider;

class QuantityDiscountsServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'eCommerce - Quantity Discounts';

    public static string $module_description = 'Module provides an ability to create quantity discounts for products';

    public static string $settings_link = '/admin/settings/modules/quantity-discounts';

    public static bool $autoEnable = false;

    public static function enabling(): bool
    {
        return parent::enabling();
    }
}
