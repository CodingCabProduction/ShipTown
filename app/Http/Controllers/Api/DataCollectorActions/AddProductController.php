<?php

namespace App\Http\Controllers\Api\DataCollectorActions;

use App\Http\Requests\Api\DataCollectorActions\AddProductStoreRequest;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Inventory;
use App\Models\ProductAlias;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class AddProductController
{
    public function store(AddProductStoreRequest $request): AnonymousResourceCollection
    {
        $dataCollectionRecord = $this->findOrCreateRecord($request);

        $field_name = $request->has('quantity_scanned') ? 'quantity_scanned' : 'quantity_requested';
        $dataCollectionRecord->increment($field_name, $request->validated($field_name, 0));

        return JsonResource::collection(Arr::wrap($dataCollectionRecord));
    }

    public function findOrCreateRecord(AddProductStoreRequest $request): DataCollectionRecord
    {
        $product_id = ProductAlias::query()->where(['alias' => $request->validated('sku_or_alias')])->first('product_id')->product_id;

        return DataCollectionRecord::query()->where([
                'data_collection_id' => $request->validated('data_collection_id'),
                'product_id' => $product_id,
            ])
            ->firstOr(function () use ($request, $product_id) {
                $warehouse_id = DataCollection::query()->find($request->validated('data_collection_id'), ['warehouse_id'])->warehouse_id;
                $inventory = Inventory::query()->where(['product_id' => $product_id, 'warehouse_id' => $warehouse_id])->first();

                return DataCollectionRecord::query()->create([
                    'data_collection_id' => $request->validated('data_collection_id'),
                    'inventory_id' => $inventory->id,
                    'warehouse_id' => $inventory->warehouse_id,
                    'warehouse_code' => $inventory->warehouse_code,
                    'product_id' => $inventory->product_id,
                    'quantity_requested' => 0,
                ]);
            });
    }
}
