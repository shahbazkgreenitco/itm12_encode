{{-- @page-meta
{
    "page_no": null,
    "file": "waiting_for_user_reminder.blade.php",
    "versions": [
        {
        "version": "1.1",
        "writer": "santosh upadhyay",
        "from": "2026-07",
        "reviewer": null,
        "description": "Outlook-compatible rewrite (inline styles, cell-level backgrounds); removed duplicated reminder paragraphs"
        },
        {
        "version": "1.2",
        "writer": "shivam kumar",
        "from": "2026-07",
        "reviewer": null,
        "description": "New modern layout with greeting icon, reminder header, details table, warning message, and CTA"
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
    $pBase  = "text-align:left; font-family:'Inter',Arial,sans-serif; font-size:14px; line-height:20px; mso-line-height-rule:exactly; color:#333333; margin:0 0 12px 0;";
    
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
                                @if($ticket && $ticket->creator && $ticket->creator->getProfileImg())
                                    <img src="{{ $ticket->creator->getProfileImg() }}" alt="{{ $ticket->creator_name }}" 
                                         style="width:28px; height:28px; border-radius:50%; display:block; object-fit:cover; border:none;"/>
                                @else
                                    <svg viewBox="0 0 24 24" width="16" height="16" style="fill:#6b7c93; vertical-align:middle;">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                @endif
                            </span>
                        </td>
                        <td style="font-size:15px; color:#333; font-weight:400; vertical-align:middle; font-family:'Inter',Arial,sans-serif;">
                            Hello @if($ticket) {{ $ticket->creator_name }}@else User @endif,
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Header - Waiting for User Reminder -->
    <h1 style="font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.35; margin:0 0 6px 0; font-family:'Inter',Arial,sans-serif;">
        Your input is required<br>
        <span style="color:{{ $themeColor }};">on ticket #{{ $ticket->ticket_tag ?? $ticket->id }}</span>
    </h1>
    <p style="font-size:13px; color:#666; margin:0 0 4px 0; font-family:'Inter',Arial,sans-serif;">
        Ticket is currently marked as <strong>"Waiting for the User"</strong>
    </p>
    <p style="font-size:13px; color:#666; margin:0 0 24px 0; font-family:'Inter',Arial,sans-serif;">
        We are yet to receive your input.
    </p>

    <!-- Details Table -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
        
        <!-- Ticket ID -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td width="38%" style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket ID</td>
            <td width="62%" style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</td>
        </tr>

        <!-- Creator Name -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Creator Name</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($ticket->creator)->fullName() ?? 'N/A' }}</td>
        </tr>

        <!-- Assigned To -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Assigned To</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                <table cellpadding="0" cellspacing="0" border="0" style="display:inline-table;">
                    <tr>
                        <td style="padding-right:6px; vertical-align:middle;">
                            <span style="display:inline-block; width:22px; height:22px; border-radius:50%; background:#e8eef5; text-align:center; line-height:22px; vertical-align:middle; overflow:hidden;">
                                <svg viewBox="0 0 24 24" width="12" height="12" style="fill:#6b7c93; vertical-align:middle; display:inline-block;">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </span>
                        </td>
                        <td style="vertical-align:middle;">{{ optional($ticket->assignedTo)->fullName() ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Status -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Status</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                <span style="color:#7b2d8e; font-weight:550; background:#f8e6ff; border-radius:23px; padding:5px 10px; display:inline-block;">Waiting for User</span>
            </td>
        </tr>

        <!-- Response Required -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Response Required</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                @if ($ticket->days_left_to_close > 0)
                    Ticket will be auto resolved in <strong>{{ $ticket->days_left_to_close }} day(s)</strong>
                @else
                    <strong>Ticket will be auto resolved today</strong>
                @endif
            </td>
        </tr>

        <!-- Engineer Comment -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Engineer Comment</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments ?? [], true) !!}</td>
        </tr>
    </table>

    <!-- Reminder Message -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:left; margin-bottom:24px;">
        <tr>
            <td>
                <p style="font-size:14px; color:#333; line-height:1.6; margin:0 0 12px 0; font-family:'Inter',Arial,sans-serif;">
                    Your input is required to proceed further with the resolution. You may reply to this email or update the ticket with the required information.
                </p>
                <p style="font-size:14px; color:#e30613; line-height:1.6; margin:0 0 12px 0; font-family:'Inter',Arial,sans-serif; font-weight:500;">
                    ⚠️ If no response is received within 3 days, the ticket will be auto resolved by the system. You may reopen it within 3 business days if required.
                </p>
            </td>
        </tr>
    </table>

    <!-- CTA Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding-bottom:32px;">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:12px 28px; background:{{ $themeColor }};">
                            <a href="{{ url('ticket/' . $ticket->id) }}" style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                View and Update Ticket <span style="font-size:16px;">→</span>
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection