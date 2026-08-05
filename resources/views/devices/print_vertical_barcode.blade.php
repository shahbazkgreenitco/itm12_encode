<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vertical QR Print</title>
    <style type="text/css">
        body {
            width: 26mm;
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
            width: 18mm;
            height: auto;
            /* float: left; */
            padding: 0;
            margin: 5mm 0 0 5mm;
        }

        .label {
            /* width: 18mm;
            height: 25mm; */
            margin: 0mm 0 0mm 0;
            padding: 0;
            /*clear: both;*/
        }

        .qr_text {
            padding: 2px 0 0 0;
            margin: 5mm 0px 0px 0px;
            /* float: left; */
            overflow-wrap: break-word;
            transform: rotate(90deg);
        }

        .qr_img img {
            width: auto;
            height: auto;
            max-width: 18mm;
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
                    @if($device->company!='')
                    <div class="text-a qr">{{ $device->company->strCutOff("name", 28) }}</div>
                    @endif
                    @if($device->name!='')
                    <div class="text-b">N: {{ $device->name }}</div>
                    @endif
                    @if($device->asset_tag!='')
                    <div class="text-b text-tag">T: {{ trim($device->asset_tag) }}</div>
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
                        @elseif($device->isCheckedOutToPlace())
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