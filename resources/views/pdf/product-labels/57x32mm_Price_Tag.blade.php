@extends('pdf.template')
@section('content')
    @php
        $users_warehouse_code = auth()->user()->warehouse_code;
        $products = \App\Models\Product::whereSku($product_sku)->with(['prices'])->get();
    @endphp

    @if(empty($products))
        <div style="width: 100%; text-align: center; margin-top: 10mm;">
            <div class="product_name" style="height: 10mm; text-align: center;">Enter Product SKU</div>
        </div>
    @endif
    @foreach($products as $product)
        <div style="width: 100%; text-align: left">
            <div class="product_name">{{ $product['name'] }}</div>
            <div class="product_price"><span class="euroSymbol">&euro;</span>{{ $product['prices'][$users_warehouse_code]['price'] }}</div>
            <div class="product_sku">{{ $product['sku'] }}</div>
            <div class="product_barcode"><img src="data:image/svg,{{ DNS1D::getBarcodeSVG($product['sku'], 'C39', 0.65, 20, 'black', false) }}" alt="barcode"/></div>
        </div>
    @endforeach

    <style>
        @page {
            size: 57mm 32mm;
            margin: 2mm;
        }

        .product_name {
            font-size: 14pt;
            margin-top: 5px;
            margin-left: 5px;
            text-align: left;
            word-wrap: anywhere;
        }

        .euroSymbol {
            margin-right: 10px;
            font-size: 20pt;
            text-align: right;
            font-family: sans-serif;
            font-weight: bold;
            word-wrap: anywhere;
        }

        .product_price {
            font-size: 30pt;
            margin-right: 5px;
            text-align: right;
            font-family: sans-serif;
            font-weight: bold;
            word-wrap: anywhere;
        }

        .product_sku {
            position: absolute;
            left: 5px;
            bottom: 5px;
            font-size: 12pt;
        }

        .product_barcode {
            position: absolute;
            right: 5px;
            bottom: 5px;
        }
    </style>

@endsection
