<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Modules\BoxTop\src\Services\BoxTopService;
use App\Traits\CsvFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Writer;

class BoxTopStockController extends Controller
{
    use CsvFileResponse;

    /**
     * @throws CannotInsertRecord|Exception
     */
    public function index(Request $request)
    {
        $filename = 'boxtop_stock.csv';

        BoxTopService::refreshBoxTopWarehouseStock();

        $query = DB::select(DB::raw('
SELECT
 modules_boxtop_warehouse_stock.SKUNumber AS SKU_BoxTop,
 products.sku as SKU_RMS,
 modules_boxtop_warehouse_stock.SKUName,
 modules_boxtop_warehouse_stock.Available

FROM `modules_boxtop_warehouse_stock`

LEFT JOIN products_aliases
  ON modules_boxtop_warehouse_stock.SKUNumber = products_aliases.alias

LEFT JOIN products
  ON products_aliases.product_id = products.id

ORDER BY SKU_RMS, SKUName ASC
'));

        $recordSet = collect($query)->map(function ($record) {
            return [
                'SKU_BoxTop' => $record->SKU_BoxTop,
                'SKU_RMS' => $record->SKU_RMS,
                'SKUName' => $record->SKUName,
                'Available' => $record->Available,
            ];
        });

        $csv = Writer::createFromFileObject(new \SplTempFileObject);

        if ($recordSet->isNotEmpty()) {
            $csv->insertOne(array_keys($recordSet[0]));

            foreach ($recordSet as $record) {
                $csv->insertOne($record);
            }
        }

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
