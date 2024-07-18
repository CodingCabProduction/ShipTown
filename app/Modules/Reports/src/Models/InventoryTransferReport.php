<?php

namespace App\Modules\Reports\src\Models;

use App\Models\DataCollectionRecord;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class InventoryTransferReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Inventory Transfers';

        $this->baseQuery = DataCollectionRecord::query()
            ->leftJoin('data_collections', 'data_collections.id', '=', 'data_collection_records.data_collection_id')
            ->leftJoin('products', 'products.id', '=', 'data_collection_records.product_id')
            ->leftJoin('products_prices', function ($join) {
                $join->on('products_prices.product_id', '=', 'data_collection_records.product_id')
                    ->whereColumn('products_prices.warehouse_id', 'data_collections.warehouse_id');
            })
            ->leftJoin('warehouses', 'warehouses.id', '=', 'data_collections.warehouse_id');

        $this->allowedIncludes = [
            'product',
            'product.tags',
            'product.aliases',
            'dataCollection',
            'inventory',
            'products_prices',
        ];

        $this->defaultSelects = [
            'product_sku',
            'product_name',
            'total_transferred_in',
            'product_cost' ,
            'warehouse_code',
            'department',
            'category',
            'transfer_name',
            'updated_at'
        ];

        $this->fields = [
            'product_sku'           => 'products.sku',
            'product_name'          => 'products.name',
            'total_transferred_in'  => 'data_collection_records.total_transferred_in',
            'product_cost'          => 'products_prices.cost',
            'department'            => 'products.department',
            'category'              => 'products.category',
            'warehouse_code'        => 'warehouses.code',
            'transfer_name'         => 'data_collections.name',
            'updated_at'            => 'data_collection_records.updated_at',
        ];

        $this->casts = [
            'warehouse_code'        => 'string',
            'department'            => 'string',
            'category'              => 'string',
            'transfer_name'         => 'string',
            'product_sku'           => 'string',
            'product_name'          => 'string',
            'product_cost'          => 'float',
            'total_transferred_in'  => 'float',
            'updated_at'            => 'datetime',
        ];

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->withAllTags($value);
                });
            })
        );

        $this->addFilter(
            AllowedFilter::scope('sku_or_alias', 'skuOrAlias'),
        );
    }
}
