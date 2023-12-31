<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 *
 * @property int         $id
 * @property int         $order_id
 * @property string      $shipping_number
 * @property string      $carrier
 * @property string      $service
 * @property string      $tracking_url
 * @property int|null    $user_id
 * @property string|null $base64_pdf_labels
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class ShippingLabel extends Model
{
    use HasFactory;

    protected $table = 'orders_shipments';

    /**
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'carrier',
        'service',
        'shipping_number',
        'tracking_url',
        'base64_pdf_labels',
    ];

    // we use attributes to set default values
    // we won't use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'base64_pdf_labels' => ''
    ];

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(ShippingLabel::class)
            ->allowedFilters([
                    AllowedFilter::partial('shipping_number'),
                    AllowedFilter::exact('order.status_code'),
                    AllowedFilter::exact('user_id'),
                    AllowedFilter::exact('order_id'),

                    AllowedFilter::scope('age_in_days_between', 'whereAgeInDaysBetween'),
                    'created_at',
                    'updated_at',
                ])
                ->allowedIncludes([
                    'order',
                    'user',
                ])
                ->defaultSort('-id')
                ->allowedSorts([
                    'id',
                ]);
    }

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
