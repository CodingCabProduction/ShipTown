<?php

namespace App\Models;

use App\BaseModel;

/**
 * @property string $business_name
 * @property string $database_version
 * @property boolean $ecommerce_connected
 * @property boolean $disable_2fa
 *
 */
class Configuration extends BaseModel
{
    protected $fillable = [
        'business_name',
        'database_version',
        'ecommerce_connected',
        'disable_2fa'
    ];

    protected $casts = [
        'ecommerce_connected' => 'boolean',
        'disable_2fa' => 'boolean'
    ];
}
