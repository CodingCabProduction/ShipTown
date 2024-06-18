<?php

namespace App\Models;

use App\Helpers\HasQuantityRequiredSort;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 *  @property int    $id
 *  @property int    $data_collection_id,
 *  @property int    $inventory_id
 *  @property int    $product_id
 *  @property double $total_transferred_in
 *  @property double $total_transferred_out
 *  @property double $quantity_requested
 *  @property double $quantity_scanned
 *  @property double $quantity_to_scan
 *  @property bool   $is_scanned
 *  @property Carbon $created_at
 *  @property Carbon $updated_at
 *
 *  @method static Builder|Product skuOrAlias($skuOrAlias)
 *
 *  @property-read Product $product
 *  @property-read DataCollection $dataCollection
 *  @property-read Inventory $inventory
 */
class DataCollectionRecord extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'data_collection_id',
        'inventory_id',
        'warehouse_id',
        'product_id',
        'total_transferred_in',
        'total_transferred_out',
        'quantity_requested',
        'quantity_scanned',
        'custom_uuid',
    ];

    protected $casts = [
        'product_id'            => 'int',
        'total_transferred_id'  => 'double',
        'total_transferred_out' => 'double',
        'quantity_requested'    => 'double',
        'quantity_scanned'      => 'double',
        'quantity_to_scan'      => 'double',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function dataCollection(): BelongsTo
    {
        return $this->belongsTo(DataCollection::class)->withTrashed();
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        $allowedSort = AllowedSort::custom('has_quantity_required', new HasQuantityRequiredSort());

        return QueryBuilder::for(DataCollectionRecord::class)
            ->allowedFilters([])
            ->allowedSorts([
                'id',
                'quantity_requested',
                'quantity_scanned',
                'quantity_to_scan',
                'updated_at',
                'created_at',
            ])
            ->allowedIncludes([
                'product',
                'inventory',
                'product.inventory',
                'product.user_inventory',
            ])
            ->defaultSort($allowedSort, '-updated_at');
    }

    public function scopeSkuOrAlias($query, string $value)
    {
        $query->where(function ($query) use ($value) {
            return $query
                ->whereIn('data_collection_records.product_id', ProductAlias::query()
                    ->select('products_aliases.product_id')
                    ->where('alias', $value));
        });

        return $query;
    }
}
