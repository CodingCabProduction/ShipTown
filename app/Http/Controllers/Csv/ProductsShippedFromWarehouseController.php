<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Traits\CsvFileResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use League\Csv\CannotInsertRecord;

class ProductsShippedFromWarehouseController extends Controller
{
    use CsvFileResponse;

    /**
     * @throws CannotInsertRecord
     */
    public function index(Request $request): Response|Application|ResponseFactory
    {
        $query = OrderProduct::getSpatieQueryBuilder()
            ->select([
                'products.sku',
                'products.name',
                DB::raw('0 as qty_at_source'),
                DB::raw('0 as qty_at_destination'),
                'orders_products.quantity_shipped',
            ])
            ->join('products', 'products.id', '=', 'orders_products.product_id')
            ->where('orders_products.quantity_shipped', '>', 0);

        return $this->toCsvFileResponse($query->get(), 'warehouse_shipped.csv');
    }
}
