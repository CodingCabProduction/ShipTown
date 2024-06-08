@extends('pdf.template')
@section('content')
    @php
        $products = [];
        if (!empty($product_sku)) {
            $products[] = \App\Models\Product::whereSku($product_sku)->first();
        }
    @endphp

    @if(empty($products))
        <div style="width: 100%; text-align: center; margin-top: 10mm;">
            <div class="product_name" style="height: 10mm; text-align: center;">Enter Product SKU</div>
        </div>
    @endif
    @foreach($products as $product)
        <div style="width: 100%; text-align: center">
            <div class="product_name" style="height: 40mm; text-align: center">{{ $product['name'] }}</div>
            <div class="product_barcode" style="height: 45mm;">
                <img src="data:image/svg,{{ DNS1D::getBarcodeSVG($product['sku'], 'C39', 1.2, 100, 'black', false) }}" alt="barcode" />
                <div style="font-size: 18pt">{{ $product['sku'] }}</div>
            </div>
        </div>
    @endforeach

    <style>
        @page {
            size: 152.4mm 101.6mm;
            margin: 2mm;
        }

        .product_name {
            font-size: 48pt;
            text-align: left;
            word-wrap: anywhere;
            margin-bottom: 2mm;
        }

        .product_price {
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

        h1, p, img{
            margin: 0;
            padding: 0;
        }
    </style>

@endsection
