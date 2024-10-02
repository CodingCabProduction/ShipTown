<?php

namespace App\Http\Controllers\Api\Modules\Api2cart;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Http\Requests\ProductsIndexRequest;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Services\Api2cartService;
use GuzzleHttp\Exception\GuzzleException;

class ProductsController extends Controller
{
    /**
     * @throws RequestException|GuzzleException
     */
    public function index(ProductsIndexRequest $request)
    {
        $connection = Api2cartConnection::query()->first();

        $sku = $request->get('sku');

        $productInfo = Api2cartService::getProductInfo($connection, $sku, ['force_all']);

        $this->respondOK200($productInfo);
    }
}
