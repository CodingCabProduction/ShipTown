<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Modules\Reports\src\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\AllowedFilter;

class TransactionController extends Controller
{
    public function index()
    {
        $report = ReportService::fromQuery(Transaction::query())
           ->addFilter(AllowedFilter::exact('id'));

        return $report->toJsonResource();
    }

    public function store(Request $request)
    {
        $attributes = $request->all();

        $transaction = Transaction::query()->create($attributes);

        return JsonResource::make($transaction);
    }

    public function update(Request $request, int $transaction_id)
    {
        $transaction = Transaction::query()->findOrFail($transaction_id);

        $attributes = $request->all();

        $transaction->update($attributes);

        return JsonResource::make($transaction);
    }
}
