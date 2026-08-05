<!doctype html>
<html>
    <head>
        <title>All Feedback Report</title>
        <style type="text/css">
            html, body {
                font-size: 9pt;
                font-family: sans-serif;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            thead {
                vertical-align: bottom;
                text-align: center;
            }
           th {
                text-align: left;
                padding-left: 0.35em;
                padding-right: 0.35em;
                padding-top: 0.35em;
                padding-bottom: 0.35em;
                vertical-align: top;
                font-weight: 600;
                border-bottom: .5pt solid #000;
            }
            td {
                padding-left: 0.35em;
                padding-right: 0.35em;
                padding-top: 0.35em;
                padding-bottom: 0.35em;
                vertical-align: top;
            }
            img {
                max-height: 32pt;
                max-width: 120pt;
            }
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
                margin-header: 5mm;
                margin-footer: 5mm;
                header: page-header;
                footer: page-footer;
                margin-top: 14mm;
                margin-bottom: 10mm;
                margin-left: 11mm;
                margin-right: 11mm;
            }
        </style>
    </head>
    <body>
        <htmlpageheader name="page-header">
            <div style="float:left; width:30%;">
                @if(CommonHelper::settings()->logo_thumbnail)
                    <img src="{{ public_path('uploads') . DIRECTORY_SEPARATOR . CommonHelper::settings()->logo_thumbnail }}" alt="Logo" class="brand-icon" />
                @else
                    <h3 style="font-weight:300; font-size:14pt;">{{ CommonHelper::settings()->site_name }}</h3>
                @endif
            </div>
            <div style="float:right; width:50%; text-align:right;">
                <div style="font-weight:100; font-size:11pt;">Feedback Tickets Report</div>
            </div>
        </htmlpageheader>

        <div style="padding:4mm 0 0 0">
        <div style="font-weight:100; font-size:10pt;">Available Count: {{ count($records) }}</div>
            <table>
                <thead>
                    <tr>
                        <th>{{ trans("content.ticket_report.Ticket_Id") }}</th>
                        <th>{{ trans("content.report_fields.Subject") }}</th>
                        <th>{{ trans("content.report_fields.Priority") }}</th>
                        <th>{{ trans("content.report_fields.Department") }}</th>
                        <th>{{ trans("content.report_fields.Problem_Category") }}</th>
                        <th>{{ trans("content.report_fields.Sub_Category") }}</th>
                        <th>{{ trans("content.ticket_report.resolved_at") }}</th>
                        <th>{{ trans("content.ticket_report.resolved_by") }}</th>
                        <th>{{ trans("content.ticket_report.Feedback_Created_At") }}</th>
                        <th>{{ trans("content.ticket_report.Feedback") }}</th>
                        <th>{{ trans("content.ticket_report.Feedback_Comment") }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($records as $d)
                    <tr>
                        <td>{{ $d->id }} </td>
                        <td>{{ $d->subject }} </td>
                        <td>{{ $d->priority_name }} </td>
                        <td>{{ $d->dept_name }} </td>
                        <td>{{ $d->cat_name }} </td>
                        <td>{{ $d->sub_cat_name }} </td>
                        <td>{{ $d->resolved_at }} </td>
                        <td>{{ $d->username }} </td>
                        <td>{{ $d->feedback_given_at }} </td>
                        <td>{{ $d->feedback }} </td>
                        <td>{{ $d->feedback_comment }} </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <htmlpagefooter name="page-footer">
            <div style="float:left; width:50%;">Page: {PAGENO}</div>
            <div style="float:right; width:50%; text-align:right;">Generated Date: {{ date('d/m/Y h:i a')}}</div>
        </htmlpagefooter>
    </body>
</html>
