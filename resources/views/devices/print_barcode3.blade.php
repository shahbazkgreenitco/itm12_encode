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
            font-family: arial, helvetica, sans-serif;
        }
        .label {
            width: 73.2mm;
            max-height: 19mm;
            padding: 0 0;
            float: left;
            outline: none;
            border: none;
            margin:0;
            margin-bottom: 2mm;
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
            width: 54mm;
            word-break: break-word;
            margin-top: 1pt;
        }
        .qr_img img {
            padding: 0.8mm;
            margin: 0;
            width: auto;
            height: auto;
            max-width: 60px
        }
        .label.odd-label {
            margin-right: 0;
            margin-left: 0;
        }
        .text-b {
            font-size: 8px;
            line-height: 9pt;
        }
        @if(config("app.client") == "wsfx")
        .text-a {
            font-size: 10pt;
            font-weight: bold;
        }
        @else
        .text-a {
            font-size: 8pt;
        }
        @endif

        .text-c {
            font-size: 8px;
        }
        @page { size: A4 landscape };
        @media print {
            @page { size: A4 landscape }
        }
        .cpad {
            padding: 6mm 2mm;
        }
    </style>
</head>
<body class="A4 landscape">
    
@if( $data["devicescount"] == 0 )
    <p>Barcode is not enabled.</p>
@endif 

<?php $count = 0; ?>
<?php $device_chunks = $data["devices"]->chunk(40); ?>

@foreach($device_chunks as $device_chunk)
    <section class="sheet cpad">
    @foreach($device_chunk as $device)
        <?php 
            $count++; 
            $odd_class = $count % 4 == 0 ? "" : "odd-label";
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
                    @elseif( $device->isCheckedOutToPlace() )
                        <div class="text-c">{{ $device->assignedPlace->place }}</div>
                    @endif
                @endif
            </div>
        </div>
    @endforeach
    </section>
@endforeach

@if( $count > 0 ) 
    <script type="text/javascript">
        window.print();
    </script>
@endif
</body>
</html>
