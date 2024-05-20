<?php

namespace App\Modules\InventoryTotals\src\Models;

use App\BaseModel;

/**
 * @property double $quantity
 * @property double $quantity_reserved
 * @property double $quantity_incoming
 * @property double $quantity_available
 */
class InventoryTotalByWarehouseTag extends BaseModel
{
    protected $table = 'inventory_totals_by_warehouse_tag';

    protected $fillable = [
        'tag_id',
        'product_id',
        'recalc_required',
        'quantity',
        'quantity_reserved',
        'quantity_incoming',
        'max_inventory_updated_at',
        'calculated_at',
    ];

    protected $casts = [
        'recalc_required' => 'boolean',
    ];
}
