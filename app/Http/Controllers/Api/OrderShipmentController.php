<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderShipmentStoreRequest;
use App\Http\Resources\OrderShipmentResource;
use App\Models\OrderShipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderShipmentController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        $query = OrderShipment::getSpatieQueryBuilder();

        return $this->getPaginatedResult($query);
    }

    public function store(OrderShipmentStoreRequest $request): OrderShipmentResource
    {
        $shipment = new OrderShipment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return new OrderShipmentResource($shipment);
    }
}
