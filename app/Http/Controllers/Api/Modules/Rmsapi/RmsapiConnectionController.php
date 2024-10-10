<?php

namespace App\Http\Controllers\Api\Modules\Rmsapi;

use App\Http\Controllers\Controller;
use App\Http\Resources\RmsapiConnectionResource;
use App\Modules\Rmsapi\src\Http\Requests\RmsapiConnectionDestroyRequest;
use App\Modules\Rmsapi\src\Http\Requests\RmsapiConnectionIndexRequest;
use App\Modules\Rmsapi\src\Http\Requests\RmsapiConnectionStoreRequest;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RmsapiConnectionController extends Controller
{
    public function index(RmsapiConnectionIndexRequest $request): AnonymousResourceCollection
    {
        return RmsapiConnectionResource::collection(RmsapiConnection::all());
    }

    public function store(RmsapiConnectionStoreRequest $request): RmsapiConnectionResource
    {
        $rmsapiConnection = RmsapiConnection::query()
            ->updateOrCreate(['id' => $request->get('id')], $request->validated());

        return new RmsapiConnectionResource($rmsapiConnection);
    }

    public function destroy(RmsapiConnectionDestroyRequest $request, RmsapiConnection $connection): Response
    {
        $connection->delete();

        return response('ok');
    }
}
