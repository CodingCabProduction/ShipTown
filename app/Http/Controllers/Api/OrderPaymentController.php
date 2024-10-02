<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderPaymentResource;
use App\Models\OrderPayment;

class OrderPaymentController extends Controller
{
    public function index()
    {
        $query = OrderPayment::getSpatieQueryBuilder();

        return OrderPaymentResource::collection($this->getPaginatedResult($query));
    }
}
