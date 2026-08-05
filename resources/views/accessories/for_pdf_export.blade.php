//new
{{--
/**
------------------------------------------------------------
File: for_pdf_export.blade.php
Module: Accessories
ACC/26/05
------------------------------------------------------------
Version: 1.0.1
Author: Safdar Ali
Page ID: #ACC-012
Created On: 2026-05-12
Reviewed By: -
------------------------------------------------------------
Purpose:
Accessories Checkout PDF Export

------------------------------------------------------------
Change Log:
[1.0.0] - Initial version
[1.0.1] - Updated PDF generation to use Spatie Laravel PDF package and redesigned UI - Safdar Ali - 2026-06-11
------------------------------------------------------------
*/
--}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Accessory Checkout Details</title>

    <style>
        @page {
            margin: 100px 20px 50px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 70px;
            border-bottom: 2px solid #dcdcdc;
            padding-bottom: 10px;
            background: #fff;
        }

        .logo-section {
            float: left;
            width: 35%;
        }

        .logo-section img {
            max-height: 50px;
            max-width: 180px;
        }

        .title-section {
            float: right;
            width: 60%;
            text-align: right;
        }

        .title-section h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .title-section p {
            margin: 5px 0 0;
            font-size: 11px;
            color: #666;
        }

        .clearfix {
            clear: both;
        }

        .summary {
            margin-bottom: 15px;
            padding: 8px 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }

        .summary strong {
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f3f4f6;
        }

        th {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
        }

        td {
            border: 1px solid #d1d5db;
            padding: 6px;
            font-size: 9px;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            border-top: 1px solid #dcdcdc;
            padding-top: 5px;
            font-size: 10px;
            color: #666;
        }

        .footer-left {
            float: left;
        }

        .footer-right {
            float: right;
        }

        .page-number {
            position: fixed;
            bottom: -35px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .page-number:after {
            content: "Page " counter(page);
        }
    </style>
</head>

<body>

    @php
    $logoPath = public_path('uploads/' . CommonHelper::settings()->logo_thumbnail);
    @endphp

    <div class="header">

        <div class="logo-section">
            @if(CommonHelper::settings()->logo_thumbnail && file_exists($logoPath))
            <img src="data:image/{{ pathinfo($logoPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                alt="Logo">
            @else
            <strong>{{ CommonHelper::settings()->site_name }}</strong>
            @endif
        </div>

        <div class="title-section">
            <h1>Accessory Checkout Details</h1>
            <p>Checkout Export Report</p>
        </div>

        <div class="clearfix"></div>

    </div>

    <div class="summary">
        <strong>Total Records:</strong> {{ count($records) }}
        <br>
        <strong>Generated On:</strong> {{ date('d/m/Y h:i A') }}
    </div>

    <table>

        <thead>
            <tr>
                <th>Tag</th>
                <th>Accessory Name</th>
                <th>Category</th>
                <th>Company</th>
                <th>Assigned To</th>
                <th>User / Place / Device</th>
                <th>Purchase Date</th>
                <th>Expected Checkin Date</th>
            </tr>
        </thead>

        <tbody>

            @forelse($records as $d)

            <tr>
                <td>{{ $d->tag }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->category_name }}</td>
                <td>{{ $d->company_name }}</td>
                <td>{{ $d->target_type }}</td>
                <td>{{ $d->target_name }}</td>
                <td>{{ $d->purchase_date_on }}</td>
                <td>{{ $d->expected_checkin_format }}</td>
            </tr>

            @empty

            <tr>
                <td colspan="8" class="text-center">
                    No records found
                </td>
            </tr>

            @endforelse

        </tbody>

    </table>

    <div class="footer">

        <div class="footer-left">
            Generated By: {{ CommonHelper::settings()->site_name }}
        </div>

        <div class="footer-right">
            {{ date('d/m/Y h:i A') }}
        </div>

        <div class="clearfix"></div>

    </div>

    <div class="page-number"></div>

</body>

</html>