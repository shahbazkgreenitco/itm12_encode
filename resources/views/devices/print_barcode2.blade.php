<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Device Labels</title>
    <link rel="shortcut icon" href="{{ asset("favicon_2.ico") }}" type="image/x-icon">
    <link rel="icon" href="{{ asset("favicon_2.ico") }}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/paper-css/normalize.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/paper-css/paper.min.css') }}" />
    <style type="text/css">
        body {
            width: 110mm;
            font-size: 9pt;
            font-family: sans-serif;
        }
        .sheet {
            background: #fff;
            box-shadow: none;
            margin: 0;
            padding: 0;
        }
        .label {
            width: 55mm;
            padding: 0 0;
            margin-right: 0;
            float: left;
            height: 26mm;
            outline: none;
            /*border: 1px solid #000;
            margin-top: .2in;*/
        }
        .page-break {
            clear: left;
            display: block;
            page-break-after: always;
            break-after: always;
        }
        .qr_img {
            float: left;
            width: 18mm;
            height: 18mm;
            margin: 5mm 0 0 1mm;
            border: none;
            text-align: center;
        }
        .qr_text {
            float: left;
            /*font-size: 9pt;*/
            width: 36mm;
            word-break: break-word;
            overflow-wrap: break-word;
            margin: 5mm 0 0 0;
            /*margin-top: 1pt;*/
        }
        span.company {
            color: #18ADFF;
            font-size: 14px;
            font-weight: 700;
        }
        .qr_img img {
            padding: 0.8mm;
            margin: 0;
            width: auto;
            height: auto;
            max-width: 16.5mm;
        }
        /*.label.odd-label {
            margin-right: .2in;
            margin-left: .2in;
        }*/
        /*.text-b {
            font-size: 7pt;
            line-height: 9pt;
        }
        .text-a {
            font-size: 9pt;
        }
        .text-c {
            font-size: 10pt;
        }*/
        @media print {
            #Header, #Footer, #header, #footer, #print-header { display: none !important; }
            /*html, body {
                width: 210mm;
                height: 297mm;
            }*/
        }
        @page {
            size: A4;
            margin: 0;
        }
    </style>
    <style type="text/css">
        /*@-moz-document url-prefix() {
            body {
                width: 8.5in;
                margin: 0;
                font-family: arial, helvetica, sans-serif;
            }
            .label {
                width: 73mm;
                padding: 0 0;
                margin-right: 0;
                float: left;
                outline: none;
                border: 1px solid #000;
                margin-top: .2in;
            }
            .page-break {
                 clear: left;
                display: none;
                 page-break-after: always;
            }
            .qr_img {
                float: left;
                width: 18mm;
                height: 18mm;
                margin: 0;
                border: none;
                text-align: center;
            }
            .qr_text {
                float: left;
                font-size: 9pt;
                width: 55mm;
                word-break: break-word;
                margin-top: 1pt;
            }
            span.company {
                color: #18ADFF;
                font-size: 14px;
                font-weight: 700;
            }
            .qr_img img {
                padding: 0.8mm;
                margin: 0;
                width: auto;
                height: auto;
            }
            .label.odd-label {
                margin-right: .18in;
                margin-left: .16in;
            }
            @media  print {
                #header, #footer, #print-header { display: none !important; }
                html, body {
                    width: 210mm;
                    height: 297mm;
                }
            }
            @page  {
                size: A4;
                margin: 0.3in 0.2in;
            }
        }*/
    </style>
</head>
<body class="">

@if( $data["devicescount"] == 0 )
    <p>Barcode is not enabled.</p>
@endif

<?php $count = 0; ?>
<?php $device_chunks = $data["devices"]->chunk(18); ?>
<section class="sheet cpad">
@foreach ($device_chunks as $device_chunk)
    @foreach($device_chunk as $device)
    <?php
        $count++;
        $odd_class = $count % 2 == 0 ? "" : "odd-label";
    ?>
    <div class="label {{ $odd_class }}">
        <div class="qr_img"><img src="{{ url('qrcode/'. $device->id) }}"></div>
        <div class="qr_text">
            {{-- @if($data["qr_text"]!='')
                <div class="text-a">{{ $data["qr_text"] }}</div>
            @endif --}}
            @if($device->company!='')
                <div class="text-a qr">{{ $device->company->strCutOff("name", 32) }}</div>
            @endif
            @if($device->name!='')
                <div class="text-b">N: {{ $device->name }}</div>
            @endif
            @if($device->asset_tag!='')
                <div class="text-b">T: {{ trim($device->asset_tag) }}</div>
            @endif
            @if($device->serial!='')
                <div class="text-b">S: {{ trim($device->serial) }}</div>
            @endif
            @if( $data["option"] == 1 && $device->isCheckedOut() )
                @if( $device->isCheckedOutToUser() )
                    @if( $data["user_info"] == 1 )
                        <div class="text-c">{{ $device->assigneduser->getGuranteedNameText(true) }} / @if($device->location) {{ $device->location->name }} @endif</div>                        
                    @else
                        <div class="text-c">{{ $device->assigneduser->username }}</div>
                    @endif
                @elseif( $device->isCheckedOutToPlace())
                    <div class="text-c">{{ $device->assignedPlace->place }}</div>
                @endif
            @endif
        </div>
    </div>
    {{--@if($count % 24 == 0)
        <div class="page-break"></div>
    @endif--}}
    @endforeach
@endforeach
</section>
@if( $count > 0 )
<script type="text/javascript">
    window.print();
</script>
@endif
</body>
</html>
