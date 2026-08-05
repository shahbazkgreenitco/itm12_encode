<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Two Column QR Print</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/paper-css/normalize.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/paper-css/paper.min.css') }}" />
    <style type="text/css">
        body {
            width: 110mm;
            font-size: 8pt;
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
        .text-a {
            font-size: 9pt;
            font-weight: bold;
        }
    </style>
</head>
<body class="">

@if( $data["userscount"] == 0 )
    <p>Barcode is not enabled.</p>
@endif

<?php $count = 0; ?>
<?php $user_chunks = $data["users"]->chunk(18); ?>
<section class="sheet cpad">
@foreach ($user_chunks as $user_chunk)
    @foreach($user_chunk as $user)
    <?php
        $count++;
        $odd_class = $count % 2 == 0 ? "" : "odd-label";
    ?>
    <div class="label {{ $odd_class }}">
        <div class="qr_img"><img src="{{ url('qrcode-user/'. $user->id) }}"></div>
        <div class="qr_text">
            @if($data["qr_text"]!='')
                <div class="text-a">{{ $data["qr_text"] }}</div>
            @endif
            @if($user->username!='')
                <div class="text-b">U: {{ $user->username }}</div>
            @endif
            @if($user->location!='')
                <div class="text-b">L: {{ trim($user->location->name) }}</div>
            @endif
            @if($user->company!='')
                <div class="text-b">C: {{ trim($user->company->name) }}</div>
            @endif
            @if($user->employee_num!='')
                <div class="text-b">E: {{ trim($user->employee_num) }}</div>
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
