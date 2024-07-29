<?php

namespace App\Modules\QuantityDiscounts\src\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @property int $id
 * @property int $quantity_discount_id
 * @property int $product_id
 *
 */
class QuantityDiscountsProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'modules_quantity_discounts_products';

    protected $fillable = [
        'quantity_discount_id',
        'product_id',
    ];

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(QuantityDiscountsProduct::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('quantity_discount_id'),
                AllowedFilter::exact('product_id'),
            ])
            ->allowedIncludes([
                'discount',
                'product'
            ]);
    }

    /**
     * @return HasOne
     */
    public function discount(): HasOne
    {
        return $this->hasOne(QuantityDiscount::class, 'id', 'quantity_discount_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
