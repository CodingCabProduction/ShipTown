<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderPaymentResource;
use App\Models\OrderPayment;
use Illuminate\Http\Request;

class OrderPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = OrderPayment::getSpatieQueryBuilder();

        return OrderPaymentResource::collection($this->getPaginatedResult($query));
    }
}
