<?php

namespace App\Http\Controllers\Api\Modules\Printnode;

use App\Exceptions\ShippingServiceException;
use App\Http\Controllers\Controller;
use App\Modules\PrintNode\src\Http\Requests\PrintJobStoreRequest;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\PrintNode;
use App\Modules\PrintNode\src\Resources\PrintJobResource;

class PrintJobController extends Controller
{
    /**
     * @throws ShippingServiceException
     */
    public function store(PrintJobStoreRequest $request): PrintJobResource
    {
        $printJob = new PrintJob($request->validated());

        PrintNode::print($printJob);

        return new PrintJobResource($printJob);
    }
}
