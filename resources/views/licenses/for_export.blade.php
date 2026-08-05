<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>License List</title>
    <style>
    @page {
        size: A4 landscape;
        margin: 80px 10px 45px 10px;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 7px;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .header {
        position: fixed;
        top: -65px;
        left: 0;
        right: 0;
        height: 55px;
        border-bottom: 2px solid #dcdcdc;
        padding-bottom: 6px;
        background: #fff;
    }

    .logo-section {
        float: left;
        width: 35%;
    }

    .logo-section img {
        max-height: 40px;
        max-width: 150px;
    }

    .title-section {
        float: right;
        width: 60%;
        text-align: right;
    }

    .title-section h1 {
        margin: 0;
        font-size: 15px;
        font-weight: bold;
    }

    .title-section p {
        margin: 2px 0 0;
        font-size: 8px;
        color: #666;
    }

    .clearfix {
        clear: both;
    }

    .summary {
        margin-bottom: 10px;
        padding: 5px 8px;
        background: #f5f5f5;
        border: 1px solid #ddd;
        font-size: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    thead {
        background: #f3f4f6;
    }

    th {
        border: 1px solid #d1d5db;
        padding: 3px;
        font-size: 6px;
        font-weight: bold;
        text-align: left;
        word-break: break-word;
    }

    td {
        border: 1px solid #d1d5db;
        padding: 3px;
        font-size: 6px;
        vertical-align: top;
        word-break: break-word;
    }

    tbody tr:nth-child(even) {
        background: #fafafa;
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
        padding-top: 4px;
        font-size: 8px;
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
        bottom: -32px;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8px;
        color: #666;
    }

    .page-number:after {
        content: "Page " counter(page);
    }

    /* Column Widths */

    th:nth-child(1),
    td:nth-child(1) {
        width: 5%;
    }

    th:nth-child(2),
    td:nth-child(2) {
        width: 6%;
    }

    th:nth-child(3),
    td:nth-child(3) {
        width: 7%;
    }

    th:nth-child(4),
    td:nth-child(4) {
        width: 9%;
    }

    th:nth-child(5),
    td:nth-child(5) {
        width: 7%;
    }

    th:nth-child(6),
    td:nth-child(6) {
        width: 7%;
    }

    th:nth-child(7),
    td:nth-child(7) {
        width: 9%;
    }

    th:nth-child(8),
    td:nth-child(8) {
        width: 4%;
    }

    th:nth-child(9),
    td:nth-child(9) {
        width: 5%;
    }

    th:nth-child(10),
    td:nth-child(10) {
        width: 6%;
    }

    th:nth-child(11),
    td:nth-child(11) {
        width: 6%;
    }

    th:nth-child(12),
    td:nth-child(12) {
        width: 5%;
    }

    th:nth-child(13),
    td:nth-child(13) {
        width: 5%;
    }

    th:nth-child(14),
    td:nth-child(14) {
        width: 6%;
    }

    th:nth-child(15),
    td:nth-child(15) {
        width: 6%;
    }

    th:nth-child(16),
    td:nth-child(16) {
        width: 5%;
    }

    th:nth-child(17),
    td:nth-child(17) {
        width: 7%;
    }

    th:nth-child(18),
    td:nth-child(18) {
        width: 5%;
    }

    th:nth-child(19),
    td:nth-child(19) {
        width: 7%;
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
            <h1>License List</h1>
            <p>Inventory Export Report</p>
        </div>

        <div class="clearfix"></div>

    </div>

    <div class="summary">
        <strong>Available Count:</strong> {{ count($records) }}
        <br>
        <strong>Generated On:</strong> {{ date('d/m/Y h:i A') }}
    </div>

    <table>

        <thead>
            <tr>
                <th>Batch No</th>
                <th>Unique Tag</th>
                <th>Company</th>
                <th>License</th>
                <th>Category</th>
                <th>Serial</th>
                <th>License Email</th>
                <th>Seats</th>
                <th>Available Seats </th>
                <th>Purchase Date</th>
                <th>Expiry Date</th>
                <th>Expired</th>
                <th>Currency</th>
                <th>Purchase Cost</th>
                <th>Order No.</th>
                <th>Support</th>
                <th>Supplier</th>
                <th>Network Count</th>
                <th>Check All Versions</th>
            </tr>
        </thead>

        <tbody>

            @forelse($records as $d)

                <tr>
                    <td>{{ $d->batch_no }}</td>
                    <td>{{ $d->unique_tag }}</td>
                    <td>{{ $d->cmp_name }}</td>
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->cat_name }}</td>
                    <td>{{ $d->serial_value }}</td>
                    <td>{{ $d->license_email }}</td>
                    <td>{{ $d->seats }}</td>
                    <td>{{ $d->available_seats }}</td>
                    <td>{{ $d->purchase_date_on }}</td>
                    <td>{{ $d->expire_date_on }}</td>
                    <td>{{ $d->is_expired }}</td>
                    <td>{{ $d->currency_code }}</td>
                    <td>{{ $d->purchase_cost_format }}</td>
                    <td>{{ $d->order_number }}</td>
                    <td>{{ $d->support }}</td>
                    <td>{{ $d->supplier_name }}</td>
                    <td>{{ $d->network_count }}</td>
                    <td>{{ $d->check_all_versions ? 'True' : 'False' }}</td>
                </tr>
            @empty

                <tr>
                    <td colspan="19" class="text-center">
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