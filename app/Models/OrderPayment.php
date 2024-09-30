<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderPayment extends BaseModel
{
    use HasFactory;

    protected $table = 'orders_payments';

    protected $guarded = [];

    protected $casts = [
        'additional_fields' => 'array',
        'paid_at' => 'datetime',
    ];

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(OrderPayment::class)
            ->allowedFilters([
                AllowedFilter::exact('order_id'),
            ])
            ->defaultSort('-id')
            ->allowedSorts([
                'id',
            ]);
    }
}
