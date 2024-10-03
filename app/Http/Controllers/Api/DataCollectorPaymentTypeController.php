<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentType\IndexRequest;
use App\Http\Requests\PaymentType\StoreRequest;
use App\Http\Resources\PaymentTypeResource;
use App\Modules\DataCollectorPayments\src\Models\PaymentType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DataCollectorPaymentTypeController extends Controller
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $query = PaymentType::getSpatieQueryBuilder()->defaultSort('name');

        return PaymentTypeResource::collection($this->getPaginatedResult($query, 999));
    }

    public function store(StoreRequest $request): PaymentTypeResource
    {
        $paymentType = PaymentType::create($request->validated());

        return new PaymentTypeResource($paymentType);
    }

    public function destroy(int $payment_type_id): PaymentTypeResource
    {
        $paymentType = PaymentType::findOrFail($payment_type_id);
        $paymentType->delete();

        return PaymentTypeResource::make($paymentType);
    }
}
