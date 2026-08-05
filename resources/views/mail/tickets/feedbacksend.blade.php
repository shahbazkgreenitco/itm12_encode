{{-- @page-meta
{
    "page_no": "STF-27",
    "file": "user_feedback.blade.php",
    "versions": [
        {
        "version": "1.0",
        "writer": "Priya Maru",
        "from": "2026-06",
        "reviewer": null,
        "description": "Ticket Feedback Mail"
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
        "description": "Theme alignment - updated to match modern layout with greeting icon, feedback display, and proper styling"
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
                        Hello @if($user) {{ $user->getGuranteedNameText(true) }}@else User @endif,
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Header - Feedback Received -->
<h1 style="font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.35; margin:0 0 6px 0; font-family:'Inter',Arial,sans-serif;">
    You received valuable<br>
    <span style="color:{{ $themeColor }};">feedback</span>
</h1>
<p style="font-size:13px; color:#666; margin:0 0 4px 0; font-family:'Inter',Arial,sans-serif;">
    Ticket ID: <b>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</b>
</p>
<p style="font-size:13px; color:#666; margin:0 0 24px 0; font-family:'Inter',Arial,sans-serif;">
    The details of feedback are below.
</p>

<!-- Details Table -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
    
    @if(!empty($ticket->id))
    <!-- Ticket -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td width="40%" style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket</td>
        <td width="60%" style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</td>
    </tr>
    @endif

    @if(!empty($ticket->decodedSubject()))
    <!-- Ticket Subject -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket Subject</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->decodedSubject() }}</td>
    </tr>
    @endif

    @if(!empty(optional($ticket->department)->name))
    <!-- Department -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Department</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($ticket->department)->name }}</td>
    </tr>
    @endif

    @if(!empty(optional($ticket->problemCategory)->name))
    <!-- Category -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Category</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($ticket->problemCategory)->name }}</td>
    </tr>
    @endif

    @if(!empty(optional($ticket->subCategory)->name))
    <!-- Sub Category -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Sub Category</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($ticket->subCategory)->name }}</td>
    </tr>
    @endif

    @if(!empty($engineer_name))
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
                    <td style="vertical-align:middle;">{{ $engineer_name }}</td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    @if(!empty($add_feed_back))
    <!-- Feedback Rating -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Feedback Rating</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <span style="color:#f59e0b; font-weight:600; background:#fef3c7; border-radius:23px; padding:5px 12px; display:inline-block; font-size:14px;">
                ⭐ {{ $add_feed_back }}/5
            </span>
        </td>
    </tr>
    @endif

    @if(!empty($tf->remarks ?? ''))
    <!-- Feedback Comment -->
    @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
    <tr style="background:{{ $bg }};">
        <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Feedback Comment</td>
        <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
            <div style="word-break: break-word; white-space: normal; padding:8px 12px; background:#f8f9fa; border-radius:6px; border-left:3px solid {{ $themeColor }};">
                {!! $tf->remarks !!}
            </div>
        </td>
    </tr>
    @endif
</table>

<!-- Thank You Message -->
<p style="font-size:14px; color:#333; margin:0 0 10px 0; font-family:'Inter',Arial,sans-serif; font-weight:500;">
    Thank you for resolving the ticket.
</p>
@endsection