<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProducts\UpdateRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $query = OrderProduct::getSpatieQueryBuilder();

        return OrderProductResource::collection($this->getPaginatedResult($query));
    }

    public function update(UpdateRequest $request, OrderProduct $product): OrderProductResource
    {
        $product->update($request->validated());

        return OrderProductResource::make($product);
    }
}
