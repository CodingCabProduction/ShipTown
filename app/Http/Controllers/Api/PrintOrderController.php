<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\Resources\PrintJobResource;
use App\Services\OrderService;
use Illuminate\Http\Request;

class PrintOrderController extends Controller
{
    public function update(Request $request, string $order_number, string $template): PrintJobResource
    {
        $pdfString = OrderService::getOrderPdf($order_number, $template);

        $printJob = new PrintJob;
        $printJob->printer_id = $request->user()->printer_id;
        $printJob->title = $template.'_'.$order_number.'_by_'.$request->user()->id;
        $printJob->pdf = base64_encode($pdfString);
        $printJob->save();

        return PrintJobResource::make($printJob);
    }
}
