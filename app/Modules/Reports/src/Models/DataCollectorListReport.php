<?php

namespace App\Modules\Reports\src\Models;

use App\Models\DataCollection;
use App\Models\DataCollectionStocktake;
use App\Models\DataCollectionTransaction;
use App\Models\DataCollectionTransferIn;
use App\Models\DataCollectionTransferOut;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class DataCollectorListReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Data Collector List';

        $this->baseQuery = DataCollection::query()
            ->leftJoin('warehouses', 'warehouses.id', '=', 'data_collections.warehouse_id');

        $differences_count_subquery = DB::raw('(
                SELECT count(*)
                FROM data_collection_records as dcr
                WHERE dcr.data_collection_id = data_collections.id
                AND IFNULL(dcr.quantity_requested, 0) != (dcr.total_transferred_in + dcr.total_transferred_out)
            )');

        $this->fields = [
            'id' => 'data_collections.id',
            'warehouse_code' => 'warehouses.code',
            'warehouse_name' => 'warehouses.name',
            'warehouse_id' => 'warehouse_id',
            'type' => 'data_collections.type',
            'name' => 'data_collections.name',
            'recount_required' => 'data_collections.recount_required',
            'calculated_at' => 'data_collections.calculated_at',
            'destination_warehouse_id' => 'data_collections.destination_warehouse_id',
            'destination_collection_id' => 'data_collections.destination_collection_id',
            'shipping_address_id' => 'data_collections.shipping_address_id',
            'billing_address_id' => 'data_collections.billing_address_id',
            'total_quantity_scanned' => 'data_collections.total_quantity_scanned',
            'total_cost' => 'data_collections.total_cost',
            'total_full_price' => 'data_collections.total_full_price',
            'total_discount' => 'data_collections.total_discount',
            'total_sold_price' => 'data_collections.total_sold_price',
            'total_profit' => 'data_collections.total_profit',
            'total_paid' => 'data_collections.total_paid',
            'total_outstanding' => 'data_collections.total_outstanding',
            'custom_uuid' => 'data_collections.custom_uuid',
            'differences_count' => $differences_count_subquery,
            'created_at' => 'data_collections.created_at',
            'updated_at' => 'data_collections.updated_at',
            'deleted_at' => 'data_collections.deleted_at',
            'currently_running_task' => 'data_collections.currently_running_task',
        ];

        $this->casts = [
            'id' => 'integer',
            'warehouse_id' => 'warehouse_id',
            'type' => 'string',
            'name' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'warehouse_code' => 'string',
            'warehouse_name' => 'string',
            'currently_running_task' => 'string',
        ];

        $this->addFilter(
            AllowedFilter::callback('with_archived', function ($query, $value) {
                if ($value === true) {
                    $query->withTrashed();
                }
            })
        );

        $this->addFilter(
            AllowedFilter::callback('only_archived', function ($query, $value) {
                if ($value === true) {
                    $query->onlyTrashed();
                }
            })
        );

        $this->addFilter(
            AllowedFilter::callback('without_transactions', function (Builder $query, $value) {
                if ($value === true) {
                    $query->where(function (Builder $query) {
                        $query->whereNotIn('data_collections.type', [
                                DataCollectionTransferIn::class,
                                DataCollectionTransferOut::class,
                                DataCollectionStocktake::class,
                            ])
                            ->orWhereNull('data_collections.type');
                    });
                }
            })
        );

        $this->addAllowedInclude('comments');
        $this->addAllowedInclude('comments.user');
        $this->addAllowedInclude('shippingAddress');
        $this->addAllowedInclude('billingAddress');
        $this->addAllowedInclude('payments');
    }
}
