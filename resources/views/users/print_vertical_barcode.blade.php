<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vertical QR Print</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.svg') }}">
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
    @if( $data["userscount"] == 0 )
        <p>Barcode is not enabled.</p>
    @endif 

    <?php $count = 0; ?>
    <?php $user_chunks = $data["users"]->chunk(40); ?>

    @foreach($user_chunks as $user_chunk)
        <section class="sheet cpad">
        @foreach($user_chunk as $user)
            <?php 
                $count++; 
                $odd_class = $count % 4 == 0 ? "" : "odd-label";
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