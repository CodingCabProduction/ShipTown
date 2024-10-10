<?php

namespace App\Models;

use App\Modules\DataCollectorPayments\src\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Models\DataCollectionPayment.
 *
 * @property int $id
 * @property int $payment_type_id
 * @property int $transaction_id
 * @property float $amount
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DataCollectionPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'payment_type_id',
        'transaction_id',
        'amount',
    ];

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(DataCollection::class);
    }

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(DataCollectionPayment::class)
            ->allowedSorts([
                'id',
                'payment_type_id',
                'transaction_id',
                'amount',
            ]);
    }
}
