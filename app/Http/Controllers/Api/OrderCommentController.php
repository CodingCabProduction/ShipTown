<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCommentStoreRequest;
use App\Http\Resources\OrderCommentResource;
use App\Models\OrderComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderCommentController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        $query = OrderComment::getSpatieQueryBuilder();

        return $this->getPaginatedResult($query);
    }

    public function store(OrderCommentStoreRequest $request): AnonymousResourceCollection
    {
        $shipment = new OrderComment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return OrderCommentResource::collection(collect([$shipment]));
    }
}
