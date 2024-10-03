<?php

namespace App\Modules\DataCollectorPayments\src\Models;

use App\Models\DataCollectionPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Models\PaymentType.
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PaymentType extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(DataCollectionPayment::class);
    }

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(PaymentType::class)
            ->allowedSorts([
                'id',
                'code',
                'name',
            ]);
    }
}
