<?php

namespace App\Modules\Reports\src\Models;

use App\Models\OrderShipment;

class OrderShipmentReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Order Shipments';

        $this->addField('user_name', 'user.name', hidden: false);
        $this->addField('shipping_number', 'orders_shipments.shipping_number', hidden: false);
        $this->addField('order_number', 'order.order_number', hidden: false);
        $this->addField('created_at', 'orders_shipments.created_at', 'datetime', hidden: false);

        $this->baseQuery = OrderShipment::query()
            ->leftJoin('orders as order', 'orders_shipments.order_id', '=', 'order.id')
            ->leftJoin('users as user', 'orders_shipments.user_id', '=', 'user.id');
    }
}
