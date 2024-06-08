@extends('pdf.template')
@section('content')
    @php
        $users_warehouse_code = auth()->user()->warehouse_code;
        $products = [];
        if (!empty($product_sku)) {
            $products[] = \App\Models\Product::whereSku($product_sku)->with(['prices'])->first();
        }
    @endphp

    @if(empty($products))
        <div style="width: 100%; text-align: center; margin-top: 10mm;">
            <div class="product_name" style="height: 10mm; text-align: center;">Enter Product SKU</div>
        </div>
    @endif
    @foreach($products as $product)
        <div style="width: 100%; text-align: left">
            <div class="product_name">{{ $product['name'] }}</div>
            <div class="product_price">{{ $product['prices'][$users_warehouse_code]['price'] }}</div>
                <div style="position: absolute; left: 0; bottom: 0;">
                    {{ $product['sku'] }}
                </div>
            <div class="product_barcode" style="position: absolute; right: 0; bottom: 0;">
                <img src="data:image/svg,{{ DNS1D::getBarcodeSVG($product['sku'], 'C39', 0.60, 20, 'black', false) }}" alt="barcode"/>
            </div>
        </div>
    @endforeach

    <style>
        @page {
            size: 57mm 32mm;
            margin: 2mm;
        }

        .product_name {
            font-size: 14pt;
            text-align: left;
            word-wrap: anywhere;
        }

        .product_price {
            text-align: right;
            font-family: sans-serif;
            font-size: 36pt;
            font-weight: bold;
            word-wrap: anywhere;
        }

        .product_barcode {
        }

        .page-break {
            page-break-after: always;
        }

        .half_first {
            width: 49%;
            float: right;
        }

        .half_second {
            width: 49%;
            float: left;
        }

        h1, p, img {
            margin: 0;
            padding: 0;
        }
    </style>

@endsection
