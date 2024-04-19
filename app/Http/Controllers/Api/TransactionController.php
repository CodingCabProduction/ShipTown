<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Modules\Reports\src\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionController extends Controller
{
    public function index()
    {
        ReportService::fromQuery(Transaction::query());


        return JsonResource::collection(Transaction::all());
    }

    public function store(Request $request)
    {
        $attributes = $request->all();

        $transaction = Transaction::create($attributes);

        return response()->json($transaction, 201);
    }
}
