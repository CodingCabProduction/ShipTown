@extends('pdf.template')
@section('content')

    @foreach($labels as $label)
        @php
            $fontSize = strlen($label) > 3 ? '24px' : '10px';
            $vcard = 'BEGIN:VCARD
VERSION:3.0
FN:Artur Hanusek
ORG:ShipTown
TEL:0863582664
EMAIL;WORK;INTERNET:artur@myshiptown.com
URL:https://ship.town
END:VCARD;'
        @endphp
        <table style="margin-top: 5px; width: 150px;">
            <tr>
                <td style="width: 50%; text-align: left; font-size: 12px; ">
{{--                    <img style="width: 55px; height: 55px;" src="data:image/svg,{{ DNS2D::getBarcodeSVG('https://ship.town', 'QRCODE') }}" alt="barcode" />--}}
                    <img style="width: 85px; height: 85px;" src="data:image/svg,{{ DNS2D::getBarcodeSVG($vcard, 'QRCODE') }}" alt="barcode" />
{{--                    <p style="font-size: 8px; margin-top: 5px; word-wrap: anywhere;">https://ship.town</p>--}}
                </td>
                <td style="width: 50%; text-align: left; font-weight: bold; padding-left: 5px; font-size: {{$fontSize}}">
                    Artur Hanusek <br>
                    0863582664 <br>
                    artur@myshiptown.com

                </td>
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
