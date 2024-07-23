<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Modules\Reports\src\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
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

        $entries = collect(data_get($attributes, 'raw_data.entries'));

        $groupedEntries = $entries->groupBy('barcode')
            ->map(function (Collection $group) {
                return [
                    'barcode' => $group->first()['barcode'],
                    'quantity' => $group->sum('quantity'),
                    'cost_price' => $group->first()['cost_price'],
                    'full_price' => $group->first()['full_price'],
                    'sold_price' => $group->first()['sold_price'],
                    'current_price' => $group->first()['current_price'],
                    'total_cost_price' => $group->sum('total_cost_price'),
                    'total_sold_price' => $group->sum('total_sold_price'),
                ];
            })
            ->values()
            ->toArray();

        $attributes['raw_data']['entries'] = $groupedEntries;

        $transaction->update($attributes);

        return JsonResource::make($transaction);
    }
}
