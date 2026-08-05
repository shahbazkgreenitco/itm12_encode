<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Device Labels</title>
    <link rel="shortcut icon" href="{{ asset("favicon_2.ico") }}" type="image/x-icon">
    <link rel="icon" href="{{ asset("favicon_2.ico") }}" type="image/x-icon">
    <style type="text/css">
        body {
            width: 8.5in;
            margin: 0 .02in;
            font-family: arial, helvetica, sans-serif;
        }
        .label {
            width: 3.92in;
            padding: 0 0;
            margin-right: 0;
            float: left;
            outline: none;
            border: 1px solid transparent;
            margin-top: .2in;
        }
        .page-break {
            clear: left;
            display: block;
            page-break-after: always;
            break-after: always;
        }
        .qr_img {
            float: left;
        }
        .qr_text {
            float: left;
            font-size: 14px;
            width: 2.33in;
            word-break: break-word;
            margin-top: 15px;
        }
        span.company {
            color: #18ADFF;
            font-size: 14px;
            font-weight: 700;
        }
        .qr_img img {
            width: 132px;
            height: 132px;
            vertical-align: middle;
            padding: 0px;
            border: 1px solid #000;
            margin: 3px;
        }
        .label.odd-label {
            margin-right: .2in;
            margin-left: .2in;
        }
        @media print {
            #Header, #Footer, #header, #footer, #print-header { display: none !important; }
        }
        @page {
            size: A4;
            margin: 0;
        }
    </style>
    <style type="text/css">
        @-moz-document url-prefix() {
            body {
                width: 8.5in;
                margin: 0;
                font-family: arial, helvetica, sans-serif;
            }
            .label {
                width: 3.8in;
                padding: 0 0;
                margin-right: 0;
                float: left;
                outline: 1px solid transparent;
                border: none;
                margin-top: .2in;
            }
            .page-break {
                 clear: left; 
                display: none;
                 page-break-after: always; 
            }
            .qr_img {
                float: left;
            }
            .qr_text {
                float: left;
                font-size: 14px;
                width: 2.13in;
                word-break: break-word;
                margin-top: 0.1in;
            }
            span.company {
                color: #18ADFF;
                font-size: 14px;
                font-weight: 700;
            }
            .qr_img img {
                width: 1.4in;
                height: 1.4in;
                vertical-align: middle;
                padding: .04in;
            }
            .label.odd-label {
                margin-right: .18in;
                margin-left: .16in;
            }
            @media  print {
                #header, #footer, #print-header { display: none !important; }
            }
            @page  {
                size: A4;
                margin: 0.3in 0.2in;
            }
        }
    </style>
</head>
<body>
    
@if( $data["devicescount"] == 0 )
    <p>Barcode is not enabled.</p>
@endif 

<?php $count = 0; ?>

@foreach ($data["devices"] as $device)
    <?php 
        $count++; 
        $odd_class = $count % 2 == 0 ? "" : "odd-label";
    ?>
    <div class="label {{ $odd_class }}">
        <div class="qr_img"><img src="{{ url('qrcode/'. $device->id) }}"></div>
        <div class="qr_text">
            @if ($data["qr_text"]!='')
            <span class="company">{{ $data["qr_text"] }}</span>
            <br><br>
            @endif
            @if ($device->name!='')
            <b>N: {{ $device->name }}</b>
            <br>
            @endif
            @if ($device->asset_tag!='')
            <span>T: {{ trim($device->asset_tag) }}</span>
            <br>
            @endif
            @if ($device->serial!='')
            <span>S: {{ trim($device->serial) }}</span>
            <br>
            @endif
        </div>
    </div>
    @if ($count % 12 == 0)
        <div class="page-break"></div>
    @endif
@endforeach

@if( $count > 0 ) 
<script type="text/javascript">
     window.print();
</script>
@endif
</body>
</html>