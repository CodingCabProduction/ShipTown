@extends('pdf.template')
@section('content')
    @php
        $label ='test';
//        \App\Models\Product::whereIn('sku', ['316765', '321334'])->get();
        $products[] = ['sku' => '40011', 'name' => 'Secret Box', 'price' => 199];
        $products[] = ['sku' => '40012', 'name' => 'Power Adaptor', 'price' => 19.95];
        $products[] = ['sku' => '40013', 'name' => 'Christmas Snowball', 'price' => 29.95];
        $products[] = ['sku' => '40014', 'name' => 'Gloves Size L', 'price' => 8.95];
        $products[] = ['sku' => '40015', 'name' => 'Buttons 100pk', 'price' => 9.99];
//
//        $products[] = ['sku' => '4001', 'name' => 'T-Shirt Blue', 'price' => 29.95];
//        $products[] = ['sku' => '4002', 'name' => 'T-Shirt Brown Grey', 'price' => 29.95];
//        $products[] = ['sku' => '4003', 'name' => 'T-Shirt Light Brown', 'price' => 29.95];
//        $products[] = ['sku' => '4004', 'name' => 'T-Shirt Light Grey', 'price' => 29.95];
//        $products[] = ['sku' => '4005', 'name' => 'T-Shirt Grey', 'price' => 29.95];
//        $products[] = ['sku' => '4006', 'name' => 'T-Shirt Black', 'price' => 29.95];
//        $products[] = ['sku' => '4007', 'name' => 'T-Shirt Purple', 'price' => 29.95];
//        $products[] = ['sku' => '4008', 'name' => 'T-Shirt Green', 'price' => 29.95];
//        $products[] = ['sku' => '4009', 'name' => 'T-Shirt ', 'price' => 29.95];
//
//        $products[] = ['sku' => '46', 'name' => 'Silver Tennis Racket', 'price' => 85];
//        $products[] = ['sku' => '47', 'name' => 'Green Cap', 'price' => 12.97];
//        $products[] = ['sku' => '48', 'name' => 'Ball Mega pack 100', 'price' => 35.90];
//        $products[] = ['sku' => '49', 'name' => 'EVO PRO 2 Tennis Racket', 'price' => 35.90];
    @endphp
    @foreach($products as $product)
{{--        @foreach($chunk as $index => $label)--}}
            @php
                $label ='test';
                $fontSize = strlen($label) > 4 ? '40px' : '95px';
                $marginTop = strlen($label) > 4 ? '40px' : '50px';
            @endphp
            <div>
                <span class="product_name" style="">{{ $product['name'] }}</span>
                <div class="product_price">€ {{ $product['price'] }}</div>
                <div class="product_barcode">
                    <img src="data:image/svg,{{ DNS1D::getBarcodeSVG($product['sku'], 'C39', 1, 25) }}" alt="barcode" />
                </div>
            </div>
        @endforeach
{{--        @if(!$loop->last)--}}
{{--            <div class="page-break"></div>--}}
{{--        @endif--}}
{{--    @endforeach--}}

    <style>
        @page {
            size: 57mm 32mm;
            margin: 2mm;
        }

        .product_name {
            font-size: 9pt;
            text-align: left;
            word-wrap: anywhere;
        }

        .product_price {
            font-family: sans-serif;
            font-size: 36pt;
            font-weight: bold;
            text-align: right;
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
