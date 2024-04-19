<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PrintJob\StoreRequest;
use App\Modules\PrintNode\src\Models\PrintJob;
use Illuminate\Http\Resources\Json\JsonResource;

class PrintJobController
{
    public function store(StoreRequest $request)
    {
        $printJob = PrintJob::create($request->all());

        return JsonResource::make($printJob);
    }
}
