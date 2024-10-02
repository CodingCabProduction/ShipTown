<?php

namespace App\Http\Controllers\Api\Modules\Printnode;

use App\Http\Controllers\Controller;
use App\Modules\PrintNode\src\Http\Requests\PrintNodeIndexRequest;
use App\Modules\PrintNode\src\PrintNode;
use Illuminate\Http\Resources\Json\JsonResource;

class PrinterController extends Controller
{
    public function index(PrintNodeIndexRequest $request): JsonResource
    {
        return new JsonResource(PrintNode::getPrinters());
    }
}
