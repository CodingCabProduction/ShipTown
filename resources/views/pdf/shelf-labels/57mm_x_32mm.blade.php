@extends('pdf.template')
@section('content')

    @foreach($labels as $label)
        @php
            $fontSize = strlen($label) > 3 ? '24px' : '32px';
        @endphp
        <table style="margin-top: 10px; width: 150px;">
            <tr>
                <td style="width: 50%; text-align: left; font-size: 12px; ">
                    <img style="width: 55px; height: 55px;" src="data:image/svg,{{ DNS2D::getBarcodeSVG('shelf:'.$label, 'QRCODE') }}" alt="barcode" />
                    <p style="font-size: 8px; margin-top: 5px; word-wrap: anywhere;">shelf:{{ $label }}</p>
                </td>
                <td style="width: 50%; text-align: center; font-weight: bold; padding-left: 20px; font-size: {{$fontSize}}">{{ $label }}</td>
            </tr>
        </table>
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <style>
        @page {
            size: 57mm 32mm;
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
