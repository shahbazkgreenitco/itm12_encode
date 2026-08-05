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
            margin-left: 2pt;
        }
        .qr_img img {
            padding: 0;
            margin: 0;
            width: auto;
            height: auto;
        }
        .label.odd-label {
            margin-right: 0;
            margin-left: 0;
        }
        .text-b {
            font-size: 8pt;
            line-height: 9pt;
            font-stretch: expanded;
        }
        .text-a {
            font-size: 8pt;
        }
        .text-c {
            font-size: 10pt;
        }
        @page { size: A4 landscape };
        @media print {
            @page { size: A4 landscape }
        }
        .cpad {
            padding: 6mm 2mm;
        }
        .text-tag {
            color: #f92f2f;
        }
        .text-checkout {
            color: #146df2;
        }
        .qr {
            color: #000000;
            font-weight: 600;
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
                @if($device->isCheckedOut())
                    @if($device->isCheckedOutToPlace() && $device->assignedPlace)
                    <div class="text-c text-checkout">{{ $device->assignedPlace->place }}</div>
                    @elseif($device->isCheckedOutToUser() && $device->assigneduser)
                    <div class="text-c text-checkout">{{ $device->assigneduser->first_name }} {{ $device->assigneduser->last_name }}</div>
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