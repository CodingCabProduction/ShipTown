@extends('pdf.template')
@section('content')
    @php
        $label ='test';
        \App\Models\Product::whereIn('sku', ['316765', '321334'])->get();
        $products[] = [
            'name' => 'Tennis Balls PRO - 6pk',
            'sku' => '316765',
            'price' => 14.95,
        ];
        $products[] = [
            'name' => 'Tennis Racket Evo 2022 L',
            'sku' => '321334',
            'price' => 74.95,
        ];
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
