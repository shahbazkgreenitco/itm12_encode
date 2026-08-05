{{-- @page-meta
{
  "page_no": "INCMD-04",
  "file": "for_incident_detail.blade",
  "versions": [
    {
      "version": "1.0",
      "writer": "Muzaffar Shaikh",
      "from": "2026-04",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans("content.ticket_incident.ticket_incident") }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #222222;
        }

        /* ── PAGE HEADER ── */
        htmlpageheader[name="page-header"] {
            display: block;
            width: 100%;
            padding: 10px 30px 8px 30px;
            border-bottom: 1px solid #cccccc;
        }

        htmlpageheader[name="page-header"]::after {
            content: "";
            display: table;
            clear: both;
        }

        .header-left {
            float: left;
            width: 40%;
        }

        .header-left img {
            max-height: 48px;
            width: auto;
        }

        .header-right {
            float: right;
            width: 55%;
            text-align: right;
            padding-top: 14px;
            font-size: 9pt;
            color: #555555;
        }

        /* ── REPORT TITLE ── */
        .report-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            color: #111111;
            padding: 18px 30px 14px 30px;
            letter-spacing: 0.01em;
        }

        /* ── CONTAINER ── */
        .container {
            padding: 0 30px 40px 30px;
        }

        /* ── TABLE ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 36px;
            table-layout: fixed;  /* Columns fixed rahenge, shrink nahi honge */
        }

        caption {
            display: none;
        }

        th,
        td {
            padding: 7px 12px;
            vertical-align: top;
            border: 1px solid #cccccc;
            text-align: left;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        th {
            width: 35%;
            font-weight: bold;
            font-size: 9pt;
            color: #222222;
            background-color: #f5f5f5;
            white-space: nowrap; /* th text wrap nahi hoga, column fixed rahega */
        }

        td {
            width: 65%;
            font-size: 9pt;
            color: #333333;
            font-weight: normal;
            background-color: #ffffff;
        }

        /* ── PAGE FOOTER ── */
        htmlpagefooter[name="page-footer"] {
            display: block;
            border-top: 1px solid #cccccc;
            padding: 7px 30px;
        }

        htmlpagefooter[name="page-footer"]::after {
            content: "";
            display: table;
            clear: both;
        }

        .footer-left {
            float: left;
            font-size: 8.5pt;
            color: #555555;
        }

        .footer-right {
            float: right;
            font-size: 8.5pt;
            color: #555555;
        }

        /* ── PRINT STYLES ── */
        @media print {
            table {
                table-layout: fixed !important;
            }

            th {
                width: 35% !important;
                white-space: nowrap !important;
            }

            td {
                width: 65% !important;
            }
        }
    </style>
</head>

<body>

    <htmlpageheader name="page-header">
        <div class="header-left">
            <img src="{{ CommonHelper::CompLogo() }}" alt="Logo" />
        </div>
        <div class="header-right">
            {{ trans("content.ticket_incident.ticket_incident") }}
        </div>
    </htmlpageheader>

    <div class="report-title">{{ trans("content.ticket_incident.ticket_incident_report") }}</div>

    <div class="container">
        @foreach($data as $d)
            <table>
                <caption>{{ trans("content.ticket_incident.ticket_incident_report") }}</caption>
                <colgroup>
                    <col style="width: 35%;" />
                    <col style="width: 65%;" />
                </colgroup>
                <tbody>
                    <tr>
                        <th>{{ trans("content.ticket_incident.company") }}</th>
                        <td>{{ $d->company_name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.ticket_id") }}</th>
                        <td>{{ $d->ticket_id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.department_impacted") }}</th>
                        <td>{{ $d->dept_name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.created_by") }}</th>
                        <td>{{ $d->username }}</td>
                    </tr>
                    @if (isset($d->location_name))
                        <tr>
                            <th>{{ trans("content.ticket_incident.location") }}</th>
                            <td>{{ $d->location_name }}</td>
                        </tr>
                    @endif
                    @if (isset($d->place_name))
                        <tr>
                            <th>{{ trans("content.ticket_incident.internal_place") }}</th>
                            <td>{{ $d->place_name }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>{{ trans("content.ticket_incident.problem_category") }}</th>
                        <td>{{ $d->cat_name }}</td>
                    </tr>
                    @if (isset($d->sub_cat_name))
                        <tr>
                            <th>{{ trans("content.ticket_incident.sub_category") }}</th>
                            <td>{{ $d->sub_cat_name }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>{{ trans("content.ticket_incident.priority") }}</th>
                        <td>{{ $d->priority_name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.financial_loss_amount") }}</th>
                        <td>{{ $d->financial_loss_amount }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.man_hour_loss") }}</th>
                        <td>{{ $d->man_hour_loss }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.service_impacted") }}</th>
                        <td>{{ $d->service_impacted }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.sla_breaches") }}</th>
                        <td>{{ $d->sla_breaches }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.subject") }}</th>
                        <td>{{ $d->subject }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.status") }}</th>
                        <td>{{ $d->status }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.incident_created_date") }} &amp; {{ trans("content.ticket_incident.time") }}</th>
                        <td>{{ $d->incident_start_date }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.incident_end_date") }} &amp; {{ trans("content.ticket_incident.time") }}</th>
                        <td>{{ $d->incident_end_date }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.content") }}</th>
                        <td>{!! $d->content !!}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.rca") }}</th>
                        <td>{!! $d->rca !!}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.why_incident_happened") }}</th>
                        <td>{!! $d->why_incident_happen !!}</td>
                    </tr>
                    <tr>
                        <th>{{ trans("content.ticket_incident.preventive_measure_taken") }}</th>
                        <td>{!! $d->preventive_measure_taken !!}</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>

    <script>
        window.print();
    </script>

</body>

</html>