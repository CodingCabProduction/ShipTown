<?php

namespace App\Modules\Reports\src\Models;

use App\Models\InventoryMovement;
use Spatie\QueryBuilder\AllowedFilter;

class InventoryMovementsReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Inventory Movements Report';
        $this->defaultSort = '-occurred_at';

        $this->baseQuery = InventoryMovement::query()
            ->leftJoin('products', 'products.id', '=', 'inventory_movements.product_id');

        $this->allowedIncludes = [
            'inventory',
            'product',
            'warehouse',
            'user',
            'product.tags'
        ];

        $this->fields = [
            'product_sku'                   => 'products.sku',
            'product_name'                  => 'products.name',
            'quantity_delta'                => 'inventory_movements.quantity_delta',
            'type'                          => 'inventory_movements.type',
            'quantity_before'               => 'inventory_movements.quantity_before',
            'quantity_after'                => 'inventory_movements.quantity_after',
            'warehouse_code'                => 'inventory_movements.warehouse_code',
            'occurred_at'                   => 'inventory_movements.occurred_at',
            'description'                   => 'inventory_movements.description',
            'sequence_number'               => 'inventory_movements.sequence_number',
            'id'                            => 'inventory_movements.id',
            'user_id'                       => 'inventory_movements.user_id',
            'product_id'                    => 'inventory_movements.product_id',
            'inventory_id'                  => 'inventory_movements.inventory_id',
            'warehouse_id'                  => 'inventory_movements.warehouse_id',
            'updated_at'                    => 'inventory_movements.updated_at',
            'created_at'                    => 'inventory_movements.created_at',
            'custom_unique_reference_id'    => 'inventory_movements.custom_unique_reference_id',
        ];

        $this->casts = [
            'id'                => 'integer',
            'user_id'           => 'integer',
            'product_id'        => 'integer',
            'sequence_number'   => 'integer',
            'occurred_at'       => 'datetime',
            'created_at'        => 'datetime',
            'quantity_delta'    => 'float',
            'quantity_before'   => 'float',
            'quantity_after'    => 'float',
        ];

        $this->addFilter(
            AllowedFilter::callback('product_has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->hasTags($value);
                });
            })
        );

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->withAllTags($value);
                });
            })
        );

        $this->addFilter(
            AllowedFilter::callback('search', function ($query, $value) {
                $query->whereHas('alias', function ($query) use ($value) {
                    $query->where(['alias' => $value]);
                });
            })
        );
    }
}
