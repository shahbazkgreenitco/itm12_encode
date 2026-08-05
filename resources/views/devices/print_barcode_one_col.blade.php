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
            width: 55mm;
            font-size: 9pt;
            font-family: sans-serif;
        }

        .sheet {
            background: #fff;
            box-shadow: none;
            margin: 0;
            padding: 0;
        }

        .qr_img {
            width: 17mm;
            height: auto;
            float: left;
            padding: 0;
            margin: 5mm 0 0 1mm;
        }

        .label {
            width: 55mm;
            height: 25mm;
            margin: 0mm 0 0mm 0;
            padding: 0;
            clear: both;
        }

        .qr_text {
            width: 36mm;
            height: 19mm;
            padding: 0;
            margin: 5mm 0 0 0;
            float: left;
            overflow-wrap: break-word;
        }

        .qr_img img {
            width: auto;
            height: auto;
            max-width: 16.5mm;
        }
    </style>
</head>
<body class="">
        
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