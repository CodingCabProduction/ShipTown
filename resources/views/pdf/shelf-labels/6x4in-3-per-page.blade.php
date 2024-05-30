@extends('pdf.template')
@section('content')

    @foreach(collect($labels)->chunk(3) as $chunk)
        @foreach($chunk as $index => $label)
            @php
                $barcodeText = 'shelf:'.$label;
                $fontSize = strlen($label) > 4 ? '50px' : '90px';
            @endphp
            <div class="label_box">
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <img style="width: 70px; height: 70px;" src="data:image/svg,{{ DNS2D::getBarcodeSVG($barcodeText, 'QRCODE') }}" alt="barcode" />
                            <p style="word-wrap: anywhere; font-size: 10px; margin-top: 5px;">{{ $barcodeText }}</p>
                        </td>
                        <td>
                            <h1 style="font-size: {{ $fontSize }}; word-wrap: anywhere; line-height: 90%">{{ $label }}</h1>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <style>
        @page {
            size:101.6mm 152.4mm;
            margin: 3mm;
        }

        h1, p, img, table, tbody, tr, td {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border: black;
            text-align: center;
            vertical-align: top;
        }

        td {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .label_box {
            height: 33%;
            clear: bottom;
        }
    </style>

@endsection
