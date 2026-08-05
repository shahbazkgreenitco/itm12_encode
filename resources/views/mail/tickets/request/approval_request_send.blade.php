{{-- @page-meta
{
    "page_no": "ARS05-27",
    "file": "approval_request_send.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-05",
        "reviewer": null,
        "description": "Approval Request Mail"
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
        "description": "Theme alignment - updated to match modern layout with greeting icon, approval tables, dynamic forms, and proper styling"
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

<!-- Greeting with Icon -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center" style="padding-bottom:18px;">
            <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block;">
                <tr>
                    <td style="padding-right:10px; vertical-align:middle;">
                        <span style="display:inline-block; width:28px; height:28px; border-radius:50%; background:#e8eef5; text-align:center; line-height:28px; vertical-align:middle; overflow:hidden;">
                            @if($user && $user->getProfileImg())
                                <img src="{{ $user->getProfileImg() }}" alt="{{ $user->getGuranteedNameText(true) }}" 
                                     style="width:28px; height:28px; border-radius:50%; display:block; object-fit:cover; border:none;"/>
                            @else
                                <svg viewBox="0 0 24 24" width="16" height="16" style="fill:#6b7c93; vertical-align:middle;">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            @endif
                        </span>
                    </td>
                    <td style="font-size:15px; color:#333; font-weight:400; vertical-align:middle; font-family:'Inter',Arial,sans-serif;">
                        Dear @if($user) {{ $user->getGuranteedNameText(true) }}@endif,
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Header - Approval Request -->
<h1 style="font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.35; margin:0 0 6px 0; font-family:'Inter',Arial,sans-serif;">
    Approval Request<br>
    <span style="color:{{ $themeColor }};">Pending Your Review</span>
</h1>
<p style="font-size:13px; color:#666; margin:0 0 4px 0; font-family:'Inter',Arial,sans-serif;">
    A {{$ticketProcureRequestObj->problemCategory->name}} has been raised in IT Request Portal
</p>
<p style="font-size:13px; color:#666; margin:0 0 24px 0; font-family:'Inter',Arial,sans-serif;">
    Following are the details for your review and approval/rejection @if($approvalRequest == 'delegated') delegated by {{ $actualUser->getGuranteedNameText(true) }} @endif.
</p>

<!-- Details Table -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
    
    @if(!empty($employee))
    <!-- Employee Name -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td width="35%" style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Employee Name</td>
        <td width="65%" style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $employee->getGuranteedNameText(true) }}</td>
    </tr>
    @endif

    @if(!empty($ticketProcureRequestObj->created_at))
    <!-- Ticket Raised Date -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket Raised Date</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ date("d M Y", strtotime($ticketProcureRequestObj->created_at)) }}</td>
    </tr>
    @endif

    @if(!empty($device))
    <!-- Device/Host Name -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Device/Host Name</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $device }}</td>
    </tr>
    @endif

    @if(!empty($ticketProcureRequestObj->created_at))
    <!-- Ticket Raised Time -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket Raised Time</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ date("h:i A", strtotime($ticketProcureRequestObj->created_at)) }}</td>
    </tr>
    @endif

    @if(!empty($location))
    <!-- Location -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Location</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $location }}</td>
    </tr>
    @endif

    @if(!empty($ticketProcureRequestObj->problemCategory->name))
    <!-- Ticket Category -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket Category</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="color:#7b2d8e; font-weight:550; background:#f8e6ff; border-radius:23px; padding:5px 10px; display:inline-block;">{{ $ticketProcureRequestObj->problemCategory->name }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($ticketProcureRequestObj->subCategory->name))
    <!-- Ticket Sub-Category -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket Sub-Category</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticketProcureRequestObj->subCategory->name }}</td>
    </tr>
    @endif

    @if(!empty($guid))
    <!-- GUID -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">GUID</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="font-family: monospace; background:#f8f9fa; padding:2px 8px; border-radius:4px;">{{ $guid }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($class_id))
    <!-- Class ID -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Class ID</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="font-family: monospace; background:#f8f9fa; padding:2px 8px; border-radius:4px;">{{ $class_id }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($approved_day))
    <!-- Period Access Required -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Period Access Required</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="color:#e30613; font-weight:600;">{{ $approved_day }} Day(s)</span>
        </td>
    </tr>
    @endif

    @if(!empty($ticketProcureRequestObj->content))
    <!-- Reason -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Reason</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <div style="word-break: break-word; white-space: normal; padding:8px 12px; background:#f8f9fa; border-radius:6px; border-left:3px solid {{ $themeColor }};">
                {!! $ticketProcureRequestObj->content !!}
            </div>
        </td>
    </tr>
    @endif
