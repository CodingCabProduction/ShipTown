<?php

namespace App\Http\Controllers\Api\Modules\Printnode;

use App\Http\Controllers\Controller;
use App\Modules\PrintNode\src\Http\Requests\ClientDestroyRequest;
use App\Modules\PrintNode\src\Http\Requests\ClientIndexRequest;
use App\Modules\PrintNode\src\Http\Requests\ClientStoreRequest;
use App\Modules\PrintNode\src\Models\Client;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\PrintNode\src\Resources\PrintNodeClientResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(ClientIndexRequest $request): AnonymousResourceCollection
    {
        return PrintNodeClientResource::collection($this->getPaginatedResult(Client::getSpatieQueryBuilder()));
    }

    public function store(ClientStoreRequest $request): PrintNodeClientResource
    {
        $printNodeClient = Client::query()->firstOrNew([], []);
        $printNodeClient->fill($request->validated());

        if (! PrintNode::noop($printNodeClient)) {
            abort(400, 'NOOP call failed');
        }

        $printNodeClient->save();

        return PrintNodeClientResource::make($printNodeClient);
    }

    public function destroy(ClientDestroyRequest $request, Client $client)
    {
        $client->delete();

        return PrintNodeClientResource::make($client);
    }
}
