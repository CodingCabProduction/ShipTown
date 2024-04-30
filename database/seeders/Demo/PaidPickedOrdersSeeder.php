<?php

namespace Database\Seeders\Demo;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\Pick;
use App\Models\Product;
use App\User;
use Illuminate\Database\Seeder;

class PaidPickedOrdersSeeder extends Seeder
{
    public function run(): void
    {

        $user = User::first();

        $orders = Order::factory()
            ->count(10)
            ->create(['status_code' => 'picking', 'label_template' => 'address_label', 'shipping_address_id' => $this->createIrishShippingAddress()->getKey()]);

        $orders->each(function (Order $order) {
            Product::query()
                ->limit(rand(1, 5))
                ->inRandomOrder()
                ->get()
                ->each(function (Product $product) use ($order) {
                    return OrderProduct::create([
                        'order_id' => $order->getKey(),
                        'product_id' => $product->getKey(),
                        'quantity_ordered' => rand(2, 6),
                        'price' => $product->price,
                        'name_ordered' => $product->name,
                        'sku_ordered' => $product->sku,
                    ]);
                });

            $order = $order->refresh();
            Order::query()->where(['id' => $order->getKey()])->update(['total_paid' => $order->total_order]);
        });

        OrderProduct::query()
            ->whereIn('order_id', $orders->pluck('id'))
            ->each(function (OrderProduct $orderProduct) use ($user) {
                $this->pickOrderProduct($orderProduct, $user);
            });
    }

    /**e
     * @param OrderProduct $orderProduct
     * @param $user
     * @return void
     */
    function pickOrderProduct(OrderProduct $orderProduct, $user): void
    {
        $orderProduct->update(['quantity_picked' => $orderProduct->quantity_ordered]);

        Pick::query()->create([
            'user_id' => $user->getKey(),
            'warehouse_code' => $user->warehouse->code,
            'product_id' => $orderProduct->product_id,
            'sku_ordered' => $orderProduct->sku_ordered,
            'name_ordered' => $orderProduct->name_ordered,
            'quantity_picked' => $orderProduct->quantity_ordered,
            'quantity_skipped_picking' => 0,
        ]);
    }

    private function createIrishShippingAddress()
    {
        return OrderAddress::factory()->create([
            'country_name' => 'Ireland',
            'country_code' => 'IE',
        ]);
    }
}
