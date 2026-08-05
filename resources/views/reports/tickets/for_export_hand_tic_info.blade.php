<!doctype html>
<html>
    <head>
        <title>Handler Tickets Info</title>
        <style type="text/css">
            /* ----- Reset & Base ----- */
            html, body {
                font-size: 9pt;
                font-family: 'Segoe UI', Arial, sans-serif;
                margin: 0;
                padding: 0;
                color: #333;
            }

            /* ----- Header / Footer ----- */
            @page {
                margin-header: 5mm;
                margin-footer: 5mm;
                header: page-header;
                footer: page-footer;
                margin-top: 17mm;
                margin-bottom: 10mm;
                margin-left: 11mm;
                margin-right: 11mm;
            }
            @page :first {
                margin-top: 14mm;
                margin-bottom: 10mm;
                margin-left: 11mm;
                margin-right: 11mm;
            }

            /* ----- Custom Header/Footer Elements ----- */
            .page-header {
                width: 100%;
                border-bottom: 1pt solid #e0e0e0;
                padding-bottom: 3mm;
                margin-bottom: 5mm;
            }
            .page-header .brand {
                float: left;
                width: 60%;
            }
            .page-header .brand img {
                max-height: 15mm;
                vertical-align: middle;
            }
            .page-header .brand h3 {
                display: inline;
                margin: 0;
                font-weight: 300;
                font-size: 12pt;
                color: #555;
            }
            .page-header .report-title {
                float: right;
                width: 40%;
                text-align: right;
                font-size: 10pt;
                font-weight: 600;
                color: #111;
                padding-top: 2mm;
            }
            .clearfix {
                clear: both;
            }

            .page-footer {
                width: 100%;
                font-size: 8pt;
                color: #888;
                border-top: 1pt solid #e0e0e0;
                padding-top: 2mm;
                margin-top: 5mm;
            }
            .page-footer .left { float: left; }
            .page-footer .right { float: right; }

            /* ----- Summary ----- */
            .record-summary {
                margin: 4mm 0 2mm;
                font-size: 9pt;
                font-weight: 600;
                color: #444;
            }

            /* ----- Table ----- */
            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 2mm;
                font-size: 8.5pt;
            }
            thead {
                display: table-header-group;
            }
            th {
                background-color: #f5f5f5;
                color: #222;
                font-weight: 600;
                text-align: left;
                padding: 6px 8px;
                border-bottom: 2pt solid #ccc;
                white-space: nowrap;
            }
            td {
                padding: 5px 8px;
                border-bottom: 0.5pt solid #eaeaea;
                vertical-align: top;
            }
            tr:nth-child(even) td {
                background-color: #fafafa;
            }
            img {
                max-height: 32pt;
                max-width: 120pt;
            }
        </style>
    </head>
    <body>
        <htmlpageheader name="page-header">
            <div class="page-header">
                <div class="brand">
                    @if(CommonHelper::settings()->logo_thumbnail)
                        <img src="{{ public_path('uploads') . DIRECTORY_SEPARATOR . CommonHelper::settings()->logo_thumbnail }}" alt="Logo" />
                    @else
                        <h3>{{ CommonHelper::settings()->site_name }}</h3>
                    @endif
                </div>
                <div class="report-title">Handler Tickets Info</div>
                <div class="clearfix"></div>
            </div>
        </htmlpageheader>

        <div class="record-summary">Available Count: {{ count($records) }}</div>

        <table>
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Ticket Subject</th>
                    <th>Department</th>
                    <th>Problem Category</th>
                    <th>Sub Category</th>
                    <th>Status</th>
                    <th>Ticket Creator</th>
                    <th>Ticket Creator Email</th>
                    <th>Ticket Handler</th>
                    <th>Ticket Handler Email</th>
                    <th>TAT</th>
                    <th>Priority</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $d)
                    <tr>
                        <td>{{ $d->ticket_id }}</td>
                        <td>{{ $d->subject }}</td>
                        <td>{{ $d->dept_name }}</td>
                        <td>{{ $d->problem_category }}</td>
                        <td>{{ $d->sub_category }}</td>
                        <td>{{ $d->status }}</td>
                        <td>{{ $d->creator }}</td>
                        <td>{{ $d->creatoremail }}</td>
                        <td>{{ $d->handler_full_name }}</td>
                        <td>{{ $d->handler_email }}</td>
                        <td>{{ $d->tat_time }}</td>
                        <td>{{ $d->priority }}</td>
                        <td>{{ $d->updated_at_format }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <htmlpagefooter name="page-footer">
            <div class="page-footer">
                <div class="left">Page: {PAGENO}</div>
                <div class="right">Generated Date: {{ date('d/m/Y h:i a') }}</div>
                <div class="clearfix"></div>
            </div>
        </htmlpagefooter>
    </body>
</html>