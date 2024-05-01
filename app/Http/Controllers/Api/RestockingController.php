<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\RestockingReport;

class RestockingController extends Controller
{
    public function index()
    {
        $report = new RestockingReport();

        return $report->toJsonResource();
    }
}
