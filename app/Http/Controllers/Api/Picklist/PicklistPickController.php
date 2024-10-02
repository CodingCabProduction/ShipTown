<?php

namespace App\Http\Controllers\Api\Picklist;

use App\Http\Controllers\Controller;
use App\Http\Requests\PickDestroyRequest;
use App\Http\Requests\Picklist\StorePickRequest;
use App\Models\OrderProduct;
use App\Models\Pick;
use App\Modules\Picklist\src\Jobs\UnDistributeDeletedPicksJob;
use Illuminate\Http\Resources\Json\JsonResource;

class PicklistPickController extends Controller
{
    public function store(StorePickRequest $request): JsonResource
    {
        $orderProducts = OrderProduct::query()
            ->whereIn('id', $request->get('order_product_ids'))
            ->oldest('order_id')
            ->get();

        $first = $orderProducts->first();

        /** @var Pick $pick */
        $pick = Pick::query()->create([
            'is_distributed' => false,
            'user_id' => request()->user()->getKey(),
            'warehouse_code' => request()->user()->warehouse->code,
            'product_id' => $first['product_id'],
            'sku_ordered' => $first['sku_ordered'],
            'name_ordered' => $first['name_ordered'],
            'quantity_picked' => $request->validated('quantity_picked', 0),
            'quantity_skipped_picking' => $request->validated('quantity_skipped_picking', 0),
            'order_product_ids' => $request->validated('order_product_ids'),
        ]);

        return JsonResource::make([$pick]);
    }

    public function destroy(PickDestroyRequest $request, Pick $pick): JsonResource
    {
        $pick->delete();

        UnDistributeDeletedPicksJob::dispatchAfterResponse();

        return JsonResource::make([$pick]);
    }
}
