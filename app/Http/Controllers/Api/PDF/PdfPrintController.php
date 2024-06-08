<?php

namespace App\Http\Controllers\Api\PDF;

use App\Http\Controllers\Controller;
use App\Modules\PrintNode\src\Models\PrintJob;
use App\Modules\PrintNode\src\Resources\PrintJobResource;
use App\Services\PdfService;
use Illuminate\Http\Request;

/**
 * Class PrintOrderController.
 */
class PdfPrintController extends Controller
{
    public function update(Request $request)
    {
        $pdfString = PdfService::fromView('pdf/'.$request->template, $request->data);

        $printJob = new PrintJob();
        $printJob->printer_id = $request->printer_id;
        $printJob->title = $request->template.'_by_'. auth()->user()->id;
        $printJob->pdf = base64_encode($pdfString);
        $printJob->save();

        return PrintJobResource::make($printJob);
    }
}
