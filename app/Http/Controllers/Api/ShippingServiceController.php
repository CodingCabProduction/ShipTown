<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ShippingCourierResource;
use App\Models\ShippingService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShippingServiceController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $query = ShippingService::getSpatieQueryBuilder();

        return ShippingCourierResource::collection($this->getPaginatedResult($query));
    }
}
