<?php

namespace App\Modules\InventoryGroups\src\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'group_name',
        'recount_required',
        'total_quantity_in_stock',
        'total_quantity_reserved',
        'total_quantity_available',
        'total_quantity_incoming',
        'total_quantity_required',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