</table>

{{-- Approvals Table --}}
@if(!empty($groupedApprovals))
@foreach($groupedApprovals as $pabId => $approvals)
<h2 style="font-size:16px; font-weight:600; color:#1a1a1a; margin:0 0 12px 0; font-family:'Inter',Arial,sans-serif;">
    {{ $approvals->first()->pab_name->name ?? 'N/A' }}
</h2>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 24px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
    <tr style="background:{{ $themeColor }};">
        <th width="50%" style="{{ $thBase }} background-color:{{ $themeColor }}; color:#ffffff; border-bottom:1px solid #e6ebf1;">Approval Name</th>
        <th width="50%" style="{{ $thBase }} background-color:{{ $themeColor }}; color:#ffffff; border-bottom:1px solid #e6ebf1;">Approve Date</th>
    </tr>
    @php $approvalIndex = 0; @endphp
    @foreach($approvals as $a)
    @php $bg = ($approvalIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($a->user)->fullName() ?? 'N/A' }}</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $a->updated_at ? \Carbon\Carbon::parse($a->updated_at)->format('d M Y, h:i A') : '-' }}</td>
    </tr>
    @endforeach
</table>
@endforeach
@endif

{{-- Dynamic Form Details --}}
@if(!empty($requestForm) && isset($requestForm->field_values) && $customFormDataSetVal == 1)
<h2 style="font-size:16px; font-weight:600; color:#1a1a1a; margin:0 0 12px 0; font-family:'Inter',Arial,sans-serif;">
    Service Request – Dynamic Form Details
</h2>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 24px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
    <tr style="background:{{ $themeColor }};">
        <th width="35%" style="{{ $thBase }} background-color:{{ $themeColor }}; color:#ffffff; border-bottom:1px solid #e6ebf1;">Field</th>
        <th width="65%" style="{{ $thBase }} background-color:{{ $themeColor }}; color:#ffffff; border-bottom:1px solid #e6ebf1;">Value</th>
    </tr>
    @php
        $custom_form = json_decode($requestForm->field_values, true);
        $formData = (!isset($custom_form['fields'])) ? $custom_form : $custom_form['fields'];
        $formIndex = 0;
    @endphp
    @foreach($formData as $field)
        @php
            $label = strip_tags($field['label'] ?? 'N/A');
            $type  = $field['type'] ?? '';
            $value = 'N/A';
            $isImage = '';

            if (!empty($field['userData'])) {
                $value = implode(', ', $field['userData']);
            } elseif (!empty($field['value'])) {
                $value = $field['value'];
            }

            if($type === 'file') {
                $imgValue = $field['value'] ?? '';
                $imgValue = is_array($imgValue) ? reset($imgValue) : $imgValue;
                $fileExt = strtolower(pathinfo((string) $imgValue, PATHINFO_EXTENSION));
                $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
            }
        @endphp

        @if($type !== 'paragraph')
        @php $bg = ($formIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $label }}</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                @if($type === 'file' && $isImage != '' && !empty($field['value']))
                    <a href="{{ asset('uploads/'.$field['value']) }}" target="_blank" style="color:{{ $themeColor }}; text-decoration:underline;">
                        <img src="{{ asset('uploads/'.$field['value']) }}" alt="attachment" style="max-width:150px; max-height:120px; border-radius:6px; border:1px solid #e6ebf1;">
                    </a>
                @elseif($type === 'file' && !empty($field['value']))
                    <a href="{{ asset('uploads/'.$field['value']) }}" target="_blank" style="color:{{ $themeColor }}; text-decoration:underline;">{{ $field['value'] }}</a>
                @elseif($type === 'checkbox-group')
                    <span style="display:inline-block; padding:3px 10px; background:#28a745; color:#ffffff; border-radius:4px; font-size:12px; font-weight:500;">✓ {{ $value }}</span>
                @else
                    {{ $value }}
                @endif
            </td>
        </tr>
        @endif
    @endforeach
