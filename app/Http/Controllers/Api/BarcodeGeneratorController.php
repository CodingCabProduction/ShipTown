<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BarcodeGeneratorIndexRequest;
use Illuminate\Http\Response;
use Milon\Barcode\DNS1D;

class BarcodeGeneratorController extends Controller
{
    public function index(BarcodeGeneratorIndexRequest $request)
    {
        $content = $request->validated('content');
        $barcode_type = 'C39';
        $width = 0.60;
        $height = 15;
        $color = $request->validated('color', 'black');
        $show_code = false;

        $content_type = 'image/svg+xml';
        $filename = 'barcode.svg';

        return new Response(
            DNS1D::getBarcodeSVG($content, $barcode_type, $width, $height, $color, $show_code),
            200,
            array(
                'Content-Type'          => $content_type,
                'Content-Disposition'   => 'attachment; filename="' . $filename . '"'
            )
        );
    }
}
