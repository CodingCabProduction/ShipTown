<?php

namespace App\Modules\QuantityDiscounts\src\Models;

use App\Traits\LogsActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
    use HasFactory;
    use LogsActivityTrait;
    use SoftDeletes;

    protected $table = 'modules_quantity_discounts';

    protected $fillable = [
        'name',
        'type',
        'job_class',
        'configuration',
    ];

    protected $casts = [
        'id' => 'integer',
        'configuration' => 'array',
    ];

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(QuantityDiscount::class)
            ->allowedFilters([
                AllowedFilter::scope('search', 'whereHasText')
            ])
            ->allowedSorts([
                'id',
                'name',
                'type'
            ])
            ->allowedIncludes([
                'products'
            ]);
    }

    /**
     * @param mixed $query
     * @param string $text
     *
     * @return mixed
     */
    public function scopeWhereHasText(mixed $query, string $text): mixed
    {
        return $query->where('name', $text)
            ->orWhere('type', $text)
            ->orWhere('name', 'like', '%'.$text.'%')
            ->orWhere('type', 'like', '%'.$text.'%');
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(QuantityDiscountsProduct::class);
    }
}
