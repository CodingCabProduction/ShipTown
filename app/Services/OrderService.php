<?php

namespace App\Services;

use App\Events\Order\OrderCreatedEvent;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderPayment;
use App\Models\OrderProduct;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class OrderService
{
    public static function canNotFulfill(Order $order, $sourceLocationId = null): bool
    {
        return ! self::canFulfill($order, $sourceLocationId);
    }

    public static function canFulfill(Order $order, $warehouse_code = null): bool
    {
        $orderProducts = $order->orderProducts()->get();

        foreach ($orderProducts as $orderProduct) {
            if (self::canNotFulfillOrderProduct($orderProduct, $warehouse_code)) {
                return false;
            }
        }

        return true;
    }

    public static function getOrderPdf(string $order_number, string $template_name): string
    {
        /** @var Order $order */
        $order = Order::query()
            ->where(['order_number' => $order_number])
            ->with(['shippingAddress', 'billingAddress'])
            ->firstOrFail();

        $view = 'pdf/orders/'.$template_name;
        $data = $order->toArray();

        return PdfService::fromView($view, $data);
    }

    /**
     * @throws Exception
     */
    public static function updateOrCreate(array $orderAttributes): Order
    {
        $attributes = $orderAttributes;
        $attributes['is_editing'] = true;

        $order = Order::updateOrCreate(['order_number' => $attributes['order_number']], $attributes);

        self::updateOrCreateShippingAddress($order, $attributes['shipping_address']);

        if (Arr::has($attributes, 'billing_address')) {
            self::updateOrCreateBillingAddress($order, $attributes['billing_address']);
        }

        $order = self::syncOrderProducts($attributes['order_products'], $order);

        $order = self::syncOrderPayments($attributes['payments'], $order);

        OrderCreatedEvent::dispatch($order);

        $order->is_editing = false;
        $order->save();

        return $order;
    }

    public static function updateOrCreateShippingAddress(Order $order, array $shippingAddressAttributes): Order
    {
        $shipping_address = OrderAddress::query()->findOrNew($order->shipping_address_id ?: 0);
        $shipping_address->fill($shippingAddressAttributes);
        $shipping_address->save();

        $order->shippingAddress()->associate($shipping_address);
        $order->save();

        return $order;
    }

    public static function updateOrCreateBillingAddress(Order $order, mixed $attributes): Order
    {
        $billing_address = OrderAddress::query()->findOrNew($order->billing_address_id ?: 0);
        $billing_address->fill($attributes);
        $billing_address->save();

        $order->billingAddress()->associate($billing_address);
        $order->save();

        return $order;
    }

    /**
     * @return null
     */
    private static function getProductId(array $orderProductAttributes)
    {
        $product = ProductService::find($orderProductAttributes['sku_ordered']);
        if ($product) {
            return $product->id;
        }

        $extractedSku = Str::substr($orderProductAttributes['sku_ordered'], 0, 6);
        $product = ProductService::find($extractedSku);
        if ($product) {
            return $product->id;
        }

        return null;
    }

    /**
     * @throws Exception
     */
    private static function syncOrderProducts($order_products, Order $order): Order
    {
        $orderProductIdsToKeep = [];

        foreach ($order_products as $orderProductAttributes) {
            $orderProduct = OrderProduct::query()->where(['order_id' => $order->getKey()])
                ->whereNotIn('id', $orderProductIdsToKeep)
                ->updateOrCreate(
                    // attributes
                    collect($orderProductAttributes)
                        ->only([
                            'sku_ordered',
                            'name_ordered',
                            'quantity_ordered',
                        ])
                        ->toArray(),
                    // values
                    [
                        'order_id' => $order->getKey(),
                        'product_id' => self::getProductId($orderProductAttributes),
                        'price' => $orderProductAttributes['price'],
                    ]
                );

            $orderProductIdsToKeep[] = $orderProduct->getKey();
        }

        OrderProduct::query()
            ->where(['order_id' => $order->id])
            ->whereNotIn('id', $orderProductIdsToKeep)
            ->delete();

        return $order->refresh();
    }

    /**
     * @throws Exception
     */
    private static function syncOrderPayments($payments, Order $order): Order
    {
        $storedOrderPayments = OrderPayment::where('order_id', $order->getKey())->get();

        $orderPaymentIdsToKeep = [];
        foreach ($payments as $payment) {
            $orderPayment = $storedOrderPayments->where('additional_fields.id', $payment['additional_fields']['id'])->first();

            if (!$orderPayment) {
                $orderPayment = new OrderPayment();
            }

            $orderPayment->fill([
                'order_id' => $order->getKey(),
                'paid_at' => $payment['paid_at'],
                'name' => $payment['name'],
                'amount' => $payment['amount'],
                'additional_fields' => $payment['additional_fields'],
            ]);

            $orderPayment->save();

            $orderPaymentIdsToKeep[] = $orderPayment->id;
        }

        OrderPayment::query()
            ->where(['order_id' => $order->id])
            ->whereNotIn('id', $orderPaymentIdsToKeep)
            ->delete();

        return $order->refresh();
    }

    /**
     * @param  null  $warehouse_code
     */
    public static function canFulfillOrderProduct(OrderProduct $orderProduct, $warehouse_code = null): bool
    {
        if ($orderProduct->product_id) {
            return self::canFulfillProduct(
                $orderProduct->product_id,
                $orderProduct->quantity_to_ship,
                $warehouse_code
            );
        }

        return false;
    }

    /**
     * @param  null  $warehouse_code
     */
    public static function canNotFulfillOrderProduct(OrderProduct $orderProduct, $warehouse_code = null): bool
    {
        return ! self::canFulfillOrderProduct($orderProduct, $warehouse_code);
    }

    public static function canFulfillProduct(int $product_id, float $quantity_requested, ?string $warehouse_code = null): bool
    {
        if ($quantity_requested <= 0) {
            return true;
        }

        $totalQuantityAvailable = Inventory::query()
            ->where('product_id', $product_id)
            ->when($warehouse_code, function ($query, $warehouse_code) {
                $query->where('warehouse_code', $warehouse_code);
            })
            ->sum('quantity_available');

        if ($totalQuantityAvailable === null) {
            return false;
        }

        return (float) $totalQuantityAvailable >= $quantity_requested;
    }
}
