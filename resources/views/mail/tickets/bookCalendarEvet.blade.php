{{-- @page-meta
{
    "page_no": "EVIN-27",
    "file": "event_invitation.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-06",
        "reviewer": null,
        "description": "Event Invitation Mail"
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
        "description": "Theme alignment - updated to match modern layout with event icon, proper styling, and CTA"
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

<!-- Header - Event Invitation -->
<h1 style="font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.35; margin:0 0 6px 0; font-family:'Inter',Arial,sans-serif;">
    You're Invited to<br>
    <span style="color:{{ $themeColor }};">This Event!</span>
</h1>
<p style="font-size:13px; color:#666; margin:0 0 4px 0; font-family:'Inter',Arial,sans-serif;">
    We are excited to invite you to an upcoming event
</p>
<p style="font-size:13px; color:#666; margin:0 0 24px 0; font-family:'Inter',Arial,sans-serif;">
    Please review the details below and confirm your attendance.
</p>

<!-- Event Details Table -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
    
    @if(!empty($blockCalender->subject))
    <!-- Event -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td width="30%" style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1; font-weight:600;">Event</td>
        <td width="70%" style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="font-weight:600; color:{{ $themeColor }};">{{ $blockCalender->subject }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($blockCalender->start_date_time))
    <!-- Date -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Date</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="font-weight:500;">{{ \Carbon\Carbon::parse($blockCalender->start_date_time)->format('d M Y') }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($blockCalender->start_date_time))
    <!-- Time -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Time</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="font-weight:500;">{{ \Carbon\Carbon::parse($blockCalender->start_date_time)->format('h:i A') }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($blockCalender->end_date_time))
    <!-- End Time -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">End Time</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="font-weight:500;">{{ \Carbon\Carbon::parse($blockCalender->end_date_time)->format('h:i A') }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($blockCalender->location))
    <!-- Location -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Location</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="font-weight:500;">{{ $blockCalender->location }}</span>
        </td>
    </tr>
    @endif

    @if(!empty($blockCalender->description))
    <!-- Description -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Description</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <div style="word-break: break-word; white-space: normal; padding:8px 12px; background:#f8f9fa; border-radius:6px; border-left:3px solid {{ $themeColor }};">
                {{ $blockCalender->description }}
            </div>
        </td>
    </tr>
    @endif
</table>

<!-- Additional Information -->
<p style="font-size:14px; color:#333; margin:0 0 16px 0; font-family:'Inter',Arial,sans-serif; line-height:22px;">
    Please review the details and confirm your attendance. You can click on the attached file to add the event directly to your calendar.
</p>

<p style="font-size:14px; color:#333; margin:0 0 28px 0; font-family:'Inter',Arial,sans-serif; line-height:22px;">
    We look forward to your participation!
</p>

@if($site_name)
<p style="text-align:left; font-family:'Inter',Arial,sans-serif; font-size:14px; color:#333333; margin:0;">
    Thanks,<br/>
    @if($email_thankuby ?? false)
    {{ $email_thankuby }}<br/>
    @endif
    {{ $site_name }}
</p>
@else
<p style="text-align:left; font-family:'Inter',Arial,sans-serif; font-size:14px; color:#333333; margin:0;">Thanks,<br/>Support Team</p>
@endif

@endsection