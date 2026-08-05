{{-- @page-meta
{
  "page_no": "USR11PE-26",
  "file": "pdf-export.blade.php",
  "versions": [
    {
      "version": "1.0",
      "writer": "Sandeep Verma",
      "from": "2026-03",
      "reviewer": null,
      "description": "Initial setup"
    }
  ]
}
--}}
<!doctype html>
<html>
    <head>
        <title>User List</title>
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
                <div style="font-weight:100; font-size:11pt;">User List</div>
            </div>
        </htmlpageheader>

        <div style="padding:4mm 0 0 0">
            <div style="font-weight:100; font-size:10pt;">Available Count: {{ count($records) }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Username</th>
                        <th>Designation</th>
                        <th>Company Name</th>
                        <th>Department Name</th>
                        <th>Active Status</th>
                        <th>Date of Joining</th>
                        <th>Last Working Date</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Job Type</th>
                        <th>External Company Name</th>
                        <th>Alternative Phone</th> 
                        <th>Work Phone</th> 
                        <th>Address</th>
                        <th>Manager</th>
                        <th>Location</th>
                        <th>Notes</th>
                        <th>Groups</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $d)
                        <tr>
                            <td>{{ $d->employee_num }} </td>
                            <td>{{ $d->full_name }} </td>
                            <td>{{ $d->jobtitle }} </td>
                            <td>{{ $d->cmp_name }} </td>
                            <td>{{ $d->dept_name }} </td>
                            <td>{{ $d->user_status }} </td>
                            <td>{{ $d->date_of_joining }} </td>
                            <td>{{ $d->last_working_date }} </td>
                            <td>{{ $d->email }} </td>
                            <td>{{ $d->phone }} </td>
                            <td>{{ $d->job_type_text }} </td>
                            <td>{{ $d->ex_user_company }} </td>
                            <td>{{ $d->phone2 }} </td> 
                            <td>{{ $d->work_phone }} </td> 
                            <td>{{ $d->address }} </td>
                            <td>{{ $d->manager_name }} </td>
                            <td>{{ $d->location_name }} </td>
                            <td>{{ $d->notes }} </td>
                            <td>
                            @foreach($fields_group as $field)
                            <span>{{ $field }}</span>
                            @endforeach
                            </td>
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