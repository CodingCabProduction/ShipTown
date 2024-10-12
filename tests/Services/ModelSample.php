<?php

namespace Tests\Services;

class ModelSample
{
    const PRODUCT = [
        'sku' => '123456',
        'name' => 'Sample Product Name',
        'price' => 1.23,
        'quantity' => 10,
    ];

    const PRODUCT_WITH_SALE_PRICE = [
        'sku' => '123456',
        'name' => 'Sample Product Name',
        'price' => 1.23,
        'quantity' => 10,
        'sale_price' => 0.23,
        'sale_price_start_date' => '2019-01-30',
        'sale_price_end_date' => '2050-01-30',
    ];

    const ORDER_01 = [
        'order_number' => '0123456789',
        'products' => [
            [
                'sku' => '123456',
                'quantity' => 2,
                'price' => 4,
            ],
        ],
    ];

    const ORDER_02 = [
        'order_number' => '0123456789',
        'products' => [
            [
                'sku' => '123456',
                'quantity' => 20,
                'price' => 30,
            ],
        ],
    ];
}
