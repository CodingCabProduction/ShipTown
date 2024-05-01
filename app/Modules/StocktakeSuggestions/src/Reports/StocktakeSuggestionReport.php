<?php

namespace App\Modules\StocktakeSuggestions\src\Reports;

use App\Models\StocktakeSuggestion;
use App\Modules\Reports\src\Models\Report;
use Spatie\QueryBuilder\AllowedFilter;

class StocktakeSuggestionReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Stocktake Suggestions';

        $this->addField('warehouse_code', 'inventory.warehouse_code', hidden: false);
        $this->addField('product_sku', 'products.sku', hidden: false);
        $this->addField('product_name', 'products.name', hidden: false);
        $this->addField('quantity_in_stock', 'inventory.quantity', hidden: false);
        $this->addField('reason', 'stocktake_suggestions.reason', hidden: false);
        $this->addField('points', 'stocktake_suggestions.points', hidden: false);
        $this->addField('last_movement_at', 'inventory.last_movement_at', 'datetime', hidden: false);
        $this->addField('last_counted_at', 'inventory.last_counted_at', 'datetime', hidden: false);
        $this->addField('last_sold_at', 'inventory.last_sold_at', 'datetime', hidden: false);
        $this->addField('in_stock_since', 'inventory.in_stock_since', 'datetime');
        $this->addField('inventory_id', 'stocktake_suggestions.inventory_id');
        $this->addField('product_id', 'stocktake_suggestions.product_id');
        $this->addField('warehouse_id', 'stocktake_suggestions.warehouse_id');

        $this->defaultSort = 'points';
        $this->allowedIncludes = [
            'product',
            'product.tags',
            'product.prices',
            'inventory',
        ];

        $this->baseQuery = StocktakeSuggestion::query()
            ->leftJoin('inventory', 'inventory.id', '=', 'stocktake_suggestions.inventory_id')
            ->leftJoin('products', 'products.id', '=', 'stocktake_suggestions.product_id');

        $this->addFilter(
            AllowedFilter::callback('product_has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->hasTags($value);
                });
            })
        );

        $this->addFilter(
            AllowedFilter::callback('search', function ($query, $value) {
                $query->where(function ($query) use ($value) {
                    $query->where('products.sku', 'like', '%'.$value.'%')
                        ->orWhere('products.name', 'like', '%'.$value.'%');
                });
            })
        );
    }
}
