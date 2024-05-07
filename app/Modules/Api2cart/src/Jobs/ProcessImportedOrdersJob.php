<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Order;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\Services\OrderService;
use Illuminate\Support\Collection;

class ProcessImportedOrdersJob extends UniqueJob
{
    public function handle(): void
    {
        Api2cartOrderImports::query()
            ->whereNull('when_processed')
            ->orderBy('id')
            ->chunk(1, function (Collection $api2cartOrderImports) {
                $api2cartOrderImports->each(function (Api2cartOrderImports $api2cartOrderImport) {
                    $order = $this->createOrderFrom($api2cartOrderImport);

                    $api2cartOrderImport->update([
                        'order_number' => $order->order_number,
                        'when_processed' => now(),
                    ]);
                });
            });
    }

    private function createOrderFrom(Api2cartOrderImports $orderImport): Order
    {
        $data = $orderImport->raw_import;

        $uuid = implode('-', ['module-api2cart-connection-id', $orderImport->connection_id, 'remote-record-id', data_get($data, 'order_id')]);

        $orderAttributes = [
            'custom_unique_reference_id' => $uuid,
            'order_number'               => data_get($data, 'id'),
            'total'                      => data_get($data, 'totals.total', 0),
            'total_products'             => data_get($data, 'totals.subtotal'),
            'total_shipping'             => data_get($data, 'totals.shipping', 0),
            'total_discounts'            => data_get($data, 'totals.discount', 0),
            'total_paid'                 => data_get($data, 'total.total_paid', 0) ?? 0,
            'shipping_method_name'       => data_get($data, 'shipping_method.name', 0),
            'shipping_method_code'       => data_get($data, 'shipping_method.additional_fields.code', 0),
            'order_placed_at'            => $orderImport->ordersCreateAt()->tz('UTC'),
            'order_products'             => $orderImport->extractOrderProducts(),
            'shipping_address'           => $orderImport->extractShippingAddressAttributes(),
            'billing_address'            => $orderImport->extractBillingAddressAttributes(),
            'raw_import'                 => $data,
        ];

        ray($orderAttributes);

        return OrderService::updateOrCreate($orderAttributes);
    }
}
