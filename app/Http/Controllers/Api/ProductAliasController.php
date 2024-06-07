<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAliasStoreRequest;
use App\Http\Resources\ProductAliasResource;
use App\Models\ProductAlias;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ProductAliasController.
 */
class ProductAliasController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $query = ProductAlias::getSpatieQueryBuilder();

        return ProductAliasResource::collection($this->getPaginatedResult($query));
    }

    public function store(ProductAliasStoreRequest $request)
    {
        $product = ProductAlias::query()->updateOrCreate(['alias' => $request->validated('alias')], $request->validated());

        return ProductAliasResource::make($product);
    }
}
