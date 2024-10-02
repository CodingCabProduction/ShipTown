<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatus\StoreRequest;
use App\Http\Requests\OrderStatus\UpdateRequest;
use App\Http\Requests\OrderStatusDestroyRequest;
use App\Http\Requests\OrderStatusIndexRequest;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class OrderStatusController extends Controller
{
    public function index(OrderStatusIndexRequest $request): AnonymousResourceCollection
    {
        $query = OrderStatus::getSpatieQueryBuilder();

        return OrderStatusResource::collection($this->getPaginatedResult($query));
    }

    public function store(StoreRequest $request): OrderStatusResource
    {
        $orderStatus = OrderStatus::where(['code' => $request->validated()['code']])->onlyTrashed()->first();

        if ($orderStatus) {
            $orderStatus->restore();

            return OrderStatusResource::make($orderStatus);
        }

        $this->validate($request, ['code' => 'unique:orders_statuses,code']);

        $orderStatus = OrderStatus::create($request->validated());

        return OrderStatusResource::make($orderStatus);
    }

    public function update(UpdateRequest $request, int $order_status_id): OrderStatusResource
    {
        $orderStatus = OrderStatus::findOrFail($order_status_id);

        $orderStatus->update($request->validated());

        return OrderStatusResource::make($orderStatus);
    }

    public function destroy(OrderStatusDestroyRequest $request, int $order_status_id): OrderStatusResource
    {
        $orderStatus = OrderStatus::findOrFail($order_status_id);

        if ($orderStatus->order_active || $orderStatus->sync_ecommerce) {
            abort(401, 'This order statuses cannot archived');
        }

        $orderStatus->delete();

        return OrderStatusResource::make($orderStatus);
    }
}
