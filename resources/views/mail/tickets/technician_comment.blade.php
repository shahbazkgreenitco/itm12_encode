{{-- @page-meta
{
    "page_no": null,
    "file": "ticket_commented.blade.php",
    "versions": [
        {
        "version": "1.1",
        "writer": "santosh upadhyay",
        "from": "2026-07",
        "reviewer": null,
        "description": "Outlook-compatible rewrite (inline styles, cell-level backgrounds, width attributes)"
        },
        {
        "version": "1.2",
        "writer": "shivam kumar",
        "from": "2026-07",
        "reviewer": null,
        "description": "New modern layout with greeting icon, comment header, details table, comment box, and CTA"
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

    // Defined once, up front — the engineer comment below uses it before the
    // details table. Falls back to a fresh lookup if the Mailable didn't pass it.
    $embedded_attachments = $embedded_attachments ?? App\Models\Ticket\Attachment::getEmbeddedAttachments($ticket->id);

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

    <!-- Header - Ticket Commented -->
    <h1 style="font-size:22px; font-weight:700; color:#1a1a1a; line-height:1.35; margin:0 0 6px 0; font-family:'Inter',Arial,sans-serif;">
        A new comment has been<br>
        added to <span style="color:{{ $themeColor }};">your ticket</span>
    </h1>
    <p style="font-size:13px; color:#666; margin:0 0 4px 0; font-family:'Inter',Arial,sans-serif;">
        Ticket ID: <b>{{ $ticket->ticket_tag ?? '#' . $ticket->id }}</b>
    </p>
    <p style="font-size:13px; color:#666; margin:0 0 4px 0; font-family:'Inter',Arial,sans-serif;">
        Comment from: <b>{{ $technician->getGuranteedNameText(true) }}</b>
    </p>
    <p style="font-size:13px; color:#666; margin:0 0 24px 0; font-family:'Inter',Arial,sans-serif;">
        Please review the comment and take necessary action.
    </p>

    <!-- Comment Content -->
    @if(!empty($comment))
        <div style="text-align:left; font-family:'Inter',Arial,sans-serif; font-size:14px; color:#333333; background:#f8f9fa; padding:16px 20px; border-radius:6px; margin:0 0 24px 0; border-left:4px solid {{ $themeColor }};">
            <p style="margin:0 0 8px 0; font-weight:600; color:#1a1a1a; font-size:13px;">Engineer Comment:</p>
            <div style="font-style:normal;">{!! CommonHelper::renderTktContent($comment, $embedded_attachments, true) !!}</div>
        </div>
    @endif

    <!-- Details Table -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; margin:0 auto 28px; border:1px solid #e6ebf1; border-radius:6px; font-family:'Inter',Arial,sans-serif;">
        
        <!-- Ticket ID -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td width="38%" style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Ticket ID</td>
            <td width="62%" style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->ticket_tag ?? '###'. $ticket->id."###" }}</td>
        </tr>

        @if(!empty($ticket->decodedSubject()))
        <!-- Subject -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Subject</td>
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

        @php
            $processedContent = CommonHelper::renderTktContent($ticket->content, $embedded_attachments);
            $processedContent = str_replace('&nbsp;', ' ', $processedContent);
            $limitedContent = Illuminate\Support\Str::limit(strip_tags($processedContent), 500, '...');
        @endphp

        @if(!empty($limitedContent))
        <!-- Content -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Content</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{!! $limitedContent !!}</td>
        </tr>
        @endif

        @if(!empty(optional($ticket->status)->name))
        <!-- Status -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Status</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">
                <span style="color:#7b2d8e; font-weight:550; background:#f8e6ff; border-radius:23px; padding:5px 10px; display:inline-block;">{{ optional($ticket->status)->name }}</span>
            </td>
        </tr>
        @endif

        @if(!empty(optional($ticket->priority)->name))
        <!-- Priority -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">Priority</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ optional($ticket->priority)->name }}</td>
        </tr>
        @endif

        @if(!empty($ticket->tat))
        <!-- TAT -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">TAT</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $ticket->tat }} Hrs</td>
        </tr>
        @endif

        @if(!empty($formattedTatExpire))
        <!-- TAT Expire -->
        @php $bg = ($rowIndex++ % 2 == 0) ? '#f4f7fb' : '#ffffff'; @endphp
        <tr style="background:{{ $bg }};">
            <td style="{{ $thBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">TAT Expire At</td>
            <td style="{{ $tdBase }} background-color:{{ $bg }}; border-bottom:1px solid #e6ebf1;">{{ $formattedTatExpire }}</td>
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
    </table>

    <!-- CTA Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding-bottom:32px;">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" bgcolor="{{ $themeColor }}" style="border-radius:6px; padding:12px 28px; background:{{ $themeColor }};">
                            <a href="{{ url('ticket/' . $ticket->id) }}" style="color:#ffffff; font-size:14px; font-weight:600; font-family:'Inter',Arial,sans-serif; text-decoration:none; display:inline-block; background:{{ $themeColor }}; border-radius:6px;">
                                View Ticket and Take an Action <span style="font-size:16px;">→</span>
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Back Trails (if enabled) -->
    @if($add_back_trail && count($back_trails))
        @foreach($back_trails as $bt)
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="border-top:1px solid #e8e8e8; font-size:0; line-height:0; padding:0;" height="1">&nbsp;</td>
                </tr>
            </table>
            <p style="text-align:left; font-family:'Inter',Arial,sans-serif; font-size:12px; line-height:18px; mso-line-height-rule:exactly; color:#666666; margin:10px 0 6px 0;">{!! \Illuminate\Support\Str::limit(strip_tags(CommonHelper::renderTktContent($bt->msg)), 500, '...') !!}</p>
            <p style="text-align:left; font-family:'Inter',Arial,sans-serif; font-size:13px; line-height:19px; mso-line-height-rule:exactly; color:#333333; margin:0 0 12px 0;">{!! \Illuminate\Support\Str::limit(strip_tags(CommonHelper::renderTktContent($bt->remarks, $embedded_attachments, true)), 500, '...') !!}</p>
        @endforeach
    @endif

@endsection