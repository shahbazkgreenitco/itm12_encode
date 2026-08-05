{{-- @page-meta
{
    "page_no": "TAM-26",
    "file": "assigned.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-06",
        "reviewer": null,
        "description": "Task Assigned Mail"
        }
    ]
} 
--}}
@extends('mail.newMailTemplate')
@section('content')
@if(CommonHelper::settings()->css_theme == 2)
<style>
    .mail-content .tag-table th,
    .mail-content .tag-table td {
        background: #08558b;
        color: #ffffff;
    }
</style>
@elseif(CommonHelper::settings()->css_theme == 3)
<style>
    .mail-content .tag-table th,
    .mail-content .tag-table td {
        background: #CD3333;
        color: #ffffff;
    }
</style>
@else
<style>
    .mail-content .tag-table th,
    .mail-content .tag-table td {
        background: #CD3333;
        color: #ffffff;
    }
</style>
@endif
<style>
    body {
        font-family: "Plus Jakarta Sans";
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        font-size: 14px;
    }

    .container {
        max-width: 600px;
        margin: 20px auto;
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #F1F1F1;
    }

    .logo-container img {
        height: auto;
    }

    .header {
        text-align: center;
    }

    .header img {
        max-width: 150px;
    }

    .content {
        text-align: center;
        padding: 20px 0;
    }

    .content h2 {
        color: #333;
    }

    .mail-content .tag-table {
        width: 100%;
        margin-top: 10px;
        border-collapse: collapse;
    }

    .main-content .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .mail-content .table th,
    .mail-content .table td {
        padding: 10px;
        text-align: left;
        font-size: 14px;
    }

    .mail-content .table tr:nth-child(even) {
        background-color: #f4f4f4;
    }

    .mail-content .table tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .footer {
        text-align: center;
        font-size: 12px;
        color: #666;
    }

    hr {
        border: none;
        height: 1px;
        background-color: #e8e8e8;
        margin: 10px 0;
    }

    .mail-content h3 {
        text-align: left;
        font-weight: 550;
    }
</style>
<p>@if($user) Hello {{ $user->fullName() }}, @else Hello, @endif </p>

<p style="text-align: left;">You have been assigned a new task.</p>

<table class="table">
    @php $rowIndex = 0; @endphp

    {{-- Task ID (Top Rounded Row) --}}
    @if(!empty($task->id))
    <tr class="tag-table">
        <th style="border-top-left-radius:10px;">Task No</th>
        <td style="border-top-right-radius:10px;">#{{ $task->id }}</td>
    </tr>
    @endif

    @if(!empty($task->name))
    <tr style="background-color: {{ ($rowIndex++ % 2 === 0) ? '#f4f4f4' : '#ffffff' }}">
        <th>Task Name</th>
        <td>{{ $task->name }}</td>
    </tr>
    @endif

    @if(!empty($task->status) && !empty($task->status->name))
    <tr style="background-color: {{ ($rowIndex++ % 2 === 0) ? '#f4f4f4' : '#ffffff' }}">
        <th>Status</th>
        <td>{{ $task->status->name }}</td>
    </tr>
    @endif

    @if(!empty($task->priority) && !empty($task->priority->name))
    <tr style="background-color: {{ ($rowIndex++ % 2 === 0) ? '#f4f4f4' : '#ffffff' }}">
        <th>Priority</th>
        <td>{{ $task->priority->name }}</td>
    </tr>
    @endif

    @if(!empty($task->cost))
    <tr style="background-color: {{ ($rowIndex++ % 2 === 0) ? '#f4f4f4' : '#ffffff' }}">
        <th>Cost</th>
        <td>{{ $task->cost }}</td>
    </tr>
    @endif

    @if(!empty($task->type_id) && $task->type_id == 4 && !empty($task->ticket))
    <tr style="background-color: {{ ($rowIndex++ % 2 === 0) ? '#f4f4f4' : '#ffffff' }}">
        <th>Ticket</th>
        <td>#{{ $task->ticket->id }} - {{ $task->ticket->subject }}</td>
    </tr>
    @endif

    @if(!empty($task->start_date))
    <tr style="background-color: {{ ($rowIndex++ % 2 === 0) ? '#f4f4f4' : '#ffffff' }}">
        <th>Work Start Date</th>
        <td>{{ \Carbon\Carbon::parse($task->start_date)->format('d M Y h:i A') }}</td>
    </tr>
    @endif

    @if(!empty($task->due_date))
    <tr style="background-color: {{ ($rowIndex++ % 2 === 0) ? '#f4f4f4' : '#ffffff' }}">
        <th>Planned Completed Date</th>
        <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y h:i A') }}</td>
    </tr>
    @endif

</table>


{{-- <p style="text-align: left;">You can access the task on below link</p>
<p style="text-align: left;"><b><a href="{{ Config::get("app.url") }}/task-management/info/{{ Crypt::encrypt($task->id) }}"><u>Click Here</u></a></b></p> --}}


@if($site_name)
<p style="text-align: left;">Thanks,<br />{{ $site_name }}</p>
@else
<p style="text-align: left;">Thanks</p>
@endif
@endsection