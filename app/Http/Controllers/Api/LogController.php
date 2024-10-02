<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LogResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class LogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = QueryBuilder::for(Activity::class)
            ->allowedFilters([
                AllowedFilter::exact('subject_type'),
                AllowedFilter::exact('subject_id'),
            ])
            ->allowedIncludes([
                'causer',
            ])
            ->allowedSorts([
                'id',
                'created_at',
            ]);

        return LogResource::collection($this->getPaginatedResult($query));
    }
}
