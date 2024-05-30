@extends('pdf.template')
@section('content')

    @foreach($labels as $label)
        @php
            $fontSize = strlen($label) > 3 ? '80px' : '160px';
        @endphp
        <div style="overflow: hidden;">
            <h1 style="text-align: center; font-size: {{$fontSize}}; margin-top: 100px; word-wrap: anywhere; line-height: 90%;">{{ $label }}</h1>
        </div>
        <img style="width: 200px; height: 200px; margin-top:50px; margin-left: 80px;" src="data:image/svg,{{ DNS2D::getBarcodeSVG('shelf:'.$label, 'QRCODE') }}" alt="barcode" />
        <p style="text-align: center; font-size: 22px;  margin-top: 5px; word-wrap: anywhere;">shelf:{{ $label }}</p>
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <style>
        @page {
            size: 101.6mm 152.4mm;
            margin: 3mm;
        }

        .page-break {
            page-break-after: always;
        }

        h1, p {
            margin: 0;
            padding: 0;
        }
    </style>

@endsection
