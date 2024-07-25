<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataCollectorStoreRequest;
use App\Http\Resources\DataCollectionRecordResource;
use App\Models\DataCollectionRecord;
use App\Modules\Reports\src\Models\DataCollectionReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DataCollectorRecordController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $report = new DataCollectionReport();

        $resource = $report->queryBuilder()
            ->orderByRaw('
                data_collection_records.is_fully_scanned ASC,
                data_collection_records.is_requested ASC,
                (data_collection_records.quantity_scanned > 0) ASC,
                data_collection_records.is_over_scanned ASC,
                shelf_location ASC,
                data_collection_records.quantity_to_scan DESC,
                data_collection_records.id DESC
            ')
            ->simplePaginate(request()->get('per_page', 10))
            ->appends(request()->query());

        return JsonResource::collection($resource);
    }

    public function store(DataCollectorStoreRequest $request): DataCollectionRecordResource
    {
        /** @var DataCollectionRecord $collectionRecord */
        $collectionRecord = DataCollectionRecord::query()->firstOrCreate([
                'data_collection_id' => $request->validated('data_collection_id'),
                'inventory_id' => $request->validated('inventory_id'),
            ], [
                'product_id' => $request->validated('product_id'),
                'warehouse_code' => $request->validated('warehouse_code'),
            ]);

        $collectionRecord->update([
            'unit_cost' => data_get($collectionRecord, 'prices.cost'),
            'unit_full_price' => data_get($collectionRecord, 'prices.price'),
            'unit_sold_price' => data_get($collectionRecord, 'prices.current_price'),
            'price_source' => data_get($collectionRecord, 'prices.price') === data_get($collectionRecord, 'prices.current_price') ? 'FULL_PRICE' : 'SALE_PRICE',
            'quantity_scanned' => $collectionRecord->quantity_scanned + $request->validated('quantity_scanned', 0),
            'quantity_requested' => $collectionRecord->quantity_requested + $request->validated('quantity_requested', 0),
        ]);

        return DataCollectionRecordResource::make($collectionRecord);
    }
}