</table>
@elseif(!empty($valueArrayData))
<h2 style="font-size:16px; font-weight:600; color:#1a1a1a; margin:0 0 12px 0; font-family:'Inter',Arial,sans-serif;">
    Service Request – Dynamic Form Details
</h2>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 24px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
    <tr style="background:{{ $themeColor }};">
        <th width="35%" style="{{ $thBase }} background-color:{{ $themeColor }}; color:#ffffff; border-bottom:1px solid #e6ebf1;">Field</th>
        <th width="65%" style="{{ $thBase }} background-color:{{ $themeColor }}; color:#ffffff; border-bottom:1px solid #e6ebf1;">Value</th>
    </tr>
    @php $valueIndex = 0; @endphp
    @foreach($valueArrayData as $v)
    @php $bg = ($valueIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            @if(!empty($v['fieldName']))
                {!! $v['fieldName'] !!}
                @if(!empty($v['required']))
                    <sup style="color:#dc3545;">*</sup>
                @endif
            @endif
        </td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            @if(!is_array($v['fieldValue']))
                @if($v['type'] == 'header')
                    <strong style="color:{{ $themeColor }};">{!! $v['fieldValue'] !!}</strong>
                @else
                    {!! $v['fieldValue'] !!}
                @endif
            @else
                @foreach($v['fieldValue'] as $val)
                    @if($v['type'] == 'file' && !empty($val))
                        <div style="margin-bottom:5px;">
                            <a href="{{ url('uploads/request_form') }}/{{ $val }}" target="_blank" style="color:{{ $themeColor }}; text-decoration:underline;">📎 {{ $val }}</a>
                        </div>
                    @elseif(!in_array($v['type'], ['text','textarea','date','header']))
                        <span style="display:inline-block; padding:3px 10px; background:#eeeeee; margin:2px; border-radius:4px; font-size:12px; font-weight:500;">{{ str_replace('-', ' ', $val) }}</span>
                    @else
                        {{ $val }}
                    @endif
                @endforeach
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endif

{{-- View Ticket Link --}}
<p style="font-size:14px; color:#333; margin:0 0 16px 0; font-family:'Inter',Arial,sans-serif;">
    You can review the complete details using the link below.
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="left" style="padding-bottom:28px;">
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:10px 24px; background:{{ $themeColor }};">
                        <a href="{{ Config::get('app.url') }}/tickets/requestInfo/{{ $ticketProcureRequestObj->id }}" 
                           style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                            View Details →
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- Approval/Reject Buttons --}}
<p style="font-size:14px; color:#333; margin:0 0 16px 0; font-family:'Inter',Arial,sans-serif; font-weight:500;">Please take appropriate action:</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="left" style="padding-bottom:20px;">
            <!-- Approve Button -->
            <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block; margin-right:12px; margin-bottom:10px;">
                <tr>
                    <td align="center" bgcolor="#28a745" style="border-radius:6px; padding:10px 28px; background:#28a745;">
                        <a href="{{ url('tickets/request-approve/' . encrypt($ticketProcureRequestObj->id) . '/' . encrypt($user->id) . '/approve') }}" 
                           style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:#28a745; border-radius:6px;">
                            Approve ✓
                        </a>
                    </td>
                </tr>
            </table>
            
            <!-- Reject Button -->
            <table cellpadding="0" cellspacing="0" border="0" style="display:inline-block; margin-bottom:10px;">
                <tr>
                    <td align="center" bgcolor="#dc3545" style="border-radius:6px; padding:10px 28px; background:#dc3545;">
                        <a href="{{ url('tickets/request-reject/' . encrypt($ticketProcureRequestObj->id) . '/' . encrypt($user->id) . '/reject') }}" 
                           style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:#dc3545; border-radius:6px;">
                            Reject ✗
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<p style="text-align:left; font-family:'Inter',Arial,sans-serif; font-size:12px; color:#666666; margin:15px 0 0 0;">Note:- This is auto generated reply. Please do not reply to this</p>
@endsection