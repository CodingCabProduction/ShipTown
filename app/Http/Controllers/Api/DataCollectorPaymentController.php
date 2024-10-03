<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataCollectionPayment\IndexRequest;
use App\Http\Requests\DataCollectionPayment\StoreRequest;
use App\Http\Requests\DataCollectionPayment\UpdateRequest;
use App\Http\Resources\DataCollectionPaymentResource;
use App\Http\Resources\PaymentTypeResource;
use App\Models\DataCollectionPayment;

class DataCollectorPaymentController extends Controller
{
    public function index(IndexRequest $request)
    {
        $query = DataCollectionPayment::getSpatieQueryBuilder()->defaultSort('id');

        return PaymentTypeResource::collection($this->getPaginatedResult($query, 999));
    }

    public function store(StoreRequest $request): DataCollectionPaymentResource
    {
        $discount = DataCollectionPayment::create($request->validated());

        return DataCollectionPaymentResource::make($discount);
    }

    public function update(UpdateRequest $request, int $id)
    {
        $payment = DataCollectionPayment::findOrFail($id);
        $payment->update($request->validated());

        return DataCollectionPaymentResource::make($payment->refresh());
    }
}
