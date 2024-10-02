<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProductShipment\StoreRequest;
use App\Http\Resources\OrderProductShipmentResource;
use App\Models\OrderProduct;
use App\Models\OrderProductShipment;

class OrderProductShipmentController extends Controller
{
    public function store(StoreRequest $request): ?OrderProductShipmentResource
    {
        try {
            $orderProductShipment = new OrderProductShipment;

            app('db')->transaction(function () use ($request, &$orderProductShipment) {
                /** @var OrderProduct $orderProduct */
                $orderProduct = OrderProduct::where(['id' => $request->get('order_product_id')])
                    ->lockForUpdate()
                    ->first();

                $orderProductShipment->fill($request->validated());
                $orderProductShipment->user_id = $request->user()->getKey();
                $orderProductShipment->warehouse_id = $orderProductShipment->user->warehouse_id;

                if ($orderProduct->quantity_to_ship < $orderProductShipment->quantity_shipped) {
                    throw new \Exception('Incorrect quantity shipped');
                }

                $orderProductShipment->save();

                $orderProduct->update([
                    'quantity_shipped' => $orderProduct->quantity_shipped + $request->get('quantity_shipped', 0),
                ]);
            });

            return new OrderProductShipmentResource($orderProductShipment);
        } catch (\Exception|\Throwable $e) {
            report($e);
            $this->respondBadRequest($e->getMessage());

            return null;
        }
    }
}
