{{-- @page-meta
{
"page_no": "USR13D-27",
"file": "print.blade.php",
"versions": [
{
"version": "1.0",
"writer": "safdar Ali",
"from": "2026-06",
"reviewer": null,
"description": "Initial version "
}
]
}
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>User Asset</title>
    <link rel="shortcut icon" href="{{ asset(" favicon_2.ico") }}" type="image/x-icon">
    <link rel="icon" href="{{ asset(" favicon_2.ico") }}" type="image/x-icon">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .my-container {
            width: 100%;
        }

        .logo {
            margin-bottom: 10px;
        }

        .image-size {
            max-width: 220px;
            max-height: 70px;
            height: auto;
        }

        hr {
            margin: 10px 0 15px;
        }

        .center_text {
            text-align: center;
        }

        .table_title {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
            overflow-wrap: break-word;
        }

        table th {
            background: #f3f3f3;
            white-space: nowrap;
        }

        .mar-top-15 {
            margin-top: 15px;
        }

        .mar-top-25 {
            margin-top: 25px;
        }

        .mar-top-40 {
            margin-top: 40px;
        }

        .signatur_div {
            width: 100%;
            margin-top: 40px;
        }

        .signature-table {
            width: 100%;
            border: none;
        }

        .signature-table td {
            width: 50%;
            border: none;
            vertical-align: top;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: #666;
        }

        @media print {
            body {
                margin: 0;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
            }

            .signatur_div {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body class="A4 portrait low-line">
    <section class="sheet cpad">
        <div class="my-container">
            <div class="row">
                <div class="pull-left">
                    <div class="logo">
                        @if(CommonHelper::settings()->logo)
                        <img src="{{ asset('uploads/' . CommonHelper::settings()->logo) }}" class="image-size"
                            alt="Logo">
                        @else
                        <strong>{{ CommonHelper::settings()->site_name }}</strong>
                        @endif
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <p class="center_text">
                    @if(config('app.client') == 'shyammetalics')
                    <strong>Asset Handover Form</strong>
                    @else
                    <strong>End User License Agreement</strong>
                    @endif
                </p>
            </div>
            <div class="row mar-top-25">
                <div class="col-md-12 pad-no">
                    <div class="pull-left">
                        <div>
                            @if(config('app.client') == 'shyammetalics')
                            <p> I {{ $user->getGuranteedNameText() }} @if ($user->employee_num)/{{ $user->employee_num
                                }}@endif {!! $eula !!}</p>
                            @elseif(config('app.client') == 'rashmi')
                            <p> I {{ $user->getGuranteedNameText() }} {!! $eula !!}</p>
                            @else
                            <p> I {{ $user->username }} @if ($user->employee_num)/{{ $user->employee_num }}@endif {!!
                                $eula !!}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @if (count($vd->devices))
            <div class="row">
                <div class="col-md-12 mar-top-15 pad-no table-responsiv">
                    <label class="table_title"> Device List</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No.</th>
                                <th>Device Tag</th>
                                <th>Device Name</th>
                                <th>Model</th>
                                <th>Manufacturer</th>
                                <th>Serial</th>
                                <th>Checkout Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vd->devices as $device)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $device->asset_tag }}</td>
                                <td>{{ $device->name }}</td>
                                <td>{{ $device->mdl_name }}</td>
                                <td>{{ $device->mnf_name }}</td>
                                <td>{{ $device->serial }}</td>
                                <td>{{ $device->last_checkout_format }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @if (count($vd->accessories))
            <div class="row">
                <div class="col-md-12 mar-top-15 pad-no table-responsiv">
                    <label class="table_title"> Accessory List</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No.</th>
                                <th>Accessory Name</th>
                                <th>Expected Checkin Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vd->accessories as $accessory)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $accessory->acc_batch_no }}-{{ $accessory->name }}</td>
                                <td>{{ $accessory->expected_checkin_format }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @if (count($vd->consumables))
            <div class="row">
                <div class="col-md-12 mar-top-15 pad-no table-responsiv">
                    <label class="table_title"> Consumable List</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No.</th>
                                <th>Consumable Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vd->consumables as $consumable)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $consumable->con_batch_no }}-{{ $consumable->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @if (count($vd->components))
            <div class="row">
                <div class="col-md-12 mar-top-15 pad-no table-responsiv">
                    <label class="table_title"> Components List</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No.</th>
                                <th>Component Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vd->components as $component)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $component->unique_tag }}-{{ $component->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @if (count($vd->licenses))
            <div class="row">
                <div class="col-md-12 mar-top-15 pad-no table-responsiv">
                    <label class="table_title">License List</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No.</th>
                                <th>License Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vd->licenses as $license)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $license->lic_batch_no }}-{{ $license->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            <div class="signatur_div">
                <table class="signature-table">
                    <tr>
                        <td>
                            <p>_________________________</p>
                            <p><strong>(Signature of Receiver)</strong></p>

                            <div class="mar-top-40">
                                <p><strong>Employee Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>

                                @if(config('app.client') == 'rashmi')
                                <p><strong>User Name:</strong> {{ $user->username }}</p>
                                @endif

                                @if($user->employee_num)
                                <p><strong>Employee No:</strong> {{ $user->employee_num }}</p>
                                @endif

                                @if($user->jobtitle)
                                <p><strong>Designation:</strong> {{ $user->jobtitle }}</p>
                                @endif

                                @if($user->email)
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                @endif

                                @if($user->phone)
                                <p><strong>Mobile No:</strong> {{ $user->phone }}</p>
                                @endif

                                <p><strong>Date:</strong> {{ now()->format('Y-m-d') }}</p>
                            </div>
                        </td>

                        <td align="right">
                            <p>_________________________</p>
                            <p><strong>(Signature of Authorised Person)</strong></p>

                            <div class="mar-top-40">
                                <strong>{{ $user->company->name }}</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <footer>
            <p class="center_text">This is system generated pdf by ITM</p>
        </footer>
    </section>

    <script type="text/javascript">
        window.onload = function () {
    window.print();
};

window.onafterprint = function () {
    window.location.href = "{{ url('/user/info/' . $user->id) }}";
};
    </script>
</body>

</html>