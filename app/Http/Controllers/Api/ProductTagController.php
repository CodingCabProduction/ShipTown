<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Taggable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\QueryBuilder;

class ProductTagController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $query = QueryBuilder::for(Taggable::class)
            ->allowedSorts(['created_at', 'updated_at', 'tag.name'])
            ->allowedFilters(['taggable_type', 'taggable_id'])
            ->allowedIncludes(['tag']);

        return JsonResource::collection($this->getPaginatedResult($query));
    }
}
