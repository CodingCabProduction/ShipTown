<?php

namespace App\Modules\QuantityDiscounts\src\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer id
 * @property string name
 * @property string type
 * @property string job_class
 * @property array configuration
 * @property string deleted_at
 * @property string updated_at
 * @property string created_at
 *
 */
class QuantityDiscount extends Model
{
    use SoftDeletes;

    protected $table = 'modules_quantity_discounts';

    protected $fillable = [
        'name',
        'type',
        'job_class',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
    ];
}
