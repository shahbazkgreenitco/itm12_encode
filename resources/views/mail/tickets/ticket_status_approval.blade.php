{{-- @page-meta
{
    "page_no": "STSA-27",
    "file": "ticket_status_approval.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-06",
        "reviewer": null,
        "description": "Ticket Status Approval Request Mail"
        },
        {
        "version": "1.1",
        "writer": "Priya Maru",
        "from": "2026-07",
        "reviewer": null,
        "description": "Outlook-compatible rewrite (inline styles, cell-level backgrounds, width attributes)"
        },
        {
        "version": "1.2",
        "writer": "shivam kumar",
        "from": "2026-07",
        "reviewer": null,
        "description": "Theme alignment - removed greeting icon, updated table styles to match theme"
        }
    ]
}
--}}
@extends('mail.ticket_mailtemplates')

@section('content')
@php
    // Theme colour (Outlook ignores <style> blocks in body, so it must be inline)
    $themeColor = CommonHelper::settings()->css_theme == 2 ? '#08558b' : '#e30613';
    
    // Reusable styles — Outlook (Word engine) needs padding, font,
    // line-height and background on every cell, not on <tr> or in <style>.
    $thBase = "padding:11px 16px; font-size:14px; line-height:20px; mso-line-height-rule:exactly; margin:0; text-align:left; font-family:'Inter',Arial,sans-serif; vertical-align:middle; font-weight:500; color:#4a5568;";
    $tdBase = "padding:11px 16px; font-size:14px; line-height:20px; mso-line-height-rule:exactly; margin:0; text-align:left; font-family:'Inter',Arial,sans-serif; color:#1a1a1a; font-weight:500; vertical-align:middle;";
    
    $rowIndex = 0;
@endphp

<!-- Header - Ticket Status Approval Request -->
<h1 style="font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.35; margin:0 0 6px 0; font-family:'Inter',Arial,sans-serif;">
    Ticket Status Approval<br>
    <span style="color:{{ $themeColor }};">Request</span>
</h1>
<p style="font-size:13px; color:#666; margin:0 0 4px 0; font-family:'Inter',Arial,sans-serif;">
    Ticket ID: <b>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</b>
</p>
<p style="font-size:13px; color:#666; margin:0 0 24px 0; font-family:'Inter',Arial,sans-serif;">
    Please review the status change request and take appropriate action.
</p>

<!-- Details Table -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
    
    @if(!empty($ticket->id))
    <!-- Ticket ID -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td width="38%" style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket ID</td>
        <td width="62%" style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->ticket_tag ?? '###'. $ticket->id."###" }}</td>
    </tr>
    @endif

    @if(!empty($ticket->subject))
    <!-- Subject -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Subject</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->subject ?? '-' }}</td>
    </tr>
    @endif

    @if(!empty($ticket->content))
    <!-- Content -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Content</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->content ?? '-' }}</td>
    </tr>
    @endif

    @if(!empty($ticket->created_at))
    <!-- Created At -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Created At</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->created_at }}</td>
    </tr>
    @endif

    @if(!empty(optional($ticket->status)->name))
    <!-- Current Status -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Current Status</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="color:#7b2d8e; font-weight:550; background:#f8e6ff; border-radius:23px; padding:5px 10px; display:inline-block;">{{ $ticket->status->name ?? '-' }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($requestData->status_id))
    <!-- Requested Status -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Requested Status</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="color:#e30613; font-weight:550; background:#ffe6e6; border-radius:23px; padding:5px 10px; display:inline-block;">{{ \App\Models\Ticket\Status::find($requestData->status_id)->name ?? '-' }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($requestData->start_time))
    <!-- Start Time -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Start Time</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $requestData->start_time }}</td>
    </tr>
    @endif

    @if(!empty($requestData->end_time))
    <!-- End Time -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">End Time</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $requestData->end_time }}</td>
    </tr>
    @endif

    @if(!empty($requestData->reason))
    <!-- Reason -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Reason</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{!! $requestData->reason !!}</td>
    </tr>
    @endif
</table>

<!-- Approvers Table -->
<h2 style="font-size:16px; font-weight:600; color:#1a1a1a; margin:0 0 16px 0; font-family:'Inter',Arial,sans-serif;">Approvers</h2>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
    
    @if(!empty($approvers))
    <!-- Approvers Header Row -->
    @php $headerBg = $themeColor; @endphp
    <tr style="background:{{ $headerBg }};">
        <th width="33%" style="{{ $thBase }} background-color:{{ $headerBg }}; color:#ffffff; border-bottom:1px solid #e6ebf1; font-weight:600;">Name</th>
        <th width="33%" style="{{ $thBase }} background-color:{{ $headerBg }}; color:#ffffff; border-bottom:1px solid #e6ebf1; font-weight:600;">Email</th>
        <th width="34%" style="{{ $thBase }} background-color:{{ $headerBg }}; color:#ffffff; border-bottom:1px solid #e6ebf1; font-weight:600;">Status</th>
    </tr>
    
    @php $approverIndex = 0; @endphp
    @foreach($approvers as $approver)
    @php $bg = ($approverIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $approver['full_name'] ?? '-' }}</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $approver['email'] ?? '-' }}</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="color:#f59e0b; font-weight:550; background:#fef3c7; border-radius:23px; padding:5px 10px; display:inline-block; font-size:13px;">Pending</span>
        </td>
    </tr>
    @endforeach
    @endif
</table>

<!-- Action Buttons Section -->
<p style="font-size:14px; color:#333; margin:0 0 16px 0; font-family:'Inter',Arial,sans-serif; font-weight:500;">Please take appropriate action:</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="left" style="padding-bottom:32px;">
            <!-- Approve Button -->
            <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block; margin-right:12px; margin-bottom:10px;">
                <tr>
                    <td align="center" bgcolor="#28a745" style="border-radius:6px; padding:10px 24px; background:#28a745;">
                        <a href="{{ url('ticket-status-approval/approve/' . $ticket->id . '/' . $user_id) }}" 
                           style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:#28a745; border-radius:6px;">
                            Approve ✓
                        </a>
                    </td>
                </tr>
            </table>
            
            <!-- Reject Button -->
            <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block; margin-right:12px; margin-bottom:10px;">
                <tr>
                    <td align="center" bgcolor="#dc3545" style="border-radius:6px; padding:10px 24px; background:#dc3545;">
                        <a href="{{ url('ticket-status-approval/reject/' . $ticket->id . '/' . $user_id) }}" 
                           style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:#dc3545; border-radius:6px;">
                            Reject ✗
                        </a>
                    </td>
                </tr>
            </table>
            
            <!-- Other Action Button -->
            <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block; margin-bottom:10px;">
                <tr>
                    <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                        <a href="{{ url('ticket-status-approval/view/' . $ticket->id . '/' . $user_id) }}" 
                           style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                            Other Action
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection